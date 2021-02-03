<?php

namespace common\models;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\imagine\Image;
use yii\web\UploadedFile;
use common\components\ChangeLogBehavior;

/**
 * ZModel override the default ActiveRecord \yii\db\ActiveRecord.
 *
 * @inheritdoc
 *
 * @property string $created
 * @property string $updated
 * @property string $createdDate
 * @property string $updatedDate
 * @property string $createdTime
 * @property string $updatedTime
 * @property boolean $isRead
 * @property boolean $isEdit
 */
class ZModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = array_merge(
            parent::fields(),
            $this->extraFields(),
            [
                'isRead', 'isEdit',
                'created', 'updated', 'createdDate', 'updatedDate', 'createdTime', 'updatedTime',
            ]
        );

        return $fields;
    }

    public static function findCreate($condition, $save = true)
    {
        $model = static::findOne($condition);
        if ($model) {
            return $model;
        }

        /* @var ActiveRecord $model */
        $class = static::className();
        $model = new $class;
        if ($model->hasMethod('setAttributes')) {
            $attributes = [];
            if (is_array($condition)) {
                $attributes = $condition;
            } else {
                if ($condition && static::primaryKey()) {
                    $attributes = [static::primaryKey()[0] => $condition];
                }
            }
            $model->setAttributes($attributes);
            if ($save) {
                $model->save();
            }
            return $model;
        }

        return null;
    }

    /**
     * 2017-05-06 12:25:15
     * @return mixed|string
     */
    public function getCreated()
    {
        if (!$this->hasAttribute('created_at') || !$this->created_at) {
            return null;
        }
        $dateFormat = Config::getValue('datetimeFormat');
        return (new \DateTime())->setTimestamp($this->created_at)->format($dateFormat);
    }

    /**
     * 2017-05-06 12:25:15
     * @return mixed|string
     */
    public function getUpdated()
    {
        if (!$this->hasAttribute('updated_at') || !$this->updated_at) {
            return null;
        }
        $dateFormat = Config::getValue('datetimeFormat');
        return (new \DateTime())->setTimestamp($this->updated_at)->format($dateFormat);
    }

    /**
     * 2017-05-06
     * @return mixed|string
     */
    public function getCreatedDate()
    {
        if (!$this->hasAttribute('created_at') || !$this->created_at) {
            return null;
        }
        $dateFormat = Config::getValue('dateFormat');
        return (new \DateTime())->setTimestamp($this->created_at)->format($dateFormat);
    }

    /**
     * 2017-05-06
     * @return mixed|string
     */
    public function getUpdatedDate()
    {
        if (!$this->hasAttribute('updated_at') || !$this->updated_at) {
            return null;
        }
        $dateFormat = Config::getValue('dateFormat');
        return (new \DateTime())->setTimestamp($this->updated_at)->format($dateFormat);
    }

    /**
     * 12:25:15
     * @return mixed|string
     */
    public function getCreatedTime()
    {
        if (!$this->hasAttribute('created_at') || !$this->created_at) {
            return null;
        }
        $dateFormat = Config::getValue('timeFormat');
        return (new \DateTime())->setTimestamp($this->created_at)->format($dateFormat);
    }

    /**
     * 12:25:15
     * @return mixed|string
     */
    public function getUpdatedTime()
    {
        if (!$this->hasAttribute('updated_at') || !$this->updated_at) {
            return null;
        }
        $dateFormat = Config::getValue('timeFormat');
        return (new \DateTime())->setTimestamp($this->updated_at)->format($dateFormat);
    }

    /**
     * @param $date
     * @param string $formatTo
     * @param string $formatFrom
     * @return string
     */
    public static function dateFormat($date, $formatTo = 'd.m.Y', $formatFrom = 'Y-m-d')
    {
        if ($date) {
            $dateTime = \DateTime::createFromFormat($formatFrom, $date);
            if ($dateTime) {
                if(!$formatTo) {
                    return $dateTime->getTimestamp();
                }
                return $dateTime->format($formatTo);
            }
        }
        return $date;
    }

    /**
     * @param $date
     * @param string $formatTo
     * @param string $formatFrom
     * @return string
     */
    public static function dateFormatDB($date, $formatTo = 'Y-m-d', $formatFrom = 'd.m.Y')
    {
        return static::dateFormat($date, $formatTo, $formatFrom);
    }

    /**
     * if current user can read this object
     * @return bool
     */
    public function getIsRead()
    {
        return true;
    }

    /**
     * if current user can edit/add/delete this object
     * @return bool
     */
    public function getIsEdit()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if ($this->hasAttribute('title')) {
            $this->title = Html::decode($this->title);
        }

        if ($this->hasAttribute('title_short')) {
            $this->title_short = Html::decode($this->title_short);
        }

        if ($this->hasAttribute('name')) {
            $this->name = Html::decode($this->name);
        }

        if ($this->hasAttribute('description')) {
            $this->description = Html::decode($this->description);
        }

        if ($this->hasAttribute('description_short')) {
            $this->description_short = Html::decode($this->description_short);
        }

        return parent::afterFind();
    }

    /**
     * @param integer $status
     * @return bool
     */
    public function changeStatus($status)
    {
        if ($this->hasAttribute('status')) {
            $this->status = (int)$status;
            return $this->save();
        }
        return false;
    }

}
