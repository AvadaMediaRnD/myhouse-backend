<?php

namespace backend\models;

use Yii,
    yii\base\Model,
    yii\web\UploadedFile;

/**
 * House form
 */
class Import1cForm extends Model {

    /**
     * @var UploadedFile[]
     */
    public $importFiles;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['importFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xml', 'maxFiles' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'importFiles' => Yii::t('model', 'Файлы для импорта'),
        ];
    }

    public function upload() {
        $folder = Yii::getAlias('@1c') . Yii::$app->params['iport_dir_1c'];

        if ($this->validate()) {
            foreach ($this->importFiles as $file) {
                $file->saveAs($folder . '/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function uploadPayment() {
        $folder = Yii::getAlias('@1c') . Yii::$app->params['iport_pay_dir_1c'];

        if ($this->validate()) {
            foreach ($this->importFiles as $file) {
                $file->saveAs($folder . '/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }

}
