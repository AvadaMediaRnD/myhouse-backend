<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\House */

$this->title = $model->name ?: ('Дом ' . $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Дома'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/house/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать дом</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-lg-4">
                <table class="table table-bordered table-striped table-view">
                    <tbody>
                        <tr>
                            <td>Название</td>
                            <td><?= $model->name ?></td>
                        </tr>
                        <tr>
                            <td>Адрес</td>
                            <td><?= $model->address ?></td>
                        </tr>
                        <tr>
                            <td>Секций</td>
                            <td><?= $model->getSections()->count() ?></td>
                        </tr>
                        <tr>
                            <td>Этажей</td>
                            <td><?= $model->getFloors()->count() ?></td>
                        </tr>
                        <?php /* ?><tr>
                            <td>Стояков</td>
                            <td><?= $model->getRisers()->count() ?></td>
                        </tr><?php */ ?>
                        <tr>
                            <td>Пользователи</td>
                            <td>
                                <?php if ($model->userAdmins) { ?>
                                    <?php foreach ($model->userAdmins as $userAdmin) { ?>
                                        <p class="no-margin"><strong><?= $userAdmin->getRoleLabel() ?>:</strong> 
                                            <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/view', 'id' => $userAdmin->id]) ?>"><?= $userAdmin->fullname ?></a>
                                        </p>
                                    <?php } ?>
                                <?php } else { ?>
                                    <p class="no-margin">Не назначены</p>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12 col-lg-8">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath1(), 'w' => 522, 'h' => 350, 'fit' => 'crop']) ?>" class="img-responsive largeImg margin-bottom-30" alt="">
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath2(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath3(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath4(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath5(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
