<?php

namespace app\modules\finance\reports\controllers;

use Yii;
use app\modules\finance\reports\models\MembersreceiptsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use kartik\mpdf\Pdf;

use app\models\Model;

/**
 * VisitdetailsController implements the CRUD actions for Visitdetails model.
 */
class MembersreceiptsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
                ],
            ],
        ];
    }

    
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    
    /**
     * Lists all Membersreceipts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $searchModel = new MembersreceiptsSearch();
        $model = $searchModel->search(Yii::$app->request->queryParams);
        
        if($request->isAjax)
        {
            return $this->renderPartial('_index', [
                'searchModel' => $searchModel,
                'model' => $model,
            ]);
        }
        else
        {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays printable version of summary Visitdetails model.
     * @return mixed
     */
    public function actionPrintsummary()
    {
        $this->layout = "@app/views/layouts/print";

        $request = Yii::$app->request;
        
        $searchModel = new MembersreceiptsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
      //  $dataProvider->pagination=false;
        
        return $this->render('printsummary',[
                'model' => $dataProvider,
        ]);
    }

    /**
     * Displays PDF version of summary Visitdetails model.
     * @return mixed
     */
    public function actionPdfsummary()
    {
        $this->layout = "@app/views/layouts/print";

        $request = Yii::$app->request;
        
        $searchModel = new MembersreceiptsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
  //      $dataProvider->pagination=false;
        
        $pdf = new Pdf([
                'content'=>$this->render('printsummary',[
                                'model' => $dataProvider,
                                ]),
                'filename'=> "report.pdf",
                'mode'=> Pdf::MODE_CORE,
                'format'=> Pdf::FORMAT_A4,
                'destination'=> Pdf::DEST_DOWNLOAD
                ]);
        return $pdf->render();
    }

    /**
     * Displays printable version of summary Visitdetails model.
     * @return mixed
     */
    public function actionChart()
    {
        $this->layout = "@app/views/layouts/chart";

        $request = Yii::$app->request;
        
        $searchModel = new MembersreceiptsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
      //  $dataProvider->pagination=false;
        
        return $this->render('chart',[
                'model' => $dataProvider,
        ]);
    }

    /**
     * Displays sidebar Visitdetails model.
     * @return mixed
     */
    public function actionSidebarsummary()
    {
        $searchModel = new SalescentersSearch();

        return $this->renderPartial('_summarysearch',[
                'searchModel' => $searchModel,
        ]);
    }
}
