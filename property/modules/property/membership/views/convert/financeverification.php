<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\finance\models\FmsvoucherSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Finance Verification';
?>


<?=

$this->render('_financeverification', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'myrights' => $myrights,
]);
?>                                    
<?php $this->beginBlock('pagesidebar'); ?>
<?=

$this->render('fincanceversearch', [
    'searchModel' => $searchModel,
    'myrights' => $myrights,
]);
?>
<?php $this->endBlock(); ?>        

