<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "master_request".
 *
 * @property int $id
 * @property int $type
 * @property string $description
 * @property string $date_request
 * @property string $time_request
 * @property string $comment
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $flat_id
 * 
 * @property UserAdmin $userAdmin
 */
class MasterRequest extends \common\models\ZModel
{
    const STATUS_NEW = 1;
    const STATUS_PROCESSING = 5;
    const STATUS_DONE = 10;
    const TYPE_PLUMBER = 1;
    const TYPE_ELECTRIC = 2;
    const TYPE_SLESAR = 3; 
    const TYPE_DEFAULT = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'master_request';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id', 'type', 'description', 'date_request', 'time_request', 'status', 'created_at', 'updated_at', 'flat_id', 'user_admin_id',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Вызов мастера',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'created_at', 'updated_at', 'flat_id', 'user_admin_id'], 'integer'],
            [['description', 'flat_id', 'status'], 'required'],
            [['description', 'comment'], 'string'],
            [['date_request', 'time_request'], 'string', 'max' => 255],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['user_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAdmin::className(), 'targetAttribute' => ['user_admin_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'type' => Yii::t('model', 'Тип мастера'),
            'description' => Yii::t('model', 'Описание'),
            'comment' => Yii::t('model', 'Комментарий'),
            'date_request' => Yii::t('model', 'Дата заявки'),
            'time_request' => Yii::t('model', 'Время заявки'),
            'status' => Yii::t('model', 'Статус'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'flat_id' => Yii::t('model', 'Квартира'),
            'user_admin_id' => Yii::t('model', 'Мастер'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdmin()
    {
        return $this->hasOne(UserAdmin::className(), ['id' => 'user_admin_id']);
    }
    
    public function getDatetimeRequest()
    {
        if (!$this->date_request) {
            return $this->time_request;
        }
        $datetime = $this->date_request;
        if ($this->time_request) {
            $datetime .= (' ' . $this->time_request);
            $date = $datetime ? date(Yii::$app->params['datetimeFormat'], strtotime($datetime)) : '';
        } else {
            $date = $datetime ? date(Yii::$app->params['dateFormat'], strtotime($datetime)) : '';
        }
        return $date;
    }

    /**
     * @param integer $length
     * @return string
     */
    public function getDescriptionShort($length = 20)
    {
        $substring = mb_substr($this->description, 0, $length, 'utf-8');
        if (mb_strlen($substring, 'utf-8') != mb_strlen($this->description, 'utf-8')) {
            $substring .= '...';
        }
        return $substring;
    }

    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_NEW => Yii::t('model', 'Новое'),
            static::STATUS_PROCESSING => Yii::t('model', 'В работе'),
            static::STATUS_DONE => Yii::t('model', 'Выполнено'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'label-default';
        if ($status == static::STATUS_NEW) {
            $itemClass = 'label-primary';
        } elseif ($status == static::STATUS_PROCESSING) {
            $itemClass = 'label-warning';
        } elseif ($status == static::STATUS_DONE) {
            $itemClass = 'label-success';
        }
        return '<small class="label '.$itemClass.'">'.$statusLabel.'</small>';
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        $options = static::getTypeOptions();
        return [
            [
                'id' => static::TYPE_PLUMBER,
                'type' => $options[static::TYPE_PLUMBER],
            ],
            [
                'id' => static::TYPE_ELECTRIC,
                'type' => $options[static::TYPE_ELECTRIC],
            ],
            [
                'id' => static::TYPE_SLESAR,
                'type' => $options[static::TYPE_SLESAR],
            ],
            [
                'id' => static::TYPE_DEFAULT,
                'type' => $options[static::TYPE_DEFAULT],
            ],
        ];
    }
    
    /**
     * @return array
     */
    public static function getTypeOptions()
    {
        return [
            static::TYPE_PLUMBER => 'Сантехник',
            static::TYPE_ELECTRIC => 'Электрик',
            static::TYPE_SLESAR => 'Слесарь',
            static::TYPE_DEFAULT => 'Любой специалист',
        ];
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getTypeLabel($type = null)
    {
        $type = $type == null ? $this->type : $type;
        $options = static::getTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }
}
