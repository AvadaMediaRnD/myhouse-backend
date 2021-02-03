<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\UserAdmin;

/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */

$this->title = 'Пользователь';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <?php if (Yii::$app->user->id == $model->id 
        || Yii::$app->user->identity->role == UserAdmin::ROLE_ADMIN 
        || (Yii::$app->user->identity->role == UserAdmin::ROLE_MANAGER && $model->role != UserAdmin::ROLE_ADMIN)
    ) { ?>
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать пользователя</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <?php } ?>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-lg-4">
                <table class="table table-bordered table-striped table-view">
                    <tbody>
                        <tr>
                            <td>Пользователь</td>
                            <td><?= $model->fullname ?></td>
                        </tr>
                        <tr>
                            <td>Роль</td>
                            <td><?= $model->getRoleLabel() ?></td>
                        </tr>
                        <tr>
                            <td>Телефон</td>
                            <td><?= $model->phone ?></td>
                        </tr>
                        <tr>
                            <td>Email(логин)</td>
                            <td><?= $model->email ?></td>
                        </tr>
                        <tr>
                            <td>Статус</td>
                            <td><?= $model->getStatusLabelHtml() ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
