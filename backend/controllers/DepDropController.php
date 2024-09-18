<?php
namespace backend\controllers;

use backend\modules\rawdata\models\TrnWoColor;
use common\models\ar\MstHandling;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnWo;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

class DepDropController extends Controller
{
    public function actionWoColor() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $wo_id = $parents[0];
                $out = TrnWoColor::find()
                    ->joinWith('moColor')
                    ->select(new Expression("trn_wo_color.id, trn_wo_color.mo_color_id, trn_mo_color.color \"name\""))
                    ->where(['trn_wo_color.wo_id'=>$wo_id])
                    ->asArray()
                    ->all()
                ;
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionWoColorByWoNo() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $wo_no = $parents[0];
                $woID = TrnWo::find()->where(['trn_wo.no'=>$wo_no])->select('id')->asArray()->one();
                $out = TrnWoColor::find()
                    ->joinWith('moColor')
                    ->select(new Expression("trn_wo_color.id, trn_wo_color.mo_color_id, trn_mo_color.color as name"))
                    ->where(['trn_wo_color.wo_id'=>$woID])
                    ->asArray()
                    ->all()
                ;
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionHandling() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $greige_id = $parents[0];
                $out = MstHandling::find()
                    ->select(new Expression("id, name"))
                    ->where(['greige_id'=>$greige_id])
                    ->asArray()
                    ->all()
                ;
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionHandlingByCust($custID) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $greige_id = $parents[0];
                $out = (new Query())
                    ->select(['id', 'name'])
                    ->from(MstHandling::tableName())
                    ->where(['greige_id' => $greige_id])
                    ->andWhere('string_to_array(buyer_ids, \',\') && array[:idBuyer]')
                    ->addParams([':idBuyer' => $custID])
                    ->all()
                ;

                if(empty($out)){
                    $out = MstHandling::find()
                        ->select(new Expression("id, name"))
                        ->where(['greige_id'=>$greige_id])
                        ->asArray()
                        ->all()
                    ;
                }

                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionLookupCreateInspecting() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $jenisProses = $parents[0]; //Dyeing Or Printing
                switch ($jenisProses){
                    case 'dyeing':
                        $query = TrnKartuProsesDyeing::find()
                            ->select(new Expression('id, no "name"'))
                            ->andWhere(['status'=>[TrnKartuProsesDyeing::STATUS_APPROVED, TrnKartuProsesDyeing::STATUS_INSPECTED]])
                            //->limit(20)
                            ->asArray()
                        ;
                        $out = $query->all();
                        break;
                    case 'printing':
                        $query = TrnKartuProsesPrinting::find()
                            ->select(new Expression('id, no "name"'))
                            ->andWhere(['status'=>TrnKartuProsesDyeing::STATUS_APPROVED])
                            //->limit(20)
                            ->asArray()
                        ;
                        $out = $query->all();
                        break;
                }
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }
}
