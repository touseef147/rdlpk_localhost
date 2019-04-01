<?php

namespace app\models\application;

use Yii;

/**
 * This is the model class for table "fms_voucher_plot_comments".
 *
 * @property string $comment_id
 * @property integer $voucher_plot_id
 * @property string $comments
 * @property integer $user_id
 * @property string $date
 * @property integer $generated_by
 * @property integer $comment_type
 * @property string $ip_address
 * @property integer $parent_id
 */
class Fmsvoucherplotcomments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fms_voucher_plot_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['voucher_plot_id', 'comments', 'user_id', 'date', 'generated_by', 'comment_type', 'ip_address'], 'required'],
            [['voucher_plot_id', 'user_id', 'generated_by', 'comment_type', 'parent_id'], 'integer'],
            [['date', 'location'], 'safe'],
            [['comments'], 'string', 'max' => 1000],
//            [['ip_address'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'voucher_plot_id' => 'Voucher Plot ID',
            'comments' => 'Comments',
            'user_id' => 'User ID',
            'date' => 'Date',
            'generated_by' => 'Generated By',
            'comment_type' => 'Category',
            'ip_address' => 'Location',
            'parent_id' => 'Reply of',
            'location' => 'City',
        ];
    }
    
    public function getGeneratedbytitle() {
        if ($this->generated_by == 0)
            return "User";
        else
            return "System";
    }
    
    public function getCommenttypetitle() {
        if ($this->comment_type == 1)
            return "Process";
        elseif ($this->comment_type == 2)
            return "Printing";
        elseif ($this->comment_type == 3)
            return "Other";
        else {
            return "";
        }
    }

    public function getParent()
    {
        return $this->hasOne(Fmsvoucherplotcomments::className(), ['parent_id' => 'comment_id']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public function getReceipt() {
        return $this->hasOne(Fmsvoucherplotdetail::className(), ['voucher_plot_id' => 'voucher_plot_id']);
    }

    public function updaterecord() {
        date_default_timezone_set('Asia/Karachi');

        $this->date=date("Y-m-d h:i:s");
        $this->user_id= Yii::$app->user->identity->id;
        $this->ip_address= getenv('REMOTE_ADDR'); //$_SERVER['REMOTE_ADDR'];
        $this->comment_type= 3;
        
        if (strpos($this->ip_address, ":") === false) {
            $details = json_decode(Propertyapplicationcomments::url_get_contents("http://ipinfo.io/{$this->ip_address}/json"));
            //print_r($details);
            $this->location = $details->city . ", " . $details->country;
            //echo $location;
        } else {
            $this->location = "---";
        }
        
        if ($this->save()) {
            return TRUE;
        }
        if (Yii::$app->user->identity->rawerrors == 1) {
            echo "<h3>" . $this->className() . " Model: updaterecord</h3>";
            print_r($this->errors);
        }

        return FALSE;
    }
    
    public static function updatesystemcomments($comment, $commenttype, $parentid) {
        date_default_timezone_set('Asia/Karachi');

        $history = new Fmsvoucherplotcomments();
        $history->date = date("Y-m-d h:i:s");
        $history->user_id = Yii::$app->user->identity->id;
        $history->ip_address = getenv('REMOTE_ADDR'); //$_SERVER['REMOTE_ADDR'];
        //$history->comment_type= 3;
        $history->generated_by = 1;
        $history->voucher_plot_id = $parentid;
        $history->comments= $comment;
        $history->comment_type = $commenttype;
        
        if (strpos($history->ip_address, ":") === false) {
            $details = json_decode(Propertyapplicationcomments::url_get_contents("http://ipinfo.io/{$history->ip_address}/json"));
            //print_r($details);
            if (isset($details) && isset($details->city) && isset($details->country)) {
                $history->location = $details->city . ", " . $details->country;
            }
            //echo $location;
        } else {
            $history->location = "---";
        }
        

        if ($history->save()) {
            return TRUE;
        }
        if (Yii::$app->user->identity->rawerrors == 1) {
            echo "<h3>" . $history->className() . " Model: updatesystemcomments</h3>";
            print_r($history->errors);
        }

        return FALSE;
    }
    
}
