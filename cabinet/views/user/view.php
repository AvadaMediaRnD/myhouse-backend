<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-3">
        <div class="row">
            <div class="col-md-6 col-lg-12">
                <div class="box">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->profile->getAvatar(), 'w' => 160, 'h' => 160, 'fit' => 'crop']) ?>" alt="Аватар">

                        <h3 class="profile-username text-center"><?= $model->fullname ?></h3>

                        <p class="text-muted text-center">Владелец квартиры</p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <span>Телефон</span>
                                <span class="pull-right"><?= $model->profile->phone ?></span>
                            </li>
                            <li class="list-group-item">
                                <span>Viber</span>
                                <span class="pull-right"><?= $model->profile->viber ?></span>
                            </li>
                            <li class="list-group-item">
                                <span>Telegram</span>
                                <span class="pull-right"><?= $model->profile->telegram ?></span>
                            </li>
                            <li class="list-group-item">
                                <span>Email</span>
                                <span class="pull-right"><?= $model->email ?></span>
                            </li>
                        </ul>

                        <a href="<?= Yii::$app->urlManager->createUrl(['/user/update']) ?>" class="btn btn-primary btn-block">Изменить</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-12">
                <h3 class="page-header">Обо мне (заметки)</h3>
                <div class="box">
                    <div class="box-body box-profile">
                        <?= $model->getUserNoteDescription() ?: 'Нет заметок' ?>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    <div class="col-lg-9">
        <h2 class="page-header">Мои квартиры</h2>
        <?php if ($model->flats) { ?>
            <?php foreach ($model->flats as $flat) { ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Описание квартиры</h3>
                    </div>
                    <div class="box-body">
                        <p><?= $flat->house->name ?>. <?= $flat->house->address ?>, кв. <?= $flat->flat ?></p>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $flat->house->getImagePath1(), 'w' => 522, 'h' => 350, 'fit' => 'crop']) ?>" class="img-responsive largeImg margin-bottom-30" alt="">
                            </div>
                            <div class="col-xs-6 col-md-3">
                                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $flat->house->getImagePath2(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                            </div>
                            <div class="col-xs-6 col-md-3">
                                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $flat->house->getImagePath3(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                            </div>
                            <div class="col-xs-6 col-md-3">
                                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $flat->house->getImagePath4(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                            </div>
                            <div class="col-xs-6 col-md-3">
                                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $flat->house->getImagePath5(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">Описание</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th style="min-width: 200px; font-weight:normal;">Название дома</th>
                                        <th style="font-weight:normal;"><?= $flat->house->name ?></th>
                                    </tr>
                                    <tr>
                                        <td>Адрес</td>
                                        <td><?= $flat->house->address ?></td>
                                    </tr>
                                    <tr>
                                        <td>№ квартиры</td>
                                        <td><?= $flat->flat ?></td>
                                    </tr>
                                    <tr>
                                        <td>Площадь</td>
                                        <td>
                                            <?php if ($flat->square) { ?>
                                                <?= $flat->square ?>м<sup>2</sup>
                                            <?php } else { ?>
                                                не указано
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Этаж</td>
                                        <td><?= $flat->floor->name ?></td>
                                    </tr>
                                    <tr>
                                        <td>Секция</td>
                                        <td><?= $flat->section->name ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицевой счет</td>
                                        <td><?= $flat->account->uid ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Квартиры пока не добавлены</h3>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
