<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Message;
use common\models\MessageAddress;
use common\models\User;
use common\models\House;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$address = null;
if ($model->messageAddress) {
    $address = $model->messageAddress;
} else {
    $messageAddress = new MessageAddress();
    $address = $messageAddress;

    $messageAddressParams = Yii::$app->request->get('MessageAddress');
    if ($messageAddressParams) {
        if ($userHasDebt = (int)$messageAddressParams['user_has_debt']) {
            $address->user_has_debt = $userHasDebt;
        }
        if ($houseId = (int)$messageAddressParams['house_id']) {
            $address->house_id = $houseId;
        }
        if ($userId = (int)$messageAddressParams['user_id']) {
            $address->user_id = $userId;
        }
    }
}

$messageParams = Yii::$app->request->get('Message');
if ($messageParams) {
    if ($type = $messageParams['type']) {
        $model->type = $type;
    }
}

if ($model->isNewRecord) {
    $model->status = Message::STATUS_WAITING;
}
if (!$model->type) {
    $model->type = Message::TYPE_DEFAULT;
}

$userSelectData = ArrayHelper::map(User::find()->all(), 'id', 'fullname');

?>

<?php $form = ActiveForm::begin(); ?>
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
    </div>
    <div class="box-body">
        <?= Html::activeHiddenInput($model, 'user_admin_from_id') ?>
        <?= Html::activeHiddenInput($model, 'status') ?>
        <?= Html::activeHiddenInput($model, 'type') ?>
        
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Тема сообщения:'])->label(false) ?>
        <?= $form->field($model, 'description', ['template' => '{input}'])->textarea([
            'maxlength' => true, 
            'rows' => 6, 
            'placeholder' => 'Текст сообщения:',
            'class' => 'compose-textarea editor-init form-control',
        ])->label(false) ?>

        <div class="row">
            
            <div class="col-xs-12 col-md-6">
                <h4>Кому отправить:</h4>
                
                <?php if ($address->user_id) { ?>
                
                    <?php echo $form->field($address, 'user_id')->widget(Select2::classname(), [
                        'data' => $userSelectData,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => ['placeholder' => 'Выберите...', 'class' => 'form-control'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                
                <?php } else { ?>
                
                    <?= $form->field($address, 'user_has_debt')->checkbox(['class' => ''])
                        //->hint('Если указан Владелец квартир, то этот параметр игнорируется')
                    ?>

                    <?= $form->field($address, 'house_id')->dropDownList(
                        House::getOptions(),
                        [
                            'prompt' => 'Всем...',
                            'onchange'=>'
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'house_id' => ''])) . '"+$(this).val()+"&section_id="+$("select#messageaddress-section_id").val()+"&floor_id="+$("select#messageaddress-floor_id").val()+"&riser_id="+$("select#messageaddress-riser_id").val(), function( data ) {
                                    $("select#messageaddress-section_id").html(data.sections);
                                    $("select#messageaddress-riser_id").html(data.risers);
                                    $("select#messageaddress-floor_id").html(data.floors);
                                    $("select#messageaddress-flat_id").html(data.flats);
                                });
                            ',
                        ]
                    ) ?>

                    <?= $form->field($address, 'section_id')->dropDownList(
                        $address->house ? ArrayHelper::map($address->house->sections, 'id', 'name') : [],
                        [
                            'prompt' => 'Всем...',
                            'onchange'=>'
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'section_id' => ''])) . '"+$(this).val()+"&house_id="+$("select#messageaddress-house_id").val()+"&floor_id="+$("select#messageaddress-floor_id").val()+"&riser_id="+$("select#messageaddress-riser_id").val(), function( data ) {
                                    $("select#messageaddress-flat_id").html(data.flats);
                                });
                            ',
                        ]
                    ) ?>

                    <?php /*
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($address, 'riser_id')->dropDownList(
                                $address->house ? ArrayHelper::map($address->house->risers, 'id', 'name') : [],
                                [
                                    'prompt' => 'Всем...',
                                    'onchange'=>'
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'riser_id' => ''])) . '"+$(this).val()+"&house_id="+$("select#messageaddress-house_id").val()+"&section_id="+$("select#messageaddress-section_id").val()+"&floor_id="+$("select#messageaddress-floor_id").val(), function( data ) {
                                            $("select#messageaddress-flat_id").html(data.flats);
                                        });
                                    ',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($address, 'floor_id')->dropDownList(
                                $address->house ? ArrayHelper::map($address->house->floors, 'id', 'name') : [],
                                [
                                    'prompt' => 'Всем...',
                                    'onchange'=>'
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'floor_id' => ''])) . '"+$(this).val()+"&house_id="+$("select#messageaddress-house_id").val()+"&section_id="+$("select#messageaddress-section_id").val()+"&riser_id="+$("select#messageaddress-riser_id").val(), function( data ) {
                                            $("select#messageaddress-flat_id").html(data.flats);
                                        });
                                    ',
                                ]
                            ) ?>
                        </div>
                    </div>
                    */ ?>
                    <?= $form->field($address, 'floor_id')->dropDownList(
                        $address->house ? ArrayHelper::map($address->house->floors, 'id', 'name') : [],
                        [
                            'prompt' => 'Всем...',
                            'onchange'=>'
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'floor_id' => ''])) . '"+$(this).val()+"&house_id="+$("select#messageaddress-house_id").val()+"&section_id="+$("select#messageaddress-section_id").val()+"&riser_id="+$("select#messageaddress-riser_id").val(), function( data ) {
                                    $("select#messageaddress-flat_id").html(data.flats);
                                });
                            ',
                        ]
                    ) ?>

                    <?= $form->field($address, 'flat_id')->dropDownList(
                        $address->house ? ArrayHelper::map($address->house->flats, 'id', 'flat') : [],
                        ['prompt' => 'Всем...']
                    ) ?>
                
                <?php } ?>
                
            </div>

        </div>
    </div>
    <div class="box-footer">
        <div class="pull-right">
            <button type="submit" class="btn btn-success"><i class="fa fa-envelope-o"></i> Отправить</button>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php $this->registerJs(<<<JS
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


