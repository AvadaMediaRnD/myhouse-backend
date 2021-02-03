<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Владельцы квартир');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить владельца квартир'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            Yii::t('app', 'Отправить сообщение должникам'),
            [
                '/message/create',
                'MessageAddress[user_has_debt]' => 1,
                'MessageAddress[house_id]' => Yii::$app->request->get('UserSearch')['searchHouse'] ?: null,
            ],
            ['class' => 'btn btn-primary'/*, 'target' => '_blank'*/]
        ) ?>
        <?= Html::a(Yii::t('app', 'Отправить приглашение'), ['invite'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'searchFullname',
                'label' => Yii::t('model', 'ФИО'),
                'value' => function ($model) {
                    return $model->profile->fullname;
                }
            ],
            'username',
            [
                'attribute' => 'searchEmail',
                'label' => Yii::t('model', 'Email'),
                'value' => function ($model) {
                    return $model->profile->email ? $model->profile->email : '';
                }
            ],
            [
                'attribute' => 'searchBirthdate',
                'label' => Yii::t('model', 'Дата рождения'),
                'value' => function ($model) {
                    return $model->profile->birthdate ? $model->profile->birthdate : '';
                }
            ],
            [
                'attribute' => 'searchHouse',
                'label' => Yii::t('model', 'ЖК'),
                'value' => function ($model) {
                    $houses = ArrayHelper::getColumn($model->flats, 'house.name');
                    return implode(',<br/>', $houses);
                },
                'filter' => ArrayHelper::map(House::find()->all(), 'id', 'name'),
                'format' => 'html',
            ],
            [
                'format' => 'html',
                'attribute' => 'searchFlat',
                'value' => function ($model) {
                    $flats = ArrayHelper::getColumn($model->flats, function ($model) {
                        return '№' . $model->flat . ', ' . $model->house->name;
                    });
                    return implode(',<br/>', $flats);
                },
                'label' => Yii::t('model', 'Квартира'),
            ],
            [
                'attribute' => 'searchCreatedDate',
                'label' => Yii::t('model', 'Добавлен'),
                'value' => function ($model) {
                    return $model->createdDate;
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'searchCreatedDate',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]),
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'filter' => $searchModel::getStatusOptions(),
            ],
            [
                'format' => 'html',
                'attribute' => 'searchHasDebt',
                'value' => function ($model) {
                    $flatIds = ArrayHelper::getColumn($model->flats, 'id');
                    $debt = Invoice::find()
                        ->where(['in', 'flat_id', $flatIds])
                        ->andWhere(['status' => Invoice::STATUS_UNPAID])
//                        ->andWhere(['<', 'created_at', strtotime('-1 month')]) // if count only older than period
                        ->sum('price');
                    return $debt ? '<span class="">Да</span>' : '';
                },
                'label' => Yii::t('model', 'Есть долг'),
                'filter' => [1 => 'Да'],
            ],
            // 'status',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{send_message} {update} {delete}',
                'buttons' => [
                    'send_message' => function ($url, $model, $key) {
                        return Html::a('<i class="glyphicon glyphicon-envelope"></i>',
                            [
                                '/message/create',
                                'MessageAddress[user_id]' => $model->id,
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
</div>
