<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class Config extends \common\models\ZModel {

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'string'],
            ['key', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'key' => Yii::t('model', 'Ключ'),
            'value' => Yii::t('model', 'Значение'),
        ];
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getValue($key)
    {
        return Yii::$app->params[$key];
    }

    /**
     * @param $key
     * @param $value
     */
    public static function setValue($key, $value)
    {
        Yii::$app->params[$key] = $value;
    }

    /**
     * get custom configs from database and append to Yii::$app->params array
     */
    public static function initConfig()
    {
        $configs = ArrayHelper::map(static::find()->all(), 'key', 'value');
        Yii::$app->params = array_merge(Yii::$app->params, $configs);

        //set page
        if(Yii::$app->request->get('page') && Yii::$app->request->get('page') > 0) {
            Yii::$app->params['page'] = (int)Yii::$app->request->get('page');
        } else {
            Yii::$app->params['page'] = 1;
        }
        Yii::$app->params['limit'] = Yii::$app->params['pageSize'];
        Yii::$app->params['offset'] = (Yii::$app->params['page'] - 1) * Yii::$app->params['limit'];
    }

}