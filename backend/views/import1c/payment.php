<?php

use yii\helpers\Html,
    yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FlatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '1С Импорт оплат');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="1cimport-index">

    <div class="box-group">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'importFiles[]')->fileInput(['multiple' => true]) ?>

        <button><?= Yii::t('app', 'Загрузить') ?></button>

        <?php ActiveForm::end() ?>
    </div>


    <div class="box-group-green">
        <h3><?= Yii::t('app', 'Список файлов в папке для импорта оплат из 1С') ?>:</h3>
        <p>
            <?php
            if (isset($list) && count($list) > 2) {
                foreach ($list as $item) :
                    if ($item !== '.' && $item !== '..') {
                        ?>
                        <a href="<?= Yii::$app->params['iport_invoice_dir_1c'] . '/' . $item ?>" download><u><?= $item ?></u></a> <br/>
                        <?php
                    } endforeach;
            } else {
                ?>
                <span class="warning"><?= Yii::t('app', 'Внимание! Нет файлов для импорта.') ?></span><br/>
                <?= Yii::t('app', 'Загрузите файлы xml через форму выше или через FTP') ?>
            <?php }
            ?>
        </p>
    </div>

    <?= Html::a(Yii::t('app', 'Запустить импорт'), 'javascript:;', ['id' => 'import_start', 'class' => 'btn btn-success']) ?>

</div>

<?php
$script = <<< JS
    $(document).ready(function(){
        $("a#import_start").on('click',function(e){
            e.preventDefault();
            
            $.ajax({
                type: 'POST',
                url: '/admin/import1c/payments',
                dataType: 'json',
                data: {run: 1},
                success: function (response) {}
            });
        });
        
    });
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
