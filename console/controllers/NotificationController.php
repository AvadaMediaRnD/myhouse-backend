<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Notification;
use common\models\Message;

/**
 * NotificationController
 */
class NotificationController extends Controller
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        die('Not implemented');
    }
    
    /**
     * Create notifications from messages
     */
    public function actionPrepare()
    {
        $this->logData('Start preparing notifications.');
        
        $messages = Message::find()
            ->where(['status' => Message::STATUS_WAITING])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        
        $this->logData('Processing ' . count($messages) . ' items.');
        
        foreach ($messages as $message) {
            $message->sendToRecipients();
        }
        
        $this->logData('Done.');
    }
    
    /**
     * @param integer $limit
     * @return mixed
     */
    public function actionSend($limit = 0)
    {
        $this->logData('Start push from pending to sent.');
        
        $notificationsQuery = Notification::find()
            ->where(['status' => Notification::STATUS_WAITING])
            ->andWhere(['<', 'send_at', time()])
            ->orderBy(['send_at' => SORT_DESC, 'id' => SORT_DESC]);
        if ($limit) {
            $notificationsQuery->limit($limit);
        }
        $notifications = $notificationsQuery->all();
        
        $this->logData('Processing ' . count($notifications) . ' items.');
        
        foreach ($notifications as $notification) {
            if ($notification->send()) {
                $this->logData("Notification ID=" . $notification->id . " sent.", false);
            } else {
                $this->logData("Notification ID=" . $notification->id . " failed to send.", false);
            }
        } 
        
        $this->logData('Done.');
    }

    /**
     * Log data
     * @param mixed $data
     * @param boolean $withTime
     */
    protected function logData($data, $withTime = true)
    {
        $string = '';
        if (is_array($data) || is_object($data)) {
            $string = print_r($data, true);
        } else {
            $string = $data;
        }
        
        $file = Yii::getAlias('@console/logs/log.txt');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        if (file_exists($file) && filesize($file) > 16*1024*1024) {
            file_put_contents($file, '');
        }
        
        $log = '';
        if ($withTime) {
            $log = "\n[".date('Y-m-d H:i:s')."]: " . Yii::$app->requestedRoute;
        }
        $log .= "\n" . $string;
            
        file_put_contents($file, $log, FILE_APPEND);
    }
}
