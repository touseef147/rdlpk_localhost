<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel app\models\application\DailyvisitorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users Report';


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
<div class="dailyvisitors-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a class="ajaxlink" href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=dashboard">Home</a>
            </li>
            <li>
                <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=visits" class="ajaxlink">Visits</a>
            </li>
            <li class="active"><?= Html::encode($this->title) ?></li>
        </ul><!-- /.breadcrumb -->
    </div>

    <input type="hidden" id="tpageno" name="tpageno" value="1">
    <input type="hidden" id="tpagesize" name="tpagesize" value="100000">
    <div class="page-content">

        <?= \app\components\Pageheader::widget(["title" => Html::encode($this->title), "subtitle" => ""]) ?>

        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->

                <div class="widget-box ui-sortable-handle" id="widget-box-1">
                    <div class="widget-header widget-header-large">
                        <h4 class="widget-title"><?= Html::encode($this->title) ?></h4>

                        <div class="widget-toolbar">
                            <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=visits/visitorsreports/users/printsummary" class="reportlink orange2">
                                <img src="<?php echo Yii::$app->urlManager->baseUrl; ?>/images/print_printer.png">
                            </a>
                            &nbsp;|&nbsp;
                            <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=visits/visitorsreports/users/pdfsummary" class="reportlink orange2">
                                <img src="<?php echo Yii::$app->urlManager->baseUrl; ?>/images/pdf.png">
                            </a>
                            &nbsp;|&nbsp;
                            <a href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=visits/visitorsreports/users/chart" class="reportlink orange2">
                                <img src="<?php echo Yii::$app->urlManager->baseUrl; ?>/images/gnumeric.png">
                            </a>
                            <input type="hidden" id="search_nav_link" value="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=visits/reports/sidebarsummary">
                            <input type="hidden" id="tsort" name="tsort" value="">
                        </div>
                    </div>

                    <div class="widget-body">
                        <div class="widget-main no-padding">
                            <table id="simple-table" class="table  table-bordered table-hover table-report">
                                <thead>
                                    <tr>
                                        <th class="center" style="width:20px;">#</th>
                                        <th class="detail-col  <?php echo $colnameclass; ?>">User Name<?php //echo $dataProvider->sort->link('name')   ?>      </th>
                                        <th class="detail-col  <?php echo $colnameclass; ?>">Sales Center<?php //echo $dataProvider->sort->link('profession_id')   ?>      </th>
                                        <th  style="width:40px;" class="detail-col  <?php echo $colnameclass; ?>">No of Visits<?php //echo $dataProvider->sort->link('city')   ?>      </th>
                                        <th style="width:40px;" class="detail-col  <?php echo $colnameclass; ?>">No of Calls<?php //echo $dataProvider->sort->link('contactno')   ?>      </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php //$rows = $dataProvider->getModels();  ?>
                                    <?php 
                                    $recs=0;
                                    $noofvisits=0;
                                    $noofcalls=0;
                                    
                                    foreach ($model as $row) { 
                                        
                                        $recs++;
                                        $noofvisits+=$row->no_of_visits;
                                        $noofcalls+=$row->no_of_calls;
                                        
                                        
                                        ?>
                                        <tr>
                                            <td class="center"><?php echo $recs; ?></td>
                                            <td align="left"><?php echo $row->username; ?></td>
                                            <td><?php echo $row->center_name; ?>      </td>
                                            <td align="right"><?php echo $row->no_of_visits; //echo ($row->visitorCity == null ? "" : $row->visitorCity->city);   ?>      </td>
                                            <td align="right"><?php echo $row->no_of_calls; ?>      </td>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                        <tr class="widget-toolbox">
                                            <td class="center">      </td>
                                            <td align="left"><strong>TOTAL</strong></td>
                                            <td><?php echo $recs; ?> Records.      </td>
                                            <td align="right"><?php echo $noofvisits; //echo ($row->visitorCity == null ? "" : $row->visitorCity->city);   ?>      </td>
                                            <td align="right"><?php echo $noofcalls; ?>      </td>
                                            </td>
                                        </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- PAGE CONTENT ENDS -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>

</form>


</div>
