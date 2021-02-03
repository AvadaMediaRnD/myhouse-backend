<?php
namespace backend\models;

use common\models\Flat;
use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\Riser;
use common\models\User;
use common\models\Account;
use common\models\Tariff;
use Yii;
use yii\base\Model;

/**
 * Flat form
 */
class FlatForm extends Model
{
    public $id;
    public $flat;
    public $square;
    public $account_uid;
    public $house_id;
    public $section_id;
    public $floor_id;
    public $riser_id;
    public $user_id;
    public $tariff_id;

    private $_model = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'house_id', 'section_id', 'floor_id', 'riser_id', 'user_id', 'tariff_id'], 'integer'],
            ['square', 'number'],
            [['flat', 'account_uid'], 'string', 'max' => 255],
            [['house_id', 'flat'], 'required'],
            ['account_uid', 'unique', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_uid' => 'uid'], 
                'filter' => function ($query) { 
                    $query->andWhere(['!=', 'account.uid', $this->_model->account->uid]); 
                    $query->andWhere(['is not', 'account.flat_id', null]);
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'flat' => Yii::t('model', 'Номер квартиры'),
            'square' => Yii::t('model', 'Площадь (кв.м.)'),
            'account_uid' => Yii::t('model', 'Лицевой счет'),
            'house_id' => Yii::t('model', 'Дом'),
            'section_id' => Yii::t('model', 'Секция'),
            'floor_id' => Yii::t('model', 'Этаж'),
            'riser_id' => Yii::t('model', 'Стояк'),
            'user_id' => Yii::t('model', 'Владелец'),
            'tariff_id' => Yii::t('model', 'Тариф'),
        ];
    }

    /**
     * Save Flat.
     *
     * @return Flat|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $model = Flat::findOne($this->id);
            if (!$model) {
                $model = new Flat();
            }
            $model->flat = $this->flat;
            $model->square = $this->square ? $this->square : 0;
            $model->house_id = $this->house_id;
            $model->section_id = $this->section_id;
            $model->floor_id = $this->floor_id;
            $model->riser_id = $this->riser_id;
            $model->user_id = $this->user_id;
            $model->tariff_id = $this->tariff_id;

            if ($model->save()) {
                if ($this->account_uid) {
                    $account = Account::find()->where(['uid' => $this->account_uid])->one();
                    if (!$account) {
                        Account::updateAll(['flat_id' => null], ['flat_id' => $model->id]);
                        $account = new Account();
                        $account->uid = $this->account_uid;
                        $account->status = Account::STATUS_ACTIVE;
                        $account->flat_id = $model->id;
                        $account->save();
                    } elseif ($account->flat_id == null) {
                        Account::updateAll(['flat_id' => null], ['flat_id' => $model->id]);
                        $account->flat_id = $model->id;
                        $account->save();
                    }
                }
                
                $this->_model = $model;
                
                return $model;
            }
        }
        
        return null;
    }

    /**
     * @param Flat $model
     * @return FlatForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        $form->_model = $model;
        if ($model) {
            $form->id = $model->id;
            $form->flat = $model->flat;
            $form->square = $model->square;
            $form->account_uid = $model->account ? $model->account->uid : null;
            $form->house_id = $model->house_id;
            $form->section_id = $model->section_id;
            $form->floor_id = $model->floor_id;
            $form->riser_id = $model->riser_id;
            $form->user_id = $model->user_id;
            $form->tariff_id = $model->tariff_id;
        }
        return $form;
    }
}
