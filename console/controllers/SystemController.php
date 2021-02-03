<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\UserAdminLog;

/**
 * SystemController
 */
class SystemController extends Controller
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
    public function actionClearExport()
    {
        $this->logData('Start deleting export files.');
        
        $dir = Yii::getAlias('@frontend/web').'/upload/Account';
        $cdir = scandir($dir); 
        $result = [];
        foreach ($cdir as $key => $value) { 
            if (!in_array($value, array(".",".."))) { 
                if (is_file($dir . DIRECTORY_SEPARATOR . $value)) { 
                    $result[] = $value; 
                }
            } 
        } 
        $log = '';
        foreach ($result as $file) {
            if (strpos($file, date('Ymd')) === false) {
                unlink($dir . '/' . $file);
                echo $dir . '/' . $file . "\n";
                $log .= 'Deleting ' . $file . "\n";
            }
        }
        
        $this->logData($log, false);
        
        $this->logData('Done.');
    }
    
    /**
     * Create notifications from messages
     * @param integer $limit
     * @param integer $seconds 365d
     */
    public function actionClearOldLogs($limit = 1000, $seconds = 31536000)
    {
        $this->logData('Start clear old admin logs.');
        
        $ts = time() - $seconds;
        $query = UserAdminLog::find()
            ->where(['<', 'created_at', $ts])
            ->limit($limit);
        $logs = $query->asArray()->all();
        $ids = \yii\helpers\ArrayHelper::getColumn($logs, 'id');
        UserAdminLog::deleteAll(['in', 'id', $ids]);
        
        $this->logData('Cleared ' . count($ids) . ' records.', false);
        
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
