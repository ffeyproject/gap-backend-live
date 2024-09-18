<?php
namespace backend\controllers;

use common\models\ar\MstHandling;
use common\models\ar\MstProcessDyeing;
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnMo;
use common\models\ar\TrnMoColor;
use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use common\models\ar\TrnWoMemo;
use mdm\admin\models\Route;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseVarDumper;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class TestController extends Controller
{
    public function actionIndex(){
        $model = new TrnWoColor(['wo_id' => 2460, 'note'=>'-', 'mo_color_id'=>8004, 'qty'=>1]);

        $wo = $model->wo;
        $model->greige_id = $wo->greige_id;
        $model->mo_id = $wo->mo_id;
        $model->sc_greige_id = $wo->sc_greige_id;
        $model->sc_id = $wo->sc_id;

        // hitung berapa banyak color yang sudah dipakai di wo ini dan wo lain, jika sudah terpakai semua, gagalkan. wo batal tidak dihitung.
        $moColorSudahDigunakan = (new Query())->from(TrnWoColor::tableName())
            ->innerJoin(TrnWo::tableName(), 'trn_wo.id = trn_wo_color.wo_id')
            ->where(['mo_color_id'=>$model->mo_color_id])
            ->andWhere(['<>', 'trn_wo.status', TrnWo::STATUS_BATAL])
            ->sum('qty')
        ;
        $moColorTotal = $moColorSudahDigunakan === null ? $model->qty : $moColorSudahDigunakan + $model->qty;

        BaseVarDumper::dump([
            '$model->moColor->qty' => $model->moColor->qty,
            '$moColorSudahDigunakan' => $moColorSudahDigunakan,
            '$moColorTotal' => $moColorTotal
        ], 10, true);
    }

    public function actionHexadecimal(){
        $integer = [
            'PHP_INT_MIN'=>PHP_INT_MIN,
            'PHP_INT_MAX'=>PHP_INT_MAX,
            '$nilai1'=>9223372036854775806,
            '$nilai2'=>2,
            '$nilai1 + $nilai2' => 9223372036854775806 + 2,
        ];

        $float = [
            'PHP_FLOAT_MIN'=>PHP_FLOAT_MIN,
            'PHP_FLOAT_MAX'=>PHP_FLOAT_MAX,
            'sprintf(%f, PHP_FLOAT_MIN)'=>sprintf('%f', PHP_FLOAT_MIN),
            'sprintf(%f, PHP_FLOAT_MAX)'=>sprintf('%f', PHP_FLOAT_MAX),
            ''=> Yii::$app->formatter->asDecimal(1.2E-5, 8),
            '0x4baf21'=>0x000000000021af4b,
            '$nilai1'=>0,
            '$nilai2'=>0,
            '$nilai1 + $nilai2' => 0,
        ];

        $heksadesimal = [
            '0x1f'=>0x1f,
            '0xa + 0xb'=>0xa + 0xb
        ];

        BaseVarDumper::dump([
            '$integer'=>$integer,
            '$float'=>$float,
            '$heksadesimal'=>$heksadesimal
        ], 10, true
        );Yii::$app->end();
    }

    public function actionRbac(){
        $modelRoute = new Route();
        $routes = array_values($modelRoute->getAppRoutes());

        $auth = Yii::$app->authManager;
        $print = $auth->getChildRoles('Registered');
        $print2 = array_keys($auth->getPermissionsByRole('Registered'));

        BaseVarDumper::dump([$print, $print2], 10, true);Yii::$app->end();
    }

    public function actionRoutes(){
        $model = new Route();
        $model->invalidate();
        $print = $model->getRoutes();
        BaseVarDumper::dump($print, 10, true);Yii::$app->end();
        //Yii::$app->getResponse()->format = 'json';
        //return $model->getRoutes();
    }

    public function actionSendMail(){
        $user = Yii::$app->user->identity;
        $mailSent = Yii::$app
            ->mailer_pmc
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['pmcEmail'] => Yii::$app->name])
            ->setTo('lifelinejar@gmail.com')
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
        BaseVarDumper::dump($mailSent, 10, true);
    }

    public function actionSqlCommand(){
        /*$add = 100;
        $id = '251';
        $command = Yii::$app->db->createCommand('UPDATE mst_greige SET stock_pfp = stock_pfp + '.$add.' WHERE id=:id')
            ->bindParam(':id', $id);
        $res = $command->execute();
        BaseVarDumper::dump($res, 10, true);*/

        $q = '';
        $query = new Query;
        $query
            //->select(new Expression('id, concat(full_name, \' (\', email, \')\') "text"'))
            //->select()
            ->from('member')
            ->join('left', '')
            ->where(['ilike', 'full_name', $q])
            ->orWhere(['ilike', 'email', $q])
            ->limit(20);
        $command = $query->createCommand();
        $data = $command->queryAll();
    }

    public function actionTest(){
        $query = TrnKartuProsesDyeing::find()
            ->joinWith('wo')
            ->select(new Expression('trn_kartu_proses_dyeing.id, trn_kartu_proses_dyeing.no "name", trn_wo.id wo_id, trn_wo.no "wo_no"'))
            ->andWhere(['trn_kartu_proses_dyeing.status'=>TrnKartuProsesDyeing::STATUS_APPROVED])
            ->limit(20)
            ->asArray()
        ;
        $out = $query->all();
        $print = $out;
        BaseVarDumper::dump($print, 10, true);
    }

    public function actionNoUrut(){
        $print = TrnWoMemo::find()
            ->select('no_urut')
            ->where(['tahun'=>date("Y")])
            //->where(['>=', 'created_at', strtotime(date('Y'))])
            ->orderBy('id desc')
            ->scalar();

        //BaseVarDumper::dump($print, 10, true);

        return $this->render('print', [
            'data'=>[
                $print
            ]
        ]);
    }
}
