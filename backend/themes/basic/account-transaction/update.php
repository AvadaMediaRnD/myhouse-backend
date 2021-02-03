<?php

use yii\helpers\Html;
use common\models\AccountTransaction;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = Yii::t('app', $model->type == AccountTransaction::TYPE_IN ? 'Приходная ведомость' : 'Расходная ведомость');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Платежи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title . ' №' . $model->uid, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
