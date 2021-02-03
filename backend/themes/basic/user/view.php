<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Профиль владельца';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Владельцы квартир'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManagerCabinet->createAbsoluteUrl(['/site/index', 'token' => $model->getAuthKey()]) ?>" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-external-link"></i> Перейти в кабинет</a>
            <a href="<?= Yii::$app->urlManager->createUrl(['/user/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать профиль</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="userAvatar">
                    <img class="img-circle img-responsive" src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->profile->getAvatar(), 'w' => 160, 'h' => 160, 'fit' => 'crop']) ?>" alt="<?= $model->fullName ?>">
                </div>
                <div class="table-responsive no-padding">
                    <table class="table table-bordered table-striped table-view">
                        <tbody>
                            <tr>
                                <td>Статус</td>
                                <td><?= $model->getStatusLabelHtml() ?></td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td><?= $model->uid ?></td>
                            </tr>
                            <tr>
                                <td>Фамилия</td>
                                <td><?= $model->profile->lastname ?></td>
                            </tr>
                            <tr>
                                <td>Имя</td>
                                <td><?= $model->profile->firstname ?></td>
                            </tr>
                            <tr>
                                <td>Отчество</td>
                                <td><?= $model->profile->middlename ?></td>
                            </tr>
                            <tr>
                                <td>Дата рождения</td>
                                <td><?= $model->profile->getBirthDate() ?></td>
                            </tr>
                            <tr>
                                <td>О владельце (заметки)</td>
                                <td><?= $model->getUserNoteDescription() ?></td>
                            </tr>
                            <tr>
                                <td>Телефон</td>
                                <td>
                                    <!--<a href="tel:<?= $model->profile->phone ?>">-->
                                        <?= $model->profile->phone ?>
                                    <!--</a>-->
                                </td>
                            </tr>
                            <tr>
                                <td>Viber</td>
                                <td><?= $model->profile->viber ?></td>
                            </tr>
                            <tr>
                                <td>Telegram</td>
                                <td><?= $model->profile->telegram ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>
                                    <!--<a href="mailto:<?= $model->email ?>">-->
                                        <?= $model->email ?>
                                    <!--</a>-->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php if ($model->flats) { ?>
                    <h4>Квартиры</h4>
                    <div class="table-responsive no-padding margin-top-15">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Дом</th>
                                    <th>Секция</th>
                                    <th>Квартира</th>
                                    <th>Лицевой счет</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($model->flats as $k => $flat) { ?>
                                    <tr role="row">
                                        <td><?= $k + 1 ?></td>
                                        <td>
                                            <?php if ($flat->house) { ?>
                                                <a href="<?= Yii::$app->urlManager->createUrl(['/house/view', 'id' => $flat->house_id]) ?>">
                                                    <?= $flat->house->name ?>
                                                </a>
                                            <?php } else { ?>
                                                не указано
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?= $flat->section->name ?>
                                        </td>
                                        <td>
                                            <?php if ($flat) { ?>
                                                <a href="<?= Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $flat->id]) ?>">
                                                    <?= $flat->flat ?>
                                                </a>
                                            <?php } else { ?>
                                                не указано
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($flat->account) { ?>
                                                <a href="<?= Yii::$app->urlManager->createUrl(['/account/view', 'id' => $flat->account->id]) ?>">
                                                    <?= $flat->account->uid ?>
                                                </a>
                                            <?php } else { ?>
                                                не указано
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
