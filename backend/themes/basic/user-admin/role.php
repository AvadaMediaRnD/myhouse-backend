<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\UserAdmin;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserAdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
/* @var $roles array */

$this->title = Yii::t('app', 'Роли');
$this->params['breadcrumbs'][] = $this->title;
$auth = Yii::$app->authManager;
?>

<div class="box">
    <?php $form = ActiveForm::begin(); ?>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Роль</th>
                        <th class="text-center">Статистика</th>
                        <th class="text-center">Касса</th>
                        <th class="text-center">Квитанции на оплату</th>
                        <th class="text-center">Лицевые счета</th>
                        <th class="text-center">Квартиры</th>
                        <th class="text-center">Владельцы квартир</th>
                        <th class="text-center">Дома</th>
                        <th class="text-center">Сообщения</th>
                        <th class="text-center">Заявки вызова мастера</th>
                        <th class="text-center">Счетчики</th>
                        <th class="text-center">Управление сайтом</th>
                        <?php /* ?>
                        <th class="text-center">Настройки</th>
                        <?php */ ?>
                        <th class="text-center">Услуги</th>
                        <th class="text-center">Тарифы</th>
                        <th class="text-center">Роли</th>
                        <th class="text-center">Пользователи</th>
                        <th class="text-center">Платежные реквизиты</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role => $roleLabel) { ?>
                        <tr>
                            <td><?= $roleLabel ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_SITE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_SITE))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_ACCOUNT_TRANSACTION."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_ACCOUNT_TRANSACTION))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_INVOICE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_INVOICE))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_ACCOUNT."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_ACCOUNT))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_FLAT."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_FLAT))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_USER."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_USER))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_HOUSE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_HOUSE))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_MESSAGE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_MESSAGE))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_MASTER_REQUEST."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_MASTER_REQUEST))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_COUNTER_DATA."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_COUNTER_DATA))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_WEBSITE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_WEBSITE))) ?></td>
                            <?php /* ?>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_SYSTEM."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_SYSTEM))) ?></td>
                            <?php */ ?>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_SERVICE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_SERVICE))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_TARIFF."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_TARIFF))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_ROLE."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_ROLE))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_USER_ADMIN."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_USER_ADMIN))) ?></td>
                            <td class="text-center"><?= Html::checkbox(UserAdmin::PERMISSION_PAY_COMPANY."[$role]", $auth->hasChild($auth->getRole($role), $auth->getPermission(UserAdmin::PERMISSION_PAY_COMPANY))) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group margin-top-15 margin-right-15">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/role']) ?>" class="btn btn-default">Отменить</a>
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>