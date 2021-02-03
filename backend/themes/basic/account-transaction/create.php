<?php

use yii\helpers\Html;
use common\models\AccountTransaction;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = Yii::t('app', $model->type == AccountTransaction::TYPE_IN ? 'Новая приходная ведомость' : 'Новая расходная ведомость');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Платежи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
