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
        $prosesDyeingConfig = [];
        $prosesPfpConfig = [];
        if ($jenis_mesin) {
            $dyeingModels = \common\models\ar\MstProcessDyeing::find()
                ->innerJoin('mst_process_dyeing_mesin', 'mst_process_dyeing_mesin.mst_process_dyeing_id = mst_process_dyeing.id')
                ->innerJoin('mst_mesin_proses', 'mst_mesin_proses.id = mst_process_dyeing_mesin.mst_mesin_proses_id')
                ->where(['mst_mesin_proses.model_mesin' => $jenis_mesin])
                ->all();
            foreach ($dyeingModels as $model) {
                $prosesDyeing[$model->nama_proses] = $model->nama_proses;
                $prosesDyeingConfig[$model->nama_proses] = $model->attributes;
            }

            $pfpModels = \common\models\ar\MstProcessPfp::find()
                ->innerJoin('mst_process_pfp_mesin', 'mst_process_pfp_mesin.mst_process_pfp_id = mst_process_pfp.id')
                ->innerJoin('mst_mesin_proses', 'mst_mesin_proses.id = mst_process_pfp_mesin.mst_mesin_proses_id')
                ->where(['mst_mesin_proses.model_mesin' => $jenis_mesin])
                ->all();
            foreach ($pfpModels as $model) {
                $prosesPfp[$model->nama_proses] = $model->nama_proses;
                $prosesPfpConfig[$model->nama_proses] = $model->attributes;
            }
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
            'prosesDyeingConfig' => $prosesDyeingConfig,
            'prosesPfpConfig' => $prosesPfpConfig,
        ]);
    }

    public function actionSaveInput()
    {
        $request = Yii::$app->request;
        $jenis_mesin = $request->post('jenis_mesin');
        $tanggal = $request->post('tanggal');
        $shift = $request->post('shift');
        $no_mesin = $request->post('no_mesin', []);

        $inputDyeing = $request->post('InputDyeing', []);
        $inputPfp = $request->post('InputPfp', []);

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            // Save InputDyeing
            if (is_array($inputDyeing)) {
                foreach ($inputDyeing as $row) {
                    if (!empty($row['nk']) && !empty($row['proses'])) {
                        // Find process ID
                        $mstProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => $row['proses']]);
                        if ($mstProcess) {
                            $kpProcess = \common\models\ar\KartuProcessDyeingProcess::findOne([
                                'kartu_process_id' => $row['nk'],
                                'process_id' => $mstProcess->id
                            ]);
                            
                            if (!$kpProcess) {
                                $kpProcess = new \common\models\ar\KartuProcessDyeingProcess();
                                $kpProcess->kartu_process_id = $row['nk'];
                                $kpProcess->process_id = $mstProcess->id;
                            }
                            
                            $val = [];
                            if ($kpProcess->value) {
                                $val = json_decode($kpProcess->value, true) ?: [];
                            }
                            
                            // Update values
                            $val['tanggal'] = $tanggal;
                            $val['shift_group'] = $shift;
                            
                            $fields = ['start', 'stop', 'no_mesin', 'temp', 'speed', 'gramasi', 'program_number', 'density', 'over_feed', 'lebar_jadi', 'panjang_jadi', 'info_kualitas', 'gangguan_produksi', 'keterangan'];
                            foreach ($fields as $f) {
                                if (isset($row[$f])) {
                                    $val[$f] = $row[$f];
                                }
                            }
                            
                            $kpProcess->value = json_encode($val);
                            if (!$kpProcess->save(false)) {
                                throw new \Exception('Gagal menyimpan data Dyeing.');
                            }
                        }
                    }
                }
            }

            // Save InputPfp
            if (is_array($inputPfp)) {
                foreach ($inputPfp as $row) {
                    if (!empty($row['nk']) && !empty($row['proses'])) {
                        // Find process ID
                        $mstProcess = \common\models\ar\MstProcessPfp::findOne(['nama_proses' => $row['proses']]);
                        if ($mstProcess) {
                            $kpProcess = \common\models\ar\KartuProcessPfpProcess::findOne([
                                'kartu_process_id' => $row['nk'],
                                'process_id' => $mstProcess->id
                            ]);
                            
                            if (!$kpProcess) {
                                $kpProcess = new \common\models\ar\KartuProcessPfpProcess();
                                $kpProcess->kartu_process_id = $row['nk'];
                                $kpProcess->process_id = $mstProcess->id;
                            }
                            
                            $val = [];
                            if ($kpProcess->value) {
                                $val = json_decode($kpProcess->value, true) ?: [];
                            }
                            
                            // Update values
                            $val['tanggal'] = $tanggal;
                            $val['shift_group'] = $shift;
                            
                            $fields = ['start', 'stop', 'no_mesin', 'temp', 'speed', 'waktu', 'program_number', 'ex_relax', 'ex_wr_oligomer', 'ex_dyeing', 'wr_pcnt', 'rpm', 'density', 'jamur', 'karat', 'over_feed', 'counter', 'lebar_jadi', 'info_kualitas', 'gangguan_produksi', 'gramasi', 'panjang_jadi', 'keterangan'];
                            foreach ($fields as $f) {
                                if (isset($row[$f])) {
                                    $val[$f] = $row[$f];
                                }
                            }
                            
                            $kpProcess->value = json_encode($val);
                            if (!$kpProcess->save(false)) {
                                throw new \Exception('Gagal menyimpan data PFP.');
                            }
                        }
                    }
                }
            }
            
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Data Tambahan Input berhasil disimpan.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index', 'jenis_mesin' => $jenis_mesin, 'tanggal' => $tanggal, 'shift' => $shift, 'no_mesin' => $no_mesin]);
    }
}
