<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\security\models\User */

$this->title = 'Update Profile';
?>
<div class="user-create">

    <?= $this->render('_updateprofile', [
        'model' => $model,
    ]) ?>

</div>