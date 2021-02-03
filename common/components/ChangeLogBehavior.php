<?php

namespace common\components;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use common\models\UserAdminLog;
use common\models\UserAdmin;

class ChangeLogBehavior extends Behavior
{
    public $ignoreAttributes = ['updated_at'];
    public $messageAfterInsert = 'Объект {object} #{uid|id} добавлен пользователем {fullname} <{username}>.';
    public $messageAfterUpdate = 'Объект {object} #{uid|id} изменен пользователем {fullname} <{username}>.';
    public $messageAfterDelete = 'Объект {object} #{uid|id} удален пользователем {fullname} <{username}>.';
    public $labelObject = null;
    private $oldAttributes = [];
    
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }
    
    public function beforeUpdate($event)
    {
        $this->oldAttributes = $this->owner->oldAttributes;
    }
    
    public function beforeDelete($event)
    {
        $this->oldAttributes = $this->owner->oldAttributes;
    }

    public function afterUpdate($event)
    {
        $this->createLog($this->owner, $event);
    }
    
    public function afterInsert($event)
    {
        $this->createLog($this->owner, $event);
    }
    
    public function afterDelete($event)
    {
        $this->createLog($this->owner, $event);
    }
    
    /**
     * 
     * @param ActiveRecord $object
     * @param \yii\db\AfterSaveEvent $event
     */
    protected function createLog($object, $event)
    {
        if (Yii::$app instanceof \yii\console\Application) {
            return true;
        }
        
        $user = Yii::$app->user->identity;
        if (!$user || !($user instanceof UserAdmin)) {
            return true;
        }
        
        $logEvent = '';
        $messageMask = '';
        if ($event->name == ActiveRecord::EVENT_AFTER_INSERT) {
            $logEvent = UserAdminLog::LOG_INSERT;
            $messageMask = $this->messageAfterInsert;
        } elseif ($event->name == ActiveRecord::EVENT_AFTER_UPDATE) {
            $logEvent = UserAdminLog::LOG_UPDATE;
            $messageMask = $this->messageAfterUpdate;
        } elseif ($event->name == ActiveRecord::EVENT_AFTER_DELETE) {
            $logEvent = UserAdminLog::LOG_DELETE;
            $messageMask = $this->messageAfterDelete;
        }
        
        $oldAttributes = [];
        $newAttributes = [];
        if ($event->name == ActiveRecord::EVENT_AFTER_UPDATE) {
            $changedAttributes = array_diff($object->attributes, $this->oldAttributes);

            $oldAttributes = array_intersect_key(
                $this->oldAttributes, 
                $changedAttributes
            );
            $newAttributes = array_intersect_key(
                $object->attributes, 
                $changedAttributes
            );

            foreach ($this->ignoreAttributes as $attribute) {
                unset($oldAttributes[$attribute]);
                unset($newAttributes[$attribute]);
            } 
        } elseif ($event->name == ActiveRecord::EVENT_AFTER_DELETE) {
            $oldAttributes = $this->oldAttributes;
        }
        
        $objectClass = (new \ReflectionClass($object))->getShortName();
        
        $message = str_replace([
                '{object}', 
                '{id}', 
                '{uid}',
                '{uid|id}',
                '{username}',
                '{fullname}',
                '{oldAttributes}',
                '{newAttributes}'
            ], [
                $this->labelObject ?: $objectClass,
                $object->hasAttribute('id') ? $object->id : '[не указан]',
                $object->hasAttribute('uid') ? $object->id : '[не указан]',
                $object->hasAttribute('uid') ? $object->uid : ($object->hasAttribute('id') ? $object->id : '[не указан]'),
                $user ? ($user->hasAttribute('username') ? $user->username : '[не указан]') : '[не указан]',
                $user ? $user->fullname : '[не указан]',
                $oldAttributes ? json_encode($oldAttributes) : null,
                $newAttributes ? json_encode($newAttributes) : null
            ], $messageMask);
        
        $userAdminLog = new UserAdminLog();
        $userAdminLog->event = $logEvent;
        $userAdminLog->message = $message;
        $userAdminLog->object_class = $objectClass;
        $userAdminLog->object_id = $object->id;
        $userAdminLog->old_attributes = $oldAttributes ? json_encode($oldAttributes) : null;
        $userAdminLog->user_admin_id = $user->id;
        if ($event->name != ActiveRecord::EVENT_AFTER_UPDATE || ($event->name == ActiveRecord::EVENT_AFTER_UPDATE && $userAdminLog->old_attributes)) {
            $userAdminLog->save();
        }
    }
}