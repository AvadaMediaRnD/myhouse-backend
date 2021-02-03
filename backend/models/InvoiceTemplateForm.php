<?php
namespace backend\models;

use common\models\InvoiceTemplate;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * InvoiceTemplate form
 */
class InvoiceTemplateForm extends Model
{
    public $title;
    public $is_default;
    public $file;

    private $_model = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
            [['is_default'], 'safe'],
            [['title'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => ['xls', 'xlsx', 'csv', 'ods'], 'maxSize' => 10*1024*1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('model', 'Название'),
            'is_default' => Yii::t('model', 'По-умолчанию'),
            'file' => Yii::t('model', 'Загрузить пользовательский шаблон'),
        ];
    }

    /**
     * Save template.
     *
     * @return InvoiceTemplate|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $file = UploadedFile::getInstance($this, 'file');
            if (!$file) {
                $this->addError('file');
                return null;
            }
            
            $model = new InvoiceTemplate();
            $model->title = $this->title;
            $model->is_default = $this->is_default;

            if ($model->save()) {
                // file
                if ($file) {
                    $path = '/upload/InvoiceTemplate/tpl-' . $model->id . '.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $model->file = $path;
                        $model->save(false);
                    }
                }
                
                $this->_model = $model;
                
                return $model;
            }
        }
        
        return null;
    }

    /**
     * @param InvoiceTemplate $model
     * @return InvoiceTemplateForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        $form->_model = $model;
        if ($model) {
            $form->title = $model->title;
            $form->is_default = $model->is_default;
        }
        return $form;
    }
}
