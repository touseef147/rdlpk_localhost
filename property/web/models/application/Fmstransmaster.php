<?php

namespace app\models\application;

use Yii;

/**
 * This is the model class for table "fms_trans_master".
 *
 * @property integer $trans_id
 * @property string $serial_no
 * @property integer $trans_type
 * @property string $trans_date
 * @property string $remarks
 * @property integer $entered_by
 * @property string $ref_no
 * @property integer $project_id
 * @property integer $sales_center_id
 */
class Fmstransmaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fms_trans_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        //, 'remarks'
        return [
            [['trans_type', 'trans_date'], 'required'],
            [['trans_type', 'entered_by', 'project_id', 'sales_center_id', 'external_trans_type'], 'integer'],
            [['trans_date'], 'safe'],
            [['serial_no'], 'string', 'max' => 20],
            [['remarks'], 'string', 'max' => 1000],
            [['ref_no'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'trans_id' => 'Trans ID',
            'serial_no' => 'Serial No',
            'trans_type' => 'Trans Type',
            'trans_date' => 'Trans Date',
            'remarks' => 'Remarks',
            'entered_by' => 'Entered By',
            'ref_no' => 'Ref No',
            'project_id' => 'Project',
            'sales_center_id' => 'Sales Center',
            'external_trans_type' => 'Transaction Against',
        ];
    }

    /* PROPERTIES */

    public function getTranstypetitle() {
        if ($this->trans_type == 1)
            return "Cash Receipt";
        elseif ($this->trans_type == 2)
            return "Bank Receipt";
        elseif ($this->trans_type == 3)
            return "Journal";
        elseif ($this->trans_type == 4)
            return "Cash Payment";
        elseif ($this->trans_type == 5)
            return "Bank Payment";
    }

    public function getViewtransdate() {
        return date("d-m-Y", strtotime($this->trans_date));
    }

    /* END OF PROPERTIES */

    public function getProject() {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    public function getSalescenter() {
        return $this->hasOne(Salescenter::className(), ['id' => 'sales_center_id']);
    }

    public function getDetails() {
        return $this->hasMany(Fmstransdetail::className(), ['trans_id' => 'trans_id']);
    }

    public function getDistributions() {
        return $this->hasMany(fmstransdetaildist::className(), ['trans_id' => 'trans_id']);
    }

    public function updaterecord() {
        if ($this->save()) {
            return TRUE;
        }

        if (Yii::$app->user->identity->rawerrors == 1) {
            echo "<h3>" . $this->className() . " Model: updaterecord</h3>";
            print_r($this->errors);
        }

        return FALSE;
    }

    public function deposit(&$detail, $craccountid = 0, $externalentitytype = "", $externalentityid = 0) {
        $totalamount = 0;
        $bankid = 0;
        $banktransdate = date("Y-m-d");
        $chequeno = "";

        for ($i = 0; $i < count($detail); $i++) {
//            if ($detail[$i]->acc_id < 1) {
//                $draccountid = $detail[$i]->detail_id;
//            }
            if ($detail[$i]->dr_amount > 0) {
                $totalamount += floatval($detail[$i]->dr_amount);
            }

            $detail[$i]->bank_trans_date = \Yii::$app->formatter->asDate($detail[$i]->bank_trans_date, 'php:Y-m-d'); // date("Y-m-d",  strtotime($model->application_date));
        }

        $valid = TRUE;
        $transaction = \Yii::$app->db->beginTransaction();

        if ($valid) {
            $this->trans_date = \Yii::$app->formatter->asDate($this->trans_date, 'php:Y-m-d'); // date("Y-m-d",  strtotime($model->application_date));
            $this->external_trans_type = ($externalentitytype == "Dealer" ? 1 : 0);

            if (!$this->save()) {
                $valid = FALSE;
                if (Yii::$app->user->identity->rawerrors == 1) {
                    echo "<h3>" . $this->className() . " Model: register</h3>";
                    print_r($this->errors);
                }
            }
        }

        //save distribution details
        if ($valid) {     //distribution
            for ($i = 0; $i < count($detail); $i++) {
                if ($detail[$i]->dr_amount > 0) {
                    $detail[$i]->trans_id = $this->trans_id;
                    $detail[$i]->against = $craccountid;
                    $detail[$i]->distributed_to_type = ($detail[$i]->bank_id == null || $detail[$i]->bank_id == 0 ? "Cash" : "Bank");
                    $detail[$i]->distributed_to_id = ($detail[$i]->bank_id == null || $detail[$i]->bank_id == 0 ? 1 : 2);

                    $bankid = $detail[$i]->bank_id;
                    $banktransdate = $detail[$i]->bank_trans_date;
                    $chequeno = $detail[$i]->bank_trans_no;

                    if (!$detail[$i]->updaterecord()) {
                        $valid = FALSE;
//                        for ($t = 0; $t < count($distribution); $t++) {
//                            $distribution[$t]->trans_detail_id=$detail[$i]->trans_id;
//                            $distribution[$t]->updaterecord();
//                        }
                    }
                }
            }
        }

        if ($valid) {
            $cr = fmstransdetaildist::find()->where(['trans_id' => $this->trans_id])->andFilterWhere(['>', 'cr_amount', 0])->one();

            if ($cr == NULL)
                $cr = new fmstransdetaildist();
            $cr->trans_id = $this->trans_id;
            $cr->against = $craccountid;
            $cr->distributed_to_id = $externalentityid;
            $cr->distributed_to_type = $externalentitytype;
            $cr->cr_amount = $totalamount;
            $cr->remarks = "";
            $cr->bank_id = $bankid;
            $cr->bank_trans_date = $banktransdate;
            $cr->bank_trans_no = $chequeno;
            if (!$cr->updaterecord()) {
                $valid = false;
            }
        }

        //Voucher detail
        if ($valid) {
            $dr = Fmstransdetail::find()->where(['trans_id' => $this->trans_id, 'acc_id' => 1])->one();

            if ($dr == NULL)
                $dr = new Fmstransdetail();

            $dr->trans_id = $this->trans_id;
            $dr->acc_id = 1;
            $dr->dr_amount = $totalamount;
            $dr->remarks = "";
            if (!$dr->updaterecord()) {
                $valid = false;
            }
        }

        if ($valid) {
            $cr = Fmstransdetail::find()->where(['trans_id' => $this->trans_id, 'acc_id' => $craccountid])->one();

            if ($cr == NULL)
                $cr = new Fmstransdetail();

            $cr->trans_id = $this->trans_id;
            $cr->acc_id = $craccountid;
            $cr->cr_amount = $totalamount;
            $cr->remarks = "";
            if (!$cr->updaterecord()) {
                $valid = false;
            }
        }

        if (!$valid) {
            if (Yii::$app->user->identity->rawerrors == 1) {
                echo "<h3>" . $this->className() . " Model: deposit</h3>";
                print_r($this->errors);
            }
        }

        if ($valid) {
            $transaction->commit();
            return TRUE;
        } else {
            $transaction->rollBack();
            return FALSE;
        }
    }

    public function withdraw(&$detail, &$distribution) {
        if ($this->save()) {
            return TRUE;
        }

        if (Yii::$app->user->identity->rawerrors == 1) {
            echo "<h3>" . $this->className() . " Model: withdraw</h3>";
            print_r($this->errors);
        }

        return FALSE;
    }

    public function adjust(&$detail, &$distribution) {
        if ($this->save()) {
            return TRUE;
        }

        if (Yii::$app->user->identity->rawerrors == 1) {
            echo "<h3>" . $this->className() . " Model: adjust</h3>";
            print_r($this->errors);
        }

        return FALSE;
    }

    public static function adjustdealerinstrumentfromreceipts($dealerid, $amount, $instrumentid, $date, $project, $salescenter) {
        $valid = true;

        $id = 0;
        $instrument = fmstransdetaildist::find()->where(['distributed_to_type' => 'instrument', 'distributed_to_id' => $instrumentid])->one();

        if ($instrument != null) {
            $id = $instrument->trans_id;
        }

        $trans = ($id == 0 ? new Fmstransmaster() : Fmstransmaster::find()->where(['trans_id' => $id])->one());
        $detaildr = ($id == 0 ? new Fmstransdetail() : Fmstransdetail::find()->where(['trans_id' => $id])->andFilterWhere(['>', 'dr_amount', 0])->one());
        $detailcr = ($id == 0 ? new Fmstransdetail() : Fmstransdetail::find()->where(['trans_id' => $id])->andFilterWhere(['>', 'cr_amount', 0])->one());
        $distdr = ($id == 0 ? new fmstransdetaildist() : fmstransdetaildist::find()->where(['trans_id' => $id])->andFilterWhere(['>', 'cr_amount', 0])->one());
        $distcr = ($id == 0 ? new fmstransdetaildist() : fmstransdetaildist::find()->where(['trans_id' => $id])->andFilterWhere(['>', 'cr_amount', 0])->one());

        if ($id == 0) {
            $trans->trans_type = 5;
            $trans->trans_date = $date;
            $trans->entered_by = Yii::$app->user->id;
            $trans->project_id = $project;
            $trans->sales_center_id = $salescenter;

            $detaildr->acc_id = 4;
            $detaildr->dr_amount = $amount;

            $detailcr->acc_id = 3;
            $detailcr->cr_amount = $amount;

            $distdr->distributed_to_type = "Dealer";
            $distdr->distributed_to_id = $dealerid;
            $distdr->dr_amount = $amount;
            $distdr->against = 4;

            $distcr->distributed_to_type = "Instrument";
            $distcr->distributed_to_id = $instrumentid;
            $distcr->cr_amount = $amount;
            $distcr->against = 4;

            if ($trans->save()) {
                $detaildr->trans_id = $trans->trans_id;
                $detailcr->trans_id = $trans->trans_id;
                $distdr->trans_id = $trans->trans_id;
                $distcr->trans_id = $trans->trans_id;

                if(!$detaildr->save()){
                    $valid=FALSE;
                    
                    if (Yii::$app->user->identity->rawerrors == 1) {
                        echo "<h3>Model: adjustdealerinstrumentfromreceipts</h3>";
                        print_r($detaildr->errors);
                    }
                }

                if(!$detailcr->save()){
                    $valid=FALSE;
                    
                    if (Yii::$app->user->identity->rawerrors == 1) {
                        echo "<h3>Model: adjustdealerinstrumentfromreceipts</h3>";
                        print_r($detailcr->errors);
                    }
                }

                if(!$distdr->save()){
                    $valid=FALSE;
                    
                    if (Yii::$app->user->identity->rawerrors == 1) {
                        echo "<h3>Model: adjustdealerinstrumentfromreceipts</h3>";
                        print_r($distdr->errors);
                    }
                }

                if(!$distcr->save()){
                    $valid=FALSE;
                    
                    if (Yii::$app->user->identity->rawerrors == 1) {
                        echo "<h3>Model: adjustdealerinstrumentfromreceipts</h3>";
                        print_r($distcr->errors);
                    }
                }
            } else {
                $valid = false;
                
                if (Yii::$app->user->identity->rawerrors == 1) {
                    echo "<h3>Model: adjustdealerinstrumentfromreceipts</h3>";
                    print_r($trans->errors);
                }
            }
        } else {
            
        }

        return $valid;
    }

}
