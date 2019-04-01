<?php

namespace app\modules\members\dealers\controllers;

use Yii;
use app\models\application\Fmstransdetaildist;
use app\models\application\FmstransdetaildistSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use app\models\Model;

/**
 * InstrumentsController implements the CRUD actions for Fmstransdetaildist model.
 */
class InstrumentsController extends Controller {

    public function behaviors() {
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
        if (Yii::$app->user->id == null) {
            $this->redirect("index.php");
        } else {
            return parent::beforeAction($action);
        }
    }

    /**
     * Lists all Fmstransdetaildist models.
     * @return mixed
     */
    public function actionIndex() {

        $request = Yii::$app->request;
        $myrights = \app\models\Model::getRights("index", "instruments", "members/dealers");

        $searchModel = new \app\models\application\FmstransdetaildistSearch();
        $dataProvider = NULL;

        if (count(Yii::$app->request->queryParams) == 1) {
            $res = \app\models\Model::checksavedinfo(Yii::$app->request->queryParams);

            if ($res == NULL) {
                $dataProvider = $searchModel->searchinstruments(Yii::$app->request->queryParams);

                $dataProvider->pagination->pageSize = $request->get("pagesize", 20);
                $dataProvider->pagination->page = $request->get("pageno", 0);
            } else {
                $dataProvider = $searchModel->searchinstruments($res);

                $dataProvider->pagination->pageSize = (isset($res["pagesize"]) ? $res["pagesize"] : 20); //$request->get("pagesize", 20);
                $dataProvider->pagination->page = (isset($res["pageno"]) ? $res["pageno"] : 0); //$request->get("pagesize", 20);
            }
        } else {
            \app\models\Model::savebrowsinginfo();

            $dataProvider = $searchModel->searchinstruments(Yii::$app->request->queryParams);

            $dataProvider->pagination->pageSize = $request->get("pagesize", 20);
            $dataProvider->pagination->page = $request->get("pageno", 0);
        }

        if ($request->isAjax) {
            return $this->renderPartial('_index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'myrights' => $myrights,
            ]);
        } else {
            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'myrights' => $myrights,
            ]);
        }
    }

    /**
     * Displays printable version of summary Fmstransdetaildist model.
     * @return mixed
     */
    public function actionPrintsummary() {
        $this->layout = "@app/views/layouts/print";

        $request = Yii::$app->request;

        $searchModel = new FmstransdetaildistSearch();
        $dataProvider = $searchModel->searchinstruments(Yii::$app->request->queryParams);

        return $this->render('printsummary', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays PDF version of summary Fmstransdetaildist model.
     * @return mixed
     */
    public function actionPdfsummary() {
        $this->layout = "@app/views/layouts/print";

        $request = Yii::$app->request;

        $searchModel = new FmstransdetaildistSearch();
        $dataProvider = $searchModel->searchinstruments(Yii::$app->request->queryParams);

        $pdf = new Pdf([
            'content' => $this->render('printsummary', [
                'dataProvider' => $dataProvider,
            ]),
            'filename' => "report.pdf",
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'destination' => Pdf::DEST_DOWNLOAD
        ]);
        return $pdf->render();
    }

    /**
     * Displays a single Fmstransdetaildist model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {

        $request = Yii::$app->request;

        if ($request->isAjax) {
            return $this->renderPartial('_view', [
                        'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                        'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Fmstransdetaildist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        //      $model = new Fmstransdetaildist();
        $modeldetail = new \app\models\application\fmstransdetaildist();
        $model = new \app\models\application\Fmstransmaster();
        $modelmember = new \app\models\application\Members();

        if ($model->load(Yii::$app->request->post())) {
            $modeldetail = Model::createMultiple(\app\models\application\fmstransdetaildist::classname());
            Model::loadMultiple($modeldetail, Yii::$app->request->post());

            $data = Yii::$app->request->post();

            for ($i = 0; $i < count($modeldetail); $i++) {
                if ($modeldetail[$i]->cr_amount > 0) {
                    $modeldetail[$i]->dr_amount += floatval($modeldetail[$i]->cr_amount);
                    $modeldetail[$i]->cr_amount =0;
                }
            }

            if (isset($data["Members"])) {
                $modelmember->id = $data["Members"]["id"];
            }

//            echo "Distribution";
//            print_r($model);
//            echo "<br /><br /><br />";
//            echo "Detail";
//            print_r($modelparent);
//            echo "<br /><br /><br />";
//            echo "Master";
//            print_r($modelgparent);
//            echo "<br /><br /><br />";
//            echo "Member";
//            print_r($modelmember);
//            echo "<br /><br /><br />";
//            return;
            if ($model->deposit($modeldetail, 4, "Dealer", $modelmember->id)) {
                \Yii::$app->response->format = 'json';

                return [
                    "saved",
                    Yii::$app->urlManager->baseUrl . "/index.php?r=members/dealers/instruments/index",
                    "Record Saved",
                    "Record saved successfully"
                ];
            }

//            $modeldetail->bank_trans_date = \Yii::$app->formatter->asDate($modeldetail->bank_trans_date, 'php:d-m-Y');
            $model->trans_date = \Yii::$app->formatter->asDate($model->trans_date, 'php:d-m-Y');

            //$modelmember = \app\models\application\Members::find()->where(['id' => $model->id])->one();
//            return $this->redirect(['index']);
        } else {
            $model->trans_date = date("d-m-Y");
            $modeldetail->bank_trans_date = date("d-m-Y");
        }

        $request = Yii::$app->request;

        if ($request->isAjax) {
            return $this->renderPartial('_create', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        }
    }

    /**
     * Updates an existing Fmstransdetaildist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $modeldetail = \app\models\application\fmstransdetaildist::find()->where(['distribution_id' => $id])->one();
        $model = \app\models\application\Fmstransmaster::find()->where(['trans_id' => $modeldetail->trans_id])->one();
        $modelmember = \app\models\application\Members::find()->where(['id' => $modeldetail->distributed_to_id])->one();

        if ($model->load(Yii::$app->request->post())) {
            $modeldetail = Model::createMultiple(\app\models\application\fmstransdetaildist::classname());
            Model::loadMultiple($modeldetail, Yii::$app->request->post());

            for ($i = 0; $i < count($modeldetail); $i++) {
                if ($modeldetail[$i]->cr_amount > 0) {
                    $modeldetail[$i]->dr_amount += floatval($modeldetail[$i]->cr_amount);
                    $modeldetail[$i]->cr_amount =0;
                }
            }

            $data = Yii::$app->request->post();

            if (isset($data["Members"])) {
                $modelmember->id = $data["Members"]["id"];
            }

            if ($model->deposit($modeldetail, 4, "Dealer", $modelmember->id)) {
                \Yii::$app->response->format = 'json';

                return [
                    "saved",
                    Yii::$app->urlManager->baseUrl . "/index.php?r=members/dealers/instruments/index",
                    "Record Saved",
                    "Record saved successfully"
                ];
            }

//            $modeldetail->bank_trans_date = \Yii::$app->formatter->asDate($modeldetail->bank_trans_date, 'php:d-m-Y');
            $model->trans_date = \Yii::$app->formatter->asDate($model->trans_date, 'php:d-m-Y');

            //$modelmember = \app\models\application\Members::find()->where(['id' => $model->id])->one();
//            return $this->redirect(['index']);
        } else {
            $model->trans_date = date("d-m-Y");
            $modeldetail->bank_trans_date = date("d-m-Y");
        }

        $request = Yii::$app->request;

        if ($request->isAjax) {
            return $this->renderPartial('_update', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        } else {
            return $this->render('_update', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        }
    }

    public function actionDeposit($id) {
        $modeldetail = \app\models\application\fmstransdetaildist::find()->where(['distribution_id' => $id])->one();
        $model = \app\models\application\Fmstransmaster::find()->where(['trans_id' => $modeldetail->trans_id])->one();
        $modelmember = \app\models\application\Members::find()->where(['id' => $modeldetail->distributed_to_id])->one();

        if ($model->load(Yii::$app->request->post())) {
            $var = \Yii::$app->request->post('submit');
            $result = false;
            $model->deposit_date = \Yii::$app->formatter->asDate($model->deposit_date, 'php:Y-m-d'); // date("Y-m-d",  strtotime($model->application_date));
            $model->clearance_date = \Yii::$app->formatter->asDate($model->clearance_date, 'php:Y-m-d'); // date("Y-m-d",  strtotime($model->application_date));

            if(strtotime($model->deposit_date) == FALSE) $model->deposit_date = null;
            if(strtotime($model->clearance_date) == FALSE) $model->clearance_date = null;

            if ($var == "deposit") {
                $result = $model->updatedepositslip();
            } else {
                $result = $model->forwardrecord();
            }

            if ($result) {
                \Yii::$app->response->format = 'json';

                return [
                    "saved",
                    Yii::$app->urlManager->baseUrl . "/index.php?r=members/dealers/instruments/index",
                    "Record Saved",
                    "Record saved successfully"
                ];
            }
        } else {
            $model->trans_date = date("d-m-Y");
            //$model->bank_trans_date = date("d-m-Y");
            
            $model->deposit_date = null;// date("d-m-Y");
            $model->clearance_date = null;//date("d-m-Y");
        }

        $model->deposit_date = \Yii::$app->formatter->asDate($model->deposit_date, 'php:d-m-Y');
        $model->clearance_date = \Yii::$app->formatter->asDate($model->clearance_date, 'php:d-m-Y');
        $model->trans_date = \Yii::$app->formatter->asDate($model->trans_date, 'php:d-m-Y');

        if(strtotime($model->deposit_date) == FALSE) $model->deposit_date = null;
        if(strtotime($model->clearance_date) == FALSE) $model->clearance_date = null;

        $request = Yii::$app->request;

        if ($request->isAjax) {
            return $this->renderPartial('deposit', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        } else {
            return $this->render('deposit', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        }
    }

    public function actionVerify($id) {
        $modeldetail = \app\models\application\fmstransdetaildist::find()->where(['distribution_id' => $id])->one();
        $model = \app\models\application\Fmstransmaster::find()->where(['trans_id' => $modeldetail->trans_id])->one();
        $modelmember = \app\models\application\Members::find()->where(['id' => $modeldetail->distributed_to_id])->one();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->verifyrecord()) {
                \Yii::$app->response->format = 'json';

                return [
                    "saved",
                    Yii::$app->urlManager->baseUrl . "/index.php?r=members/dealers/instruments/index",
                    "Record Saved",
                    "Record saved successfully"
                ];
            }

            $model->trans_date = \Yii::$app->formatter->asDate($model->trans_date, 'php:d-m-Y');
        } else {
            $model->trans_date = date("d-m-Y");
            $modeldetail->bank_trans_date = date("d-m-Y");
        }

        $request = Yii::$app->request;

        if ($request->isAjax) {
            return $this->renderPartial('verify', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        } else {
            return $this->render('verify', [
                        'model' => $model,
                        'modeldetail' => $modeldetail,
                        'modelmember' => $modelmember,
            ]);
        }
    }

    /**
     * Deletes an existing Fmstransdetaildist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        if ($this->findModel($id)->delete()) {
            \Yii::$app->response->format = 'json';

            return [
                "Removed",
                Yii::$app->urlManager->baseUrl . "/index.php?r=members/dealers/instruments/index",
                "Record Removed",
                "Record Removed successfully"
            ];
        }
        //return $this->redirect(['index']);
        else {
            \Yii::$app->response->format = 'json';

            return [
                "Failed",
                "",
                "Failed",
                "Failed to remove record."
            ];
//            return "Failed to remove record";
        }
    }

    /**
     * Displays sidebar Fmstransdetaildist model.
     * @return mixed
     */
    public function actionSidebarsummary() {
        $res = \app\models\Model::checksavedinfo(["r" => "members/dealers/instruments/index"]);
        $myrights = \app\models\Model::getRights("index", "instruments", "members/dealers");

        $searchModel = new FmstransdetaildistSearch();
        $searchModel->loadparams($res);

        return $this->renderPartial('_summarysearch', [
                    'searchModel' => $searchModel,
                    'myrights' => $myrights,
        ]);
    }

    /**
     * Displays sidebar Fmstransdetaildist model.
     * @return mixed
     */
    public function actionSidebarinput() {
        return $this->renderPartial('_sidebarinput');
    }

    /**
     * Finds the Fmstransdetaildist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Fmstransdetaildist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Fmstransdetaildist::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
