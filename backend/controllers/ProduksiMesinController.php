<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\ar\MstMesinProses;

class ProduksiMesinController extends Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $jenis_mesin = $request->get('jenis_mesin');
        $no_mesin = $request->get('no_mesin'); // array of machine ids/names
        $tanggal = $request->get('tanggal');
        $shift = $request->get('shift');

        $jenisMesins = MstMesinProses::find()->select('model_mesin')->distinct()->where(['not', ['model_mesin' => null]])->column();
        $mesins = MstMesinProses::find()->all();
        
        $dyeingRecords = [];
        $pfpRecords = [];

        if ($jenis_mesin && $no_mesin && $tanggal && $shift) {
            // Dyeing
            $queryDyeing = \common\models\ar\KartuProcessDyeingProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_dyeing kpd', 'kp.kartu_process_id = kpd.id')
                ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesDyeing::STATUS_DELIVERED])
                ->andWhere(['not', ['kpd.status' => \common\models\ar\TrnKartuProsesDyeing::STATUS_BATAL]])
                ->andWhere(['like', 'kp.value', '"tanggal":"' . $tanggal . '"'])
                ->andWhere(['like', 'kp.value', '"shift_group":"' . $shift . '"'])
                ->with(['kartuProcess.wo', 'kartuProcess.mo', 'kartuProcess.woColor', 'process']);

            $orConditionsDyeing = ['or'];
            foreach ((array)$no_mesin as $nm) {
                $orConditionsDyeing[] = ['like', 'kp.value', '"no_mesin":"' . str_replace(['%', '_'], ['\%', '\_'], $nm) . '"'];
            }
            if (count((array)$no_mesin) > 0) {
                $queryDyeing->andWhere($orConditionsDyeing);
            }
            $dyeingRecords = $queryDyeing->all();

            // PFP
            $queryPfp = \common\models\ar\KartuProcessPfpProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_pfp kpd', 'kp.kartu_process_id = kpd.id')
                ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesPfp::STATUS_DELIVERED])
                ->andWhere(['not', ['kpd.status' => \common\models\ar\TrnKartuProsesPfp::STATUS_GAGAL_PROSES]])
                ->andWhere(['like', 'kp.value', '"tanggal":"' . $tanggal . '"'])
                ->andWhere(['like', 'kp.value', '"shift_group":"' . $shift . '"'])
                ->with(['kartuProcess.wo', 'kartuProcess.mo', 'kartuProcess.woColor', 'process']);

            $orConditionsPfp = ['or'];
            foreach ((array)$no_mesin as $nm) {
                $orConditionsPfp[] = ['like', 'kp.value', '"no_mesin":"' . str_replace(['%', '_'], ['\%', '\_'], $nm) . '"'];
            }
            if (count((array)$no_mesin) > 0) {
                $queryPfp->andWhere($orConditionsPfp);
            }
            $pfpRecords = $queryPfp->all();
        }

        $prosesDyeing = [];
        $prosesPfp = [];
        if ($jenis_mesin) {
            $prosesDyeing = \common\models\ar\MstProcessDyeing::find()
                ->innerJoin('mst_process_dyeing_mesin', 'mst_process_dyeing_mesin.mst_process_dyeing_id = mst_process_dyeing.id')
                ->innerJoin('mst_mesin_proses', 'mst_mesin_proses.id = mst_process_dyeing_mesin.mst_mesin_proses_id')
                ->where(['mst_mesin_proses.model_mesin' => $jenis_mesin])
                ->select(['mst_process_dyeing.nama_proses', 'mst_process_dyeing.nama_proses'])
                ->indexBy('nama_proses')
                ->column();

            $prosesPfp = \common\models\ar\MstProcessPfp::find()
                ->innerJoin('mst_process_pfp_mesin', 'mst_process_pfp_mesin.mst_process_pfp_id = mst_process_pfp.id')
                ->innerJoin('mst_mesin_proses', 'mst_mesin_proses.id = mst_process_pfp_mesin.mst_mesin_proses_id')
                ->where(['mst_mesin_proses.model_mesin' => $jenis_mesin])
                ->select(['mst_process_pfp.nama_proses', 'mst_process_pfp.nama_proses'])
                ->indexBy('nama_proses')
                ->column();
        }

        return $this->render('index', [
            'jenisMesins' => $jenisMesins,
            'mesins' => $mesins,
            'jenis_mesin' => $jenis_mesin,
            'no_mesin' => $no_mesin,
            'tanggal' => $tanggal,
            'shift' => $shift,
            'dyeingRecords' => $dyeingRecords,
            'pfpRecords' => $pfpRecords,
            'prosesDyeing' => $prosesDyeing,
            'prosesPfp' => $prosesPfp,
        ]);
    }
}
