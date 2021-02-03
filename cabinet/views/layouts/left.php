<?php

$user = Yii::$app->user->identity;
$flats = $user->flats;

$itemsSite = [];
$itemsInvoice = [];
$itemsTariff = [];

if ($flats) {
    foreach ($flats as $flat) {
        $itemsSite[] = ['label' => $flat->house->name . ', кв.' . $flat->flat, 'icon' => 'building-o', 'url' => ['/site/index', 'flat_id' => $flat->id], 'active' => (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index' && Yii::$app->request->get('flat_id') == $flat->id)];
        $itemsInvoice[] = ['label' => $flat->house->name . ', кв.' . $flat->flat, 'icon' => 'file-text-o', 'url' => ['/invoice/index', 'InvoiceSearch[flat_id]' => $flat->id], 'active' => (Yii::$app->controller->id == 'invoice' && Yii::$app->controller->action->id == 'index' && Yii::$app->request->get('InvoiceSearch')['flat_id'] == $flat->id)];
        $itemsTariff[] = ['label' => $flat->house->name . ', кв.' . $flat->flat, 'icon' => 'file-text-o', 'url' => ['/tariff/index', 'flat_id' => $flat->id], 'active' => (Yii::$app->controller->id == 'tariff' && Yii::$app->controller->action->id == 'index' && Yii::$app->request->get('flat_id') == $flat->id)];
    }
    array_unshift(
        $itemsInvoice,
        ['label' => 'Все квитанции', 'icon' => 'files-o', 'url' => ['/invoice/index'], 'active' => (Yii::$app->controller->id == 'invoice' && Yii::$app->controller->action->id == 'index' && Yii::$app->request->get('InvoiceSearch')['flat_id'] == null)]
    );
}

$noFlatsMessage = 'Раздел станет доступным после добавления квартиры.';
?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar -->
    <section class="sidebar">
        
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [
                    [
                        'label' => Yii::t('app', 'Сводка'), 
                        'icon' => 'line-chart', 
                        'url' => '#!', 
                        'active' => Yii::$app->controller->id == 'site',
                        'items' => $itemsSite,
                        'options' => $flats ? [] : ['class' => 'disabled', 'onclick' => 'alert("'.$noFlatsMessage.'"); return false;'],
                    ],
                    [
                        'label' => Yii::t('app', 'Квитанции'), 
                        'icon' => 'files-o', 
                        'url' => '#!', 
                        'active' => Yii::$app->controller->id == 'invoice',
                        'items' => $itemsInvoice,
                        'options' => $flats ? [] : ['class' => 'disabled', 'onclick' => 'alert("'.$noFlatsMessage.'"); return false;'],
                    ],
                    [
                        'label' => Yii::t('app', 'Тарифы'), 
                        'icon' => 'money', 
                        'url' => ['/tariff/index'], 
                        'active' => Yii::$app->controller->id == 'tariff',
                        'items' => $itemsTariff,
                        'options' => $flats ? [] : ['class' => 'disabled', 'onclick' => 'alert("'.$noFlatsMessage.'"); return false;'],
                    ],
                    [
                        'label' => Yii::t('app', 'Сообщения'), 
                        'icon' => 'envelope-o', 
                        'url' => ['/message/index'], 
                        'active' => Yii::$app->controller->id == 'message',
                        'options' => $flats ? [] : ['class' => 'disabled', 'onclick' => 'alert("'.$noFlatsMessage.'"); return false;'],
                    ],
                    [
                        'label' => Yii::t('app', 'Вызов мастера'), 
                        'icon' => 'wrench', 
                        'url' => ['/master-request/index'], 
                        'active' => Yii::$app->controller->id == 'master-request',
                        'options' => $flats ? [] : ['class' => 'disabled', 'onclick' => 'alert("'.$noFlatsMessage.'"); return false;'],
                    ],
                    ['label' => Yii::t('app', 'Профиль'), 'icon' => 'user-circle-o', 'url' => ['/user/view'], 'active' => Yii::$app->controller->id == 'user'],
                    
                ],
            ]
        ) ?>
        
    </section>
    <!-- /.sidebar -->
</aside>

