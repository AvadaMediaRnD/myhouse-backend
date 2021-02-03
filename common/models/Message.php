<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $invoice_id
 * @property int $user_admin_from_id
 */
class Message extends \common\models\ZModel
{
    const STATUS_SENT = 10;
    const STATUS_WAITING = 5;
    const STATUS_DISABLED = 0;
    const TYPE_DEFAULT = 'default';
    const TYPE_INVOICE = 'invoice';
    const TYPE_HOUSE = 'house';
    const TYPE_PAY = 'pay';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id', 'name', 'description', 'type', 'status', 'created_at', 'updated_at', 'invoice_id', 'user_admin_from_id',
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
                'labelObject' => 'Сообщение',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'type'], 'required'],
            [['id', 'status', 'created_at', 'updated_at', 'invoice_id', 'user_admin_from_id'], 'integer'],
            [['type', 'description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'name' => Yii::t('model', 'Заголовок'),
            'description' => Yii::t('model', 'Текст'),
            'type' => Yii::t('model', 'Тип'),
            'status' => Yii::t('model', 'Статус'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'invoice_id' => Yii::t('model', 'Квитанция'),
            'user_admin_from_id' => Yii::t('model', 'От кого'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddresses()
    {
        return $this->hasMany(MessageAddress::className(), ['message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddress()
    {
        return $this->hasOne(MessageAddress::className(), ['message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessageViews()
    {
        return $this->hasMany(UserMessageView::className(), ['message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdminFrom()
    {
        return $this->hasOne(UserAdmin::className(), ['id' => 'user_admin_from_id']);
    }

    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_WAITING => Yii::t('model', 'В очереди'),
            static::STATUS_SENT => Yii::t('model', 'Отправлен'),
            static::STATUS_DISABLED => Yii::t('model', 'Отключен'),
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
     * @return array
     */
    public static function getTypeOptions()
    {
        return [
            static::TYPE_DEFAULT => Yii::t('model', 'Общий'),
            static::TYPE_INVOICE => Yii::t('model', 'Квитанция'),
            static::TYPE_HOUSE => Yii::t('model', 'ЖК'),
            static::TYPE_PAY => Yii::t('model', 'Оплата'),
        ];
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabel($type = null)
    {
        $type = $type == null ? $this->type : $type;
        $options = static::getTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }

    /**
     * get if user read this message
     * @param null $userId
     * @return bool
     */
    public function getIsUserView($userId = null)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        return UserMessageView::find()->where(['user_id' => $userId, 'message_id' => $this->id])->exists();
    }

    /**
     * set user read this message
     * @param null $userId
     */
    public function setIsUserView($userId = null)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        if (!$this->getIsUserView($userId)) {
            Yii::$app->db->createCommand()->insert(UserMessageView::tableName(), ['user_id' => $userId, 'message_id' => $this->id])->execute();
        }
    }

    /**
     * send message to users
     */
    public function sendToRecipients()
    {
        $users = $this->messageAddress->getUserRecipients();
        if ($users) {
            foreach ($users as $user) {
                // TODO
                $notification = new Notification();
                $notification->message_id = $this->id;
                $notification->send_at = time() + 60;
                $notification->status = Notification::STATUS_WAITING;
                $notification->user_id = $user->id;
                $notification->title = $this->name;
                $notification->message = mb_substr(strip_tags(Html::decode($this->description)), 0, 240);
                $notification->save();
            }
        }
        
        $this->status = static::STATUS_SENT;
        $this->save();

        return true;
    }
}
