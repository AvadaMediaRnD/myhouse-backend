<?php
namespace cabinet\models;

use common\models\MasterRequest;
use Yii;
use yii\base\Model;

/**
 * MasterRequest form
 */
class MasterRequestForm extends Model
{
    public $description;
    public $type;
    public $flat_id;
    public $date_request;
    public $time_request;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'flat_id'], 'integer'],
            [['description'], 'string', 'max' => 15000],
            [['description', 'flat_id', 'date_request'], 'required'],
            [['date_request', 'time_request'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description' => Yii::t('model', 'Описание'),
            'type' => Yii::t('model', 'Тип мастера'),
            'flat_id' => Yii::t('model', 'Квартира'),
            'date_request' => Yii::t('model', 'Дата работ'),
            'time_request' => Yii::t('model', 'Время работ'),
            'status' => Yii::t('model', 'Статус'),
        ];
    }

    /**
     * Save MasterRequest
     *
     * @return MasterRequest|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $masterRequest = new MasterRequest();
            $masterRequest->description = $this->description;
            $masterRequest->type = $this->type ?: MasterRequest::TYPE_DEFAULT;
            $masterRequest->flat_id = $this->flat_id;
            $masterRequest->date_request = date('Y-m-d', strtotime($this->date_request));
            $masterRequest->time_request = $this->time_request;
            $masterRequest->status = $this->status;
            if ($masterRequest->save()) {
                return $masterRequest;
            }
        }
        
        return null;
    }

    /**
     * @param MasterRequest $model
     * @return MasterRequestForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        if ($model) {
            $form->description = $model->description;
            $form->type = $model->type;
            $form->flat_id = $model->flat_id;
            $form->date_request = $model->date_request ? date(Yii::$app->params['dateFormat'], strtotime($model->date_request)) : '';
            $form->time_request = $model->time_request;
            $form->status = $model->status;
        }
        return $form;
    }
}
