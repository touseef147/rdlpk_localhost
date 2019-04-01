<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel app\models\application\MembersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dealers';


$colnameclass = "";

/*
  if(isset($dataProvider->sort->orders["role_type_name"]))
  {
  if($dataProvider->sort->orders["role_type_name"] ==4)
  {
  $colnameclass="sorting_asc";
  }
  if($dataProvider->sort->orders["role_type_name"] ==3)
  {
  $colnameclass="sorting_desc";
  }
  }
 */
?>
<div class="members-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <?=
    \app\components\Breadcrumb::widget([
        "items" => [
            ["link" => "members/dealers/default/index", "title" => "Dealers Control Panel"],
            ["link" => "", "title" => Html::encode($this->title)],
        ],
    ])
    ?>

    <input type="hidden" id="tpageno" name="tpageno" value="<?= $dataProvider->pagination->page ?>">
    <input type="hidden" id="tpagesize" name="tpagesize" value="<?= $dataProvider->pagination->pageSize ?>">
    <div class="page-content">

        <?= ""//\app\components\Pageheader::widget(["title" => Html::encode($this->title), "subtitle" => ""]) ?>

        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->

                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">
                        <li class="active">
                            <a data-toggle="tab" href="#tab1">
                                <i class="green ace-icon fa fa-home bigger-120"></i>
                                Dealers
                            </a>
                        </li>

                        <li>
                            <a class="tab-pane-content" data-toggle="tab" href="#tab2" data-url="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=members/dealers/instruments/index">
                                <i class="green ace-icon fa fa-th bigger-120"></i>
                                Instruments
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content no-padding">
                        <div id="tab1" class="tab-pane fade in active">
                            <div class="widget-box ui-sortable-handle" id="widget-box-1">
                                <div class="widget-header widget-header-medium">
                                    <h4 class="widget-title"><?= Html::encode($this->title) ?></h4>

                                    <div class="widget-toolbar no-border">
                                        <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=members/dealers/dealers/create" class="btn btn-white btn-round btn-primary ajaxlink">
                                            &nbsp;<i class="ace-icon fa fa-plus"></i>&nbsp;Add&nbsp;
                                        </a>
                                        <a href="#" style="display:none;" class="btn btn-white btn-round btn-primary">
                                            &nbsp;<i class="ace-icon fa fa-print"></i>&nbsp;Print&nbsp;
                                        </a>
                                        <a href="#" style="display:none;" class="btn btn-white btn-round btn-primary">
                                            &nbsp;<i class="ace-icon fa fa-envelope"></i>&nbsp;PDF&nbsp;
                                        </a>
                                        <input type="hidden" id="search_nav_link" value="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=members/dealers/dealers/sidebarsummary">
                                        <input type="hidden" id="tsort" name="tsort" value="">
                                    </div>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main no-padding">
                                        <table id="simple-table" class="table  table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="center" style="width:40px;">      </th>
                                                    <th style="width:80px;"><?php echo $dataProvider->sort->link('image') ?>      </th>
                                                    <th><?php echo $dataProvider->sort->link('name') ?>      </th>
                                                    <th><?php echo $dataProvider->sort->link('sodowo') ?>      </th>
                                                    <th><?php echo $dataProvider->sort->link('dealers_business_title') ?>      </th>

                                                    <th><?php echo $dataProvider->sort->link('cnic') ?>      </th>
                                                    <th><?php echo $dataProvider->sort->link('address') ?>      </th>
                                                    <th><?php echo $dataProvider->sort->link('status') ?>      </th>

                                                    <th style="width:40px;">      </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $rows = $dataProvider->getModels(); ?>
                                                <?php foreach ($rows as $row) { ?>
                                                    <tr>
                                                        <td class="center">      </td>
                                                        <td align="left">
                                                            <img src="<?php echo Yii::$app->urlManager->baseUrl; ?>/uploads/members/pictures/<?php echo $row->image; ?>?<?php echo date('H-i-s'); ?>" height="100">
                                                        </td>
                                                        <td align="left"><a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=members/dealers/dealers/update&id=<?php echo $row->id; ?>" class="ajaxlink"><?php echo $row->name; ?></a></td>

                                                        <td align="left"><?php echo $row->sodowo; ?>      </td>                                     
                                                        <td align="left"><?php echo $row->dealers_business_title; ?>      </td>                                     
                                                        <td align="left"><?php echo $row->cnic; ?>      </td>
                                                        <td align="left"><?php echo $row->address; ?>      </td>
                                                        <td align="left"><?php
                                                            if ($row->status == 1)
                                                                echo'Active';
                                                            else
                                                                echo'Inactive';
                                                            ?>      </td>

                                                        <td class="center">
                                                            <a data-original-title="Edit" href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=members/dealers/dealers/update&id=<?php echo $row->id; ?>" class="tooltip-error ajaxlink" data-rel="tooltip" title="">
                                                                <span class="blue">
                                                                    <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                                </span>
                                                            </a>
                                                            <a data-original-title="Delete" href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=members/dealers/dealers/delete&id=<?php echo $row->id; ?>" class="tooltip-error deletelink" data-rel="tooltip" title="">
                                                                <span class="red">
                                                                    <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                                </span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="widget-toolbox padding-8 clearfix">
                                        <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
                                    </div>        
                                </div>
                            </div>
                        </div>

                        <div id="tab2" class="tab-pane fade in">
                        </div>
                    </div>
                </div>

                <!-- PAGE CONTENT ENDS -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
</div>



<?php $this->beginBlock('pagesidebar'); ?>
<?=
$this->render('_summarysearch', [
    'searchModel' => $searchModel,
    'myrights' => $myrights,
]);
?>
<?php $this->endBlock(); ?>        

