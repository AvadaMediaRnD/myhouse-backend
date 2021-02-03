<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Flat;
use common\models\House;
use common\models\MasterRequest;
use common\models\User;
use common\models\UserAdmin;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */
/* @var $form yii\widgets\ActiveForm */

$user = $model->flat ? $model->flat->user : null;
$userId = $user->id;

$flats = [];
$flatOptions = [];
if ($user) {
    $flats = $user->flats;
} else {
    $flatQuery = Flat::find()->orderBy(['house_id' => SORT_ASC, 'flat' => SORT_ASC]);
    if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
        $flatQuery->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()]);
    }
    $flats = $flatQuery->all();
}
$flatOptions = ArrayHelper::map($flats, 'id', function ($model) {
    return $model->flat . ', ' . $model->house->name;
});

$userQuery = User::find();
if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
    $userQuery->joinWith('flats')
        ->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()]);
}
$userOptions = ArrayHelper::map($userQuery->all(), 'id', 'fullname');

$userMasterOptions = UserAdmin::getUserMasterOptions();
?>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-12 col-md-7 col-lg-6">
            <div class="page-header-spec">
                <?= $form->field($model, 'date_request', ['template' => '{input}'])->widget(DatePicker::className(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
                <span class="label-mid">от</span>
                <?= $form->field($model, 'time_request', [
                    'template' => "
                        <div class=\"input-group bootstrap-timepicker\">{input}\n
                            <div class=\"input-group-addon\">
                            <i class=\"fa fa-clock-o\"></i>
                        </div>
                        </div>"
                ])->textInput() ?>
            </div>
        </div>
    </div>
    
    <div class="box">
        <div class="box-body">
            <div class="row">
                
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="user_id">Владелец квартиры</label>
                            <?php if ($user) { ?>
                                <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $user->id]) ?>" target="_blank"><i class="fa fa-external-link"></i></a>
                            <?php } ?>
                            <?php /* Html::dropDownList(
                                    'user_id', 
                                    $userId, 
                                    $userOptions,
                                    [
                                        'id' => 'user_id',
                                        'prompt' => 'Выберите...',
                                        'class' => 'form-control',
                                        'onchange'=>'
                                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/account/get-lists-by-user', 'user_id' => ''])) . '"+$(this).val(), function( data ) {
                                                $("select#masterrequest-flat_id").html(data.flats);
                                                console.log(data);
                                            });
                                        ',
                                    ]) */ ?>

                            <?php echo Select2::widget([
                                'name' => 'user_id',
                                'value' => $user->id,
                                'data' => $userOptions,
                                'language' => 'ru',
                                'theme' => Select2::THEME_DEFAULT,
                                'options' => [
                                    'placeholder' => 'Выберите...', 
                                    'class' => 'form-control',
                                    'onchange'=>'
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/account/get-lists-by-user', 'user_id' => ''])) . '"+$(this).val(), function( data ) {
                                            $("select#masterrequest-flat_id").html(data.flats);
                                            console.log(data);
                                        });
                                    ',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                        <?php if ($user) { ?>
                            <?php /*
                            <p><b>Владелец:</b> <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $user->id]) ?>"><?= $user->fullname ?></a></p>
                             */ ?>
                            <p><b>Телефон:</b> <a href="tel:<?= str_replace(['(', ')', '-', ' '], '', $user->profile->phone) ?>"><?= $user->profile->phone ?></a></p>
                        <?php } ?>
                    </div>
                    
                
                <div class="col-xs-12 col-md-6">
                    <?php if ($model->flat) { ?>
                        <?php if ($model->flat->house_id) { ?>
                            <p><b>Дом:</b> <a href="<?= Yii::$app->urlManager->createUrl(['/house/view', 'id' => $model->flat->house_id]) ?>"><?= $model->flat->house->name ?></a></p>
                        <?php } ?>
                        <?php if ($model->flat->section) { ?>
                            <p><b>Секция:</b> <?= $model->flat->section->name ?></p>
                        <?php } ?>
                        <?php if ($model->flat->floor) { ?>
                            <p><b>Этаж:</b> <?= $model->flat->floor->name ?></p>
                        <?php } ?>
                    <?php } ?>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'description')->textarea(['rows' => 8]) ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?php /* $form->field($model, 'flat_id')->dropDownList(
                        $flatOptions,
                        ['prompt' => 'Выберите...']
                    ) */ ?>
                    <?php echo $form->field($model, 'flat_id')->widget(Select2::class, [
                        'data' => $flatOptions,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => 'Выберите...', 
                            'class' => 'form-control',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                    
                    <?= $form->field($model, 'type')->dropDownList(
                        MasterRequest::getTypeOptions()
                    ) ?>

                    <?= $form->field($model, 'status')->dropDownList(
                        MasterRequest::getStatusOptions(),
                        ['prompt' => 'Выберите...']
                    ) ?>

                    <?= $form->field($model, 'user_admin_id')->dropDownList(
                        $userMasterOptions,
                        ['prompt' => 'Выберите...']
                    ) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'comment', ['enableClientValidation' => false])->textarea(['rows' => 8, 'class' => 'compose-textarea editor-init form-control']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <div class="form-group">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>" class="btn btn-default">Отменить</a>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php 
$this->registerCssFile(Yii::$app->urlManager->createUrl('plugins/timepicker/bootstrap-timepicker.min.css'));
$this->registerJsFile(Yii::$app->urlManager->createUrl('plugins/timepicker/bootstrap-timepicker.min.js'), ['depends' => yii\web\YiiAsset::class]);
$this->registerJs(<<<JS
    $('#masterrequest-time_request').timepicker({
        showInputs: false,
        showMeridian: false
    });
    
    $(function () {
        //Add text editor
        function addTextEditor() {
            $("textarea.compose-textarea.editor-init").wysihtml5({
                locale: 'ru-RU',
                toolbar: {
                    "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                    "emphasis": true, //Italics, bold, etc. Default true
                    "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                    "html": false, //Button which allows you to edit the generated HTML. Default false
                    "link": false, //Button to insert a link. Default true
                    "image": false, //Button to insert an image. Default true,
                    "color": false, //Button to change color of font
                    "blockquote": false, //Blockquote
                    "fa": true,
                    "size": 'none' //default: none, other options are xs, sm, lg
                }
            }).removeClass('editor-init');
        }
        
        addTextEditor();
    
    });
JS
);
