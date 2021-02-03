<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?php
            if ($this->title !== null) {
                echo \yii\helpers\Html::encode($this->title);
            } else {
                echo \yii\helpers\Inflector::camel2words(
                    \yii\helpers\Inflector::id2camel($this->context->module->id)
                );
                echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
            } ?>
        </h1>
        
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => '<i class="fa fa-home"></i> Главная', 
                'url' => ['/'],
                'encode' => false,
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    Разработано <a href="https://avada-media.ua/">AVADA-MEDIA</a>. На базе системы управления <a href="https://avada-media.ua/moydom24/">"МойДом24"</a>.
</footer>
