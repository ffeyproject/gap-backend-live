<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/04/19
 * Time: 23.39
 */

namespace backend\controllers;

use common\models\ar\JualExFinish;
use common\models\ar\MstGreige;
use common\models\ar\MstGreigeGroup;
use common\models\ar\MstHandling;
use common\models\ar\MstVendor;
use common\models\ar\TrnGudangJadi;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesPfpItem;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnKirimMakloon;
use common\models\ar\TrnMo;
use common\models\ar\TrnNotif;
use common\models\ar\TrnOrderCelup;
use common\models\ar\TrnOrderPfp;
use common\models\ar\TrnPfpKeluar;
use common\models\ar\TrnReturBuyer;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AjaxController extends Controller
{
    /**
     * @param null $q
     * @param null $id
     * @return array
     * @throws \yii\db\Exception
     * @throws ForbiddenHttpException
     */
    /*public function actionLookupMember($q = null, $id = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(new Expression('id, concat(full_name, \' (\', email, \')\') "text"'))
                ->from('member')
                ->where(['ilike', 'full_name', $q])
                ->orWhere(['ilike', 'email', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $member = Member::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $member->full_name.' ('.$member->email.')'];
        }
        return $out;
    }*/

    public function actionGreigeSearch($q = null){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];

            if (!is_null($q)) {
                $query = new Query;
                $query->select(new Expression('id, nama_kain "text"'))
                    ->from('mst_greige')
                    ->where(['ilike', 'nama_kain', $q])
                    ->limit(20);
                $command = $query->createCommand();
                $out['results'] = $command->queryAll();
            }

            return $out;
        }

        throw new ForbiddenHttpException('Method tidak diizinkan');
    }

    public function actionGreigeGroupSearch($q = null){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];

            if (!is_null($q)) {
                $query = new Query;
                $query->select(new Expression('id, nama_kain "text"'))
                    ->from('mst_greige_group')
                    ->where(['ilike', 'nama_kain', $q])
                    ->limit(20);
                $command = $query->createCommand();
                $out['results'] = $command->queryAll();
            }

            return $out;
        }

        throw new ForbiddenHttpException('Method tidak diizinkan');
    }

    /**
     * @param null $q
     * @return array
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionCustomerSearch($q = null){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];

            if (!is_null($q)) {
                $query = new Query;
                $query->select(new Expression('id, name "text"'))
                    ->from('mst_customer')
                    ->where(['ilike', 'name', $q])
                    ->limit(20);
                $command = $query->createCommand();
                $out['results'] = $command->queryAll();
            }

            return $out;
        }

        throw new ForbiddenHttpException('Method tidak diizinkan');
    }

    /**
     * @param null $q
     * @return array
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionCustomerNoAndNameSearch($q = null){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];

            if (!is_null($q)) {
                $query = new Query;
                $query->from('mst_customer')
                    ->select(new Expression('id, concat(name, \' (\', cust_no, \')\') "text"'))
                    ->where(['ilike', 'name', $q])
                    ->orWhere(['ilike', 'cust_no', $q])
                    ->limit(20);
                $command = $query->createCommand();
                $out['results'] = $command->queryAll();
            }

            return $out;
        }

        throw new ForbiddenHttpException('Method tidak diizinkan');
    }

    /**
     * @param null $q
     * @return array
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionJualExFinishSearch($q = null){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];

            if (!is_null($q)) {
                $query = new Query;
                $query->select(new Expression('id, no "text"'))
                    ->from(JualExFinish::tableName())
                    ->where(['ilike', 'no', $q])
                    ->andWhere(['not', ['no_urut' => null]])
                    ->limit(20);
                $command = $query->createCommand();
                $out['results'] = $command->queryAll();
            }

            return $out;
        }

        throw new ForbiddenHttpException('Method tidak diizinkan');
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupGreigeGroup($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(new Expression('id, nama_kain "text"'))
                ->from(MstGreigeGroup::tableName())
                ->where(['ilike', 'nama_kain', $q])
                ->andWhere(['aktif'=>true])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupGreige($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                //->select(new Expression('id, nama_kain "text"'))
                //->select(new Expression("id, CONCAT(nama_kain, ' (Alias: ', alias, ')') AS \"text\""))
                ->select(new Expression("id, CONCAT(nama_kain, ' (Alias: ', alias, ')') \"text\""))
                ->from(MstGreige::tableName())
                ->where(['ilike', 'nama_kain', $q])
                ->andWhere(['aktif'=>true])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupHandling($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->select(new Expression("id, name \"text\""))
                ->from(MstHandling::tableName())
                ->where(['ilike', 'name', $q])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoAll($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnWo::find()
                ->select([
                    'trn_wo.id',
                    'trn_wo.no as text',
                    'motif' => 'mst_greige.nama_kain',
                ])
                ->joinWith('greige', false)
                ->where(['ilike', 'trn_wo.no', $q])
                ->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED])
                ->limit(20)
                ->asArray()
            ;

            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoOnlyByNo($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if ($q !== null) {
            $query = TrnWo::find()
                ->select([
                    'trn_wo.id',
                    'trn_wo.no as text',
                ])
                ->where(['ilike', 'trn_wo.no', $q])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoByNo($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if ($q !== null) {
            $query = TrnWo::find()
                ->select([
                    'trn_wo.no as id',
                    'trn_wo.no as text',
                    'motif' => 'mst_greige.nama_kain',
                ])
                ->joinWith('greige', false)
                ->where(['ilike', 'trn_wo.no', $q])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWo($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            /*$query = new Query;
            $query->select(new Expression('id, no "text"'))
                ->from(TrnWo::tableName())
                ->where(['ilike', 'no', $q])
                ->andWhere(['status'=>TrnWo::STATUS_APPROVED])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();*/

            $query = TrnWo::find()
                ->joinWith('scGreige', false)
                ->select(new Expression('trn_wo.sc_greige_id, trn_wo.id, trn_wo.no "text"'))
                ->where(['ilike', 'trn_wo.no', $q])
                ->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED])
                ->andWhere(['<>', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_MAKLOON])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoDyeing($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnWo::find()
                ->joinWith('scGreige', false)
                ->select(new Expression('trn_wo.id, trn_wo.sc_greige_id, trn_wo.no "text"'))
                ->where(['ilike', 'trn_wo.no', $q])
                ->andWhere(['trn_sc_greige.process'=>TrnScGreige::PROCESS_DYEING])
                ->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED])
                ->andWhere(['<>', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_MAKLOON])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoColor(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        $wo_id = Yii::$app->request->post('data');
        if (!is_null($wo_id)) {
            $query = TrnWoColor::find()
                ->with('moColor')
                ->where(['trn_wo_color.wo_id' => $wo_id])
                ->asArray()
            ;
            $out = $query->all();
        }

        return $out;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoColorByKirimMakloon(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        $id = Yii::$app->request->post('data');
        if (!is_null($id)) {
            $model = TrnKirimMakloon::findOne($id);
            $query = TrnWoColor::find()
                ->with('moColor')
                ->where(['trn_wo_color.wo_id' => $model->wo_id])
                ->limit(20)
                ->asArray()
            ;
            $out = $query->all();
        }

        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupOrderPfp($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnOrderPfp::find()
                ->select(new Expression('id, status, no "text"'))
                ->where(['ilike', 'no', $q])
                ->andWhere(['<>', 'status', TrnOrderPfp::STATUS_DRAFT])
                //->andWhere(['status' => TrnOrderPfp::STATUS_APPROVED])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupOrderCelup($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnOrderCelup::find()
                ->select(new Expression('id, status, no "text"'))
                ->where(['ilike', 'no', $q])
                ->andWhere(['<>', 'status', TrnOrderCelup::STATUS_DRAFT])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoPrinting($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnWo::find()
                ->joinWith('scGreige', false)
                ->select(new Expression('trn_wo.sc_greige_id, trn_wo.id, trn_wo.no "text"'))
                ->where(['ilike', 'trn_wo.no', $q])
                ->andWhere(['trn_sc_greige.process'=>TrnScGreige::PROCESS_PRINTING])
                ->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED])
                ->andWhere(['<>', 'trn_wo.jenis_order', TrnSc::JENIS_ORDER_MAKLOON])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoMakloon($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(new Expression('id, no "text"'))
                ->from(TrnWo::tableName())
                ->where(['ilike', 'no', $q])
                ->andWhere(['status'=>TrnWo::STATUS_APPROVED])
                ->andWhere(['trn_wo.jenis_order'=>TrnSc::JENIS_ORDER_MAKLOON])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();

            /*$query = TrnWo::find()
                ->joinWith('scGreige', false)
                ->select(new Expression('trn_wo.sc_greige_id, trn_wo.id, trn_wo.no "text"'))
                ->where(['ilike', 'trn_wo.no', $q])
                ->andWhere(['trn_sc_greige.process'=>TrnScGreige::PROCESS_DYEING])
                ->andWhere(['trn_wo.status'=>TrnWo::STATUS_APPROVED])
                ->andWhere(['trn_wo.jenis_order'=>TrnSc::JENIS_ORDER_MAKLOON])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();*/
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupWoBeliJadi($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(new Expression('id, no "text"'))
                ->from(TrnWo::tableName())
                ->where(['ilike', 'no', $q])
                ->andWhere(['status'=>TrnWo::STATUS_APPROVED])
                ->andWhere(['trn_wo.jenis_order'=>TrnSc::JENIS_ORDER_BARANG_JADI])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionLookupPlGreigeNoDoc($q = null){
        //sleep(2);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        if (!is_null($q)) {
            $query = new Query;
            $query->from(TrnStockGreige::tableName())
                ->where(['no_document'=>$q]);
            $out = $query->one();

            if(!$out){throw new NotFoundHttpException('Data tidak ditemukan.');}

            $greige = $query->select(['id', 'nama_kain'])
            ->from(MstGreige::tableName())
            ->where(['id'=>$out['greige_id']])
            ->one();

            if(!$greige){throw new NotFoundHttpException('Data tidak ditemukan.');}

            $out['greige'] = $greige;
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupKartuProsesDyeing($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnKartuProsesDyeing::find()
                ->joinWith('wo')
                ->select(new Expression('trn_kartu_proses_dyeing.id, trn_kartu_proses_dyeing.no "text", trn_wo.id "wo_id", trn_wo.no "wo_no"'))
                ->where(['ilike', 'trn_kartu_proses_dyeing.no', $q])
                ->andWhere(['trn_kartu_proses_dyeing.status'=>TrnKartuProsesDyeing::STATUS_GANTI_GREIGE])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupKartuProsesPrinting($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = TrnKartuProsesPrinting::find()
                ->joinWith('wo')
                ->select(new Expression('trn_kartu_proses_printing.id, trn_kartu_proses_printing.no "text", trn_wo.id "wo_id", trn_wo.no "wo_no"'))
                ->where(['ilike', 'trn_kartu_proses_printing.no', $q])
                ->andWhere(['trn_kartu_proses_printing.status'=>TrnKartuProsesPrinting::STATUS_GANTI_GREIGE])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionGetNotif($userId){
        $result = [
            'notif' => [],
            'msg' => [],
            'task' => [],
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $datas TrnNotif[]*/
        $datas = TrnNotif::find()->where(['user_id'=>$userId, 'read'=>false])->asArray()->all();
        foreach ($datas as $data) {
            switch ($data['type']){
                case TrnNotif::TYPE_NOTIFICATION:
                    $result['notif'][] = '<li><a href="'.$data['link'].'"><h3><small>'.$data['message'].'</small></h3></a></li>';
                    break;
                case TrnNotif::TYPE_MESSAGE:
                    $result['msg'][] = '<li><a href="'.$data['link'].'"><h3><small>'.$data['message'].'</small></h3></a></li>';
                    break;
                case TrnNotif::TYPE_TASK:
                    $result['task'][] = '<li><a href="'.$data['link'].'"><h3><small>'.$data['message'].'</small></h3></a></li>';
                    break;
            }
        }

        return $result;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupKirimMakloonPosted($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->select(new Expression("id, no \"text\""))
                ->from(TrnKirimMakloon::tableName())
                ->where(['ilike', 'no', $q])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupReturBuyerRepair($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->select(new Expression("id, no \"text\""))
                ->from(TrnReturBuyer::tableName())
                ->where(['ilike', 'no', $q])
                ->andWhere(['status'=>TrnReturBuyer::STATUS_REPAIR])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupReturBuyerRedyeing($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->select(new Expression("id, no \"text\""))
                ->from(TrnReturBuyer::tableName())
                ->where(['ilike', 'no', $q])
                ->andWhere(['status'=>TrnReturBuyer::STATUS_RE_DYEING])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @param null $q
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionLookupSc($q = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->select(new Expression("id, no \"text\""))
                ->from(TrnSc::tableName())
                ->where(['ilike', 'no', $q])
                ->andWhere(['status'=>TrnSc::STATUS_APPROVED])
                ->limit(20);
            $command = $query->createCommand();
            $out['results'] = $command->queryAll();
        }
        return $out;
    }

    /**
     * @return int
     * @throws NotFoundHttpException
     */
    public function actionLookupReWo(){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $nomorWo = null;

        $data = Yii::$app->request->post('reWo');
        if(!empty($data)){
            $nomorWo = $data;
        }

        if($nomorWo === null){
            throw new InvalidArgumentException('parameter nomor WO tidah boleh kosong');
        }

        $row = (new Query())
            ->select(['mo_id', 'no', 'id'])
            ->from(TrnWo::tableName())
            ->where(['no'=>$nomorWo])
            ->one();

        if($row){
            $mo = (new Query())
                ->from(TrnMo::tableName())
                ->where(['id'=>$row['mo_id']])
                ->one();
            return $mo;
        }

        throw new NotFoundHttpException('WO dengan nomor: '.$nomorWo.' tidak ditemukan.');
    }

    /**
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionLookupKpById($q = null, $id = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = null;
        if (!is_null($q)) {
            switch ($q){
                case 'dyeing':
                    $out = TrnKartuProsesDyeing::find()
                        ->joinWith(['wo.mo.sc.cust', 'wo.greige', 'woColor.moColor'])
                        ->where(['trn_kartu_proses_dyeing.id'=>$id])
                        ->asArray()
                        ->one()
                    ;
                    break;
                case 'printing':
                    $out = TrnKartuProsesPrinting::find()
                        ->joinWith(['wo.mo.sc.cust', 'wo.greige', 'woColor.moColor'])
                        ->where(['trn_kartu_proses_printing.id'=>$id])
                        ->asArray()
                        ->one()
                    ;
                    break;
            }
        }
        return $out;
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionLookupStockGreigeFresh($q){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            /*$query = TrnStockGreige::find()
                ->joinWith('greige', false)
                ->select(new Expression("trn_stock_greige.id, CONCAT(trn_stock_greige.id, ' | ', mst_greige.nama_kain) AS text"))
                ->where(['trn_stock_greige.status'=>TrnStockGreige::STATUS_VALID])
                ->andWhere(['ilike', 'mst_greige.nama_kain', $q])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();*/

            $stocks = TrnStockGreige::find()
                ->joinWith('greige')
                ->where(['trn_stock_greige.status'=>TrnStockGreige::STATUS_VALID])
                ->andWhere(['ilike', 'mst_greige.nama_kain', $q])
                ->limit(20)
                ->asArray()
                ->all()
            ;

            $out['results'] = [];

            foreach ($stocks as $stock) {
                $panjang = Yii::$app->formatter->asDecimal($stock['panjang_m']);
                $out['results'][] = [
                    'id'=>$stock['id'],
                    'text'=>$stock['id'].
                        ' | '.$stock['greige']['nama_kain'].
                        ' | '.TrnStockGreige::gradeOptions()[$stock['grade']].
                        ' | '.$panjang.
                        ' | '.$stock['lot_lusi'].
                        ' | '.$stock['lot_pakan'].
                        ' | '.TrnStockGreige::asalGreigeOptions()[$stock['asal_greige']]
                ];
            }
        }

        return $out;
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGudangJadiByKeys(){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post('data');

        $response = [];
        $models = TrnGudangJadi::findAll(['id'=>$data]);
        $no = 1;
        foreach ($models as $model) {
            $response[] = [
                'no'=>$no,
                //'qty'=>$model->qty,
                'qty'=>Yii::$app->formatter->asDecimal($model->qty),
                'grade'=>$model->gradeName
            ];

            $no ++;
        }


        return $response;
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionLookupPfpKeluar($q){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $query = TrnPfpKeluar::find()
                ->select(["*", new Expression("no AS text")])
                ->where(['status'=>TrnPfpKeluar::STATUS_APPROVED])
                ->andWhere(['ilike', 'no', $q])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }

        return $out;
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionLookupVendor($q){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $query = MstVendor::find()
                ->select(["*", new Expression("name AS text")])
                ->where(['aktif'=>true])
                ->andWhere(['ilike', 'name', $q])
                ->limit(20)
                ->asArray()
            ;
            $out['results'] = $query->all();
        }

        return $out;
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    // public function actionLookupKartuProsesStock($processId, $kartuProsesId, $greigeId, $asalGreige, $jenisGudang, $q){
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $dataPencarian = explode('*', $q);
    //     $noDocument = isset($dataPencarian[0]) ? $dataPencarian[0] : null;
    //     $qty = isset($dataPencarian[1]) ? $dataPencarian[1] : null;
    //     $grade = isset($dataPencarian[2]) ? $dataPencarian[2] : null;
    //     $lotLusi = isset($dataPencarian[3]) ? $dataPencarian[3] : null;
    //     $lotPakan = isset($dataPencarian[4]) ? $dataPencarian[4] : null;
    //     $ketWeaving = isset($dataPencarian[5]) ? $dataPencarian[5] : null;

    //     $qStockOptionList = TrnStockGreige::find()
    //         ->with('greige')
    //         ->where(['status'=>TrnStockGreige::STATUS_VALID])
    //         ->andWhere(['greige_id'=>$greigeId])
    //         ->andWhere(['asal_greige'=>$asalGreige])
    //         ->andWhere(['jenis_gudang'=>$jenisGudang])
    //         ->andFilterWhere(['ilike', 'no_document', $noDocument])
    //         ->andFilterWhere(['lot_lusi'=>$lotLusi])
    //         ->andFilterWhere(['lot_pakan'=>$lotPakan])
    //         ->andFilterWhere(['status_tsd'=>$ketWeaving])
    //     ;

    //     switch ($processId){
    //         case TrnScGreige::PROCESS_DYEING:
    //             $selectedItems = TrnKartuProsesDyeingItem::find()
    //                 ->select(['id', 'stock_id'])
    //                 ->where(['kartu_process_id'=>$kartuProsesId])
    //                 ->asArray()
    //                 ->all();
    //             $selectedItemsIds = ArrayHelper::getColumn($selectedItems, 'stock_id');
    //             if(!empty($selectedItemsIds)){
    //                 $qStockOptionList->andWhere(['not in', 'id', $selectedItemsIds]);
    //             }
    //             break;
    //         case TrnScGreige::PROCESS_PRINTING:
    //             $selectedItems = TrnKartuProsesPrintingItem::find()
    //                 ->select(['id', 'stock_id'])
    //                 ->where(['kartu_process_id'=>$kartuProsesId])
    //                 ->asArray()
    //                 ->all();
    //             $selectedItemsIds = ArrayHelper::getColumn($selectedItems, 'stock_id');
    //             if(!empty($selectedItemsIds)){
    //                 $qStockOptionList->andWhere(['not in', 'id', $selectedItemsIds]);
    //             }
    //             break;
    //         case TrnScGreige::PROCESS_PFP:
    //             $selectedItems = TrnKartuProsesPfpItem::find()
    //                 ->select(['id', 'stock_id'])
    //                 ->where(['kartu_process_id'=>$kartuProsesId])
    //                 ->asArray()
    //                 ->all();
    //             $selectedItemsIds = ArrayHelper::getColumn($selectedItems, 'stock_id');
    //             if(!empty($selectedItemsIds)){
    //                 $qStockOptionList->andWhere(['not in', 'id', $selectedItemsIds]);
    //             }
    //             break;
    //     }

    //     if($qty > 0){
    //         $qStockOptionList->andWhere(['panjang_m'=>$qty]);
    //     }

    //     switch ($grade){
    //         case 'a':
    //         case 'A':
    //             $qStockOptionList->andWhere(['grade'=>TrnStockGreige::GRADE_A]);
    //             break;
    //         case 'b':
    //         case 'B':
    //             $qStockOptionList->andWhere(['grade'=>TrnStockGreige::GRADE_B]);
    //             break;
    //         case 'c':
    //         case 'C':
    //             $qStockOptionList->andWhere(['grade'=>TrnStockGreige::GRADE_C]);
    //             break;
    //         case 'd':
    //         case 'D':
    //             $qStockOptionList->andWhere(['grade'=>TrnStockGreige::GRADE_D]);
    //             break;
    //         case 'e':
    //         case 'E':
    //             $qStockOptionList->andWhere(['grade'=>TrnStockGreige::GRADE_E]);
    //             break;
    //         case 'ng':
    //         case 'NG':
    //             $qStockOptionList->andWhere(['grade'=>TrnStockGreige::GRADE_NG]);
    //             break;
    //         default:
    //     }

    //     $qStockOptionList->limit(20);

    //     //$out = ['results' => ['id' => '', 'text' => '']];
    //     $out = ['results'=>[]];
    //     foreach ($qStockOptionList->asArray()->all() as $item) {
    //         $panjang = Yii::$app->formatter->asDecimal($item['panjang_m']);

    //         switch ($item['status_tsd']) {
    //             case TrnStockGreige::STATUS_TSD_SM:
    //             case TrnStockGreige::STATUS_TSD_SA:
    //             case TrnStockGreige::STATUS_TSD_ST:
    //                 $ketWv = ' | Ket. Weaving: '.TrnStockGreige::tsdOptions()[$item['status_tsd']].' &#9940;';
    //                 break;
    //             case TrnStockGreige::STATUS_TSD_TSD:
    //                 $ketWv = ' | Ket. Weaving: '.TrnStockGreige::tsdOptions()[$item['status_tsd']].' &#9851;';
    //                 break;
    //             default:
    //                 $ketWv = ' | Ket. Weaving: '.TrnStockGreige::tsdOptions()[$item['status_tsd']].' &#9737;';
    //         }
    //         $text = 'Greige: '.$item['greige']['nama_kain'].
    //             ' | Grade: '.TrnStockGreige::gradeOptions()[$item['grade']].
    //             ' | Lapak: '.$item['no_lapak'].
    //             ' | Panjang: '.$panjang.'M | No Doc.: '.$item['no_document'].
    //             ' | No. MC.: '.$item['no_set_lusi'].
    //             ' | Lot Lusi: '.$item['lot_lusi'].
    //             ' | Lot Pakan: '.$item['lot_pakan'].
    //             $ketWv
    //         ;
    //         $out['results'][] = ['id'=>$item['id'], 'text'=>$text, 'data'=>$item];
    //     }

    //     return $out;
    // }

    public function actionLookupKartuProsesStock($processId, $kartuProsesId, $greigeId, $asalGreige, $jenisGudang, $q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $dataPencarian = explode('*', $q);
        $noDocument = $dataPencarian[0] ?? null;
        $qty        = $dataPencarian[1] ?? null;
        $grade      = $dataPencarian[2] ?? null;
        $lotLusi    = $dataPencarian[3] ?? null;
        $lotPakan   = $dataPencarian[4] ?? null;
        $ketWeaving = $dataPencarian[5] ?? null;

        $qStockOptionList = TrnStockGreige::find()
            ->with('greige')
            ->where(['status' => TrnStockGreige::STATUS_VALID])
            ->andWhere(['greige_id' => $greigeId])
            ->andWhere(['asal_greige' => $asalGreige])
            ->andWhere(['jenis_gudang' => $jenisGudang])
            ->andFilterWhere(['ilike', 'no_document', $noDocument])
            ->andFilterWhere(['lot_lusi' => $lotLusi])
            ->andFilterWhere(['lot_pakan' => $lotPakan])
            ->andFilterWhere(['status_tsd' => $ketWeaving]);

        // Exclude stock yg sudah dipakai
        switch ($processId) {
            case TrnScGreige::PROCESS_DYEING:
                $selectedItems = TrnKartuProsesDyeingItem::find()
                    ->select(['stock_id'])
                    ->where(['kartu_process_id' => $kartuProsesId])
                    ->column();
                if (!empty($selectedItems)) {
                    $qStockOptionList->andWhere(['not in', 'id', $selectedItems]);
                }
                break;

            case TrnScGreige::PROCESS_PRINTING:
                $selectedItems = TrnKartuProsesPrintingItem::find()
                    ->select(['stock_id'])
                    ->where(['kartu_process_id' => $kartuProsesId])
                    ->column();
                if (!empty($selectedItems)) {
                    $qStockOptionList->andWhere(['not in', 'id', $selectedItems]);
                }
                break;

            case TrnScGreige::PROCESS_PFP:
                $selectedItems = TrnKartuProsesPfpItem::find()
                    ->select(['stock_id'])
                    ->where(['kartu_process_id' => $kartuProsesId])
                    ->column();
                if (!empty($selectedItems)) {
                    $qStockOptionList->andWhere(['not in', 'id', $selectedItems]);
                }
                break;
        }

        if ($qty > 0) {
            $qStockOptionList->andWhere(['panjang_m' => $qty]);
        }

        // Filter grade
        switch (strtolower($grade)) {
            case 'a': $qStockOptionList->andWhere(['grade' => TrnStockGreige::GRADE_A]); break;
            case 'b': $qStockOptionList->andWhere(['grade' => TrnStockGreige::GRADE_B]); break;
            case 'c': $qStockOptionList->andWhere(['grade' => TrnStockGreige::GRADE_C]); break;
            case 'd': $qStockOptionList->andWhere(['grade' => TrnStockGreige::GRADE_D]); break;
            case 'e': $qStockOptionList->andWhere(['grade' => TrnStockGreige::GRADE_E]); break;
            case 'ng': $qStockOptionList->andWhere(['grade' => TrnStockGreige::GRADE_NG]); break;
            default: // tidak filter grade
        }

        $qStockOptionList->limit(20);

        $out = ['results' => []];
        foreach ($qStockOptionList->asArray()->all() as $item) {
            $panjang = Yii::$app->formatter->asDecimal($item['panjang_m']);

            // Build full text
            $text = 'Greige: '.$item['greige']['nama_kain'].
                ' | Grade: '.TrnStockGreige::gradeOptions()[$item['grade']].
                ' | Lapak: '.$item['no_lapak'].
                ' | Panjang: '.$panjang.'M'.
                ' | No Doc.: '.$item['no_document'].
                ' | No. MC.: '.$item['no_set_lusi'].
                ' | Lot Lusi: '.$item['lot_lusi'].
                ' | Lot Pakan: '.$item['lot_pakan'].
                ' | Ket. Weaving: '.TrnStockGreige::tsdOptions()[$item['status_tsd']];

            // Tambah warna untuk seluruh baris
            switch ($item['status_tsd']) {
                case TrnStockGreige::STATUS_TSD_SM:
                    $text = '<span style="color:red;font-weight:bold;">'.$text.' &#9940;</span>';
                    break;
                case TrnStockGreige::STATUS_TSD_SA:
                    $text = '<span style="color:red;font-weight:bold;">'.$text.' &#9940;</span>';
                    break;
                case TrnStockGreige::STATUS_TSD_ST:
                    $text = '<span style="color:red;font-weight:bold;">'.$text.' &#9940;</span>';
                    break;
                case TrnStockGreige::STATUS_TSD_TSD:
                    $text = '<span style="color:brown;font-weight:bold;">'.$text.' &#9851;</span>';
                    break;
                case TrnStockGreige::STATUS_TSD_LAIN_LAIN:
                    $text = '<span style="color:brown;font-weight:bold;">'.$text.' &#8635;</span>';
                    break;
                case TrnStockGreige::STATUS_TSD_PUTIH:
                    $text = '<span style="color:green;font-weight:bold;">'.$text.' &#9851;</span>';
                    break;
                case TrnStockGreige::STATUS_GETAR_MESIN:
                    $text = '<span style="color:green;font-weight:bold;">'.$text.' &#9851;</span>';
                    break;
                default:
                    $text = '<span style="color:black;">'.$text.' &#9737;</span>';
            }

            $out['results'][] = [
                'id'   => $item['id'],
                'text' => $text,
                'data' => $item,
            ];
        }

        return $out;
    }


}