<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Invoice;
use common\helpers\PriceHelper;
use common\models\TariffService;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm backend\models\InvoiceTemplateForm */
/* @var $models common\models\InvoiceTemplate[] */

$this->title = Yii::t('app', 'Настройка шаблонов');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Список шаблонов</h3>
    </div>
    <div class="box-body">
        <?php if ($models) { ?>
            <?php foreach ($models as $model) { ?>
                <div class="template">
                    <p><?= $model->title ?> <?php if ($model->is_default) { ?><span class="text-bold">(по-умолчанию)</span><?php } ?></p>
                    <a href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl($model->file) ?>"><i class="fa fa-download" aria-hidden="true"></i> Скачать шаблон</a>
                    <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/template', 'default_id' => $model->id]) ?>" class="text-success">&bull; Назначить шаблоном по умоланию</a>
                    <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/template', 'delete_id' => $model->id]) ?>" class="text-danger" data-confirm="Вы уверены, что хотите удалить этот элемент?"><i class="fa fa-trash" aria-hidden="true"></i> Удалить шаблон</a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>Нет загруженных шаблонов</p>
        <?php } ?>
        <?php $form = ActiveForm::begin() ?>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($modelForm, 'file', ['enableClientValidation' => false])->fileInput(['required' => true]) ?>
                    <?= $form->field($modelForm, 'title')->textInput() ?>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/template']) ?>" class="btn btn-default">Отменить</a>
                        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
