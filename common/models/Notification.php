<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property string $data
 * @property int $send_at
 * @property int $status
 * @property int $user_id
 * @property int $message_id
 *
 * @property User $user
 * @property Message
 */
class Notification extends \common\models\ZModel
{
    const STATUS_WAITING = 5;
    const STATUS_SENT = 10;
    const STATUS_DISABLED = 0;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['send_at', 'status', 'user_id', 'message_id'], 'integer'],
            [['title', 'message'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::className(), 'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'message' => Yii::t('app', 'Message'),
            'data' => Yii::t('app', 'Data'),
            'send_at' => Yii::t('app', 'Send At'),
            'status' => Yii::t('app', 'Status'),
            'user_id' => Yii::t('app', 'User ID'),
            'message_id' => Yii::t('app', 'Message ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }
    
    /**
     * 
     * @return bool
     */
    public function send()
    {
        $user = $this->user;
        
        if ($user && $this->message) {
            $response = $this->sendOneSignal($this->title, $this->message, $user->uid);
            $return['response'] = $response;
            $result = json_encode($return);
        
            $message = 'Message id=' . $this->id . ' sent.';
        } else {
            $result = null;
            
            $message = 'Message id=' . $this->id . ' NOT sent.';
        }
        Yii::getLogger()->log($message, \yii\log\Logger::LEVEL_INFO, 'Notification.send');
        $this->data = $result;
        $this->status = static::STATUS_SENT;
        $this->save();
        
        return true;
    }
    
    /**
     * 
     * @param string $title
     * @param string $message
     * @return string
     */
    protected function sendOneSignal($title, $message, $tag) 
    {
        if (!Yii::$app->params['oneSignal']['apiAppId'] 
            || !Yii::$app->params['oneSignal']['apiUrl']
            || !Yii::$app->params['oneSignal']['apiAuth']) {
            return ['error' => 'API params not set in config'];
        }
        
        $content = [
            'en' => $message,
        ];
        $heading = [
            'en' => $title,
        ];

        $fields = [
            'app_id' => Yii::$app->params['oneSignal']['apiAppId'],
            'filters' => [
                ['field' => 'tag', 'key' => 'user_uid', 'relation' => '=', 'value' => $tag],
            ],
            // 'data' => ['foo' => 'bar'],
            'contents' => $content,
            'headings' => $heading,
        ];

        $fields = json_encode($fields);
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['oneSignal']['apiUrl']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . Yii::$app->params['oneSignal']['apiAuth']
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}
