<?php
namespace api\modules\v1\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * ZModel base model for api
 */
class ZModel extends Model
{
    protected $_user;

    /**
     * @return string
     */
    public static function getShortClassName()
    {
        return (new \ReflectionClass(static::className()))->getShortName();
    }

    /**
     * @param $data
     * @param null $formName
     * @return bool
     */
    public function loadData($data, $formName = null)
    {
        $shortName = static::getShortClassName();
        return $this->load([$shortName => $data], $formName);
    }

    /**
     * @return null|User|\yii\web\IdentityInterface
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }
}
