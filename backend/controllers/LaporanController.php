<?php

namespace backend\controllers;

use common\models\ar\TrnKartuProsesDyeingSearch;
use common\models\ar\TrnKartuProsesPfpSearch;
use Yii;
use yii\web\Controller;

class LaporanController extends Controller
{
    /**
     * Lists all TrnKartuProsesDyeing models.
     * @return mixed
     */    public function actionPrintPersiapanGabungan()
    {
        $params = Yii::$app->request->queryParams;
        $searchModelDyeing = new \common\models\ar\TrnKartuProsesDyeingSearch();
        $searchModelPfp = new \common\models\ar\TrnKartuProsesPfpSearch();
        
        $mcFilter = isset($params['mcFilter']) ? $params['mcFilter'] : [];
        if (!is_array($mcFilter) && $mcFilter !== '') {
            $mcFilter = [$mcFilter];
        }
        $shiftPagiFilter = isset($params['shiftPagiFilter']) ? $params['shiftPagiFilter'] : null;
        $shiftSiangFilter = isset($params['shiftSiangFilter']) ? $params['shiftSiangFilter'] : null;
        $tanggalFilter = isset($params['tanggalFilter']) ? $params['tanggalFilter'] : date('Y-m-d');
        
        // --- DYEING ---
        $dataProviderDyeing = $searchModelDyeing->search($params);
        $queryDyeing = $dataProviderDyeing->query->limit(2000);
        if (!empty($mcFilter) || !empty($shiftPagiFilter) || !empty($shiftSiangFilter) || !empty($tanggalFilter)) {
            $queryDyeing->innerJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
            if (!empty($mcFilter)) {
                $namaMesins = \common\models\ar\MstMesinProses::find()->select('nama_mesin')->where(['model_mesin' => $mcFilter])->column();
                $mcConditions = ['or'];
                if (!empty($namaMesins)) {
                    foreach ($namaMesins as $nm) {
                        $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $nm . '"'];
                    }
                }
                foreach ($mcFilter as $mc) {
                    $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $mc . '"'];
                }
                $queryDyeing->andWhere($mcConditions);
            }
            if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
                $shiftConditions = ['or'];
                if (!empty($shiftPagiFilter)) $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftPagiFilter . '"'];
                if (!empty($shiftSiangFilter)) $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftSiangFilter . '"'];
                $queryDyeing->andWhere($shiftConditions);
            }
            if (!empty($tanggalFilter)) {
                $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
                $dates = explode(' - ', $normalizedFilter);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $dateExpr = "split_part(split_part(kpdp.value, '\"tanggal\":\"', 2), '\"', 1)";
                    $queryDyeing->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                    $queryDyeing->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
                } else {
                    $queryDyeing->andWhere(['like', 'kpdp.value', '"tanggal":"' . $tanggalFilter . '"']);
                }
            }
        } else {
            $queryDyeing->leftJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
        }
        
        // --- PFP ---
        $dataProviderPfp = $searchModelPfp->search($params);
        $queryPfp = $dataProviderPfp->query->limit(2000);
        $processIdPfp = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar() ?: 1;
        
        if (!empty($mcFilter) || !empty($shiftPagiFilter) || !empty($shiftSiangFilter) || !empty($tanggalFilter)) {
            $queryPfp->innerJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = '.$processIdPfp);
            if (!empty($mcFilter)) {
                $namaMesins = \common\models\ar\MstMesinProses::find()->select('nama_mesin')->where(['model_mesin' => $mcFilter])->column();
                $mcConditions = ['or'];
                if (!empty($namaMesins)) {
                    foreach ($namaMesins as $nm) {
                        $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $nm . '"'];
                    }
                }
                foreach ($mcFilter as $mc) {
                    $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $mc . '"'];
                }
                $queryPfp->andWhere($mcConditions);
            }
            if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
                $shiftConditions = ['or'];
                if (!empty($shiftPagiFilter)) $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftPagiFilter . '"'];
                if (!empty($shiftSiangFilter)) $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftSiangFilter . '"'];
                $queryPfp->andWhere($shiftConditions);
            }
            if (!empty($tanggalFilter)) {
                $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
                $dates = explode(' - ', $normalizedFilter);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $dateExpr = "split_part(split_part(kppp.value, '\"tanggal\":\"', 2), '\"', 1)";
                    $queryPfp->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                    $queryPfp->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
                } else {
                    $queryPfp->andWhere(['like', 'kppp.value', '"tanggal":"' . $tanggalFilter . '"']);
                }
            }
        } else {
            $queryPfp->leftJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = '.$processIdPfp);
        }
        
        $dataProviderDyeing->pagination = false;
        $dataProviderPfp->pagination = false;
        
        $shiftOrders = [];
        $allModels = [];
        foreach ($dataProviderDyeing->getModels() as $model) {
            $model->tipe_laporan = 'Dyeing';
            
            $json = [];
            $kpdp = $model->getKartuProcessDyeingProcesses()->where(['process_id'=>1])->one();
            if ($kpdp) {
                try { $json = \yii\helpers\Json::decode($kpdp->value); } catch (\Throwable $t) {}
            }
            $shift = !empty($json['shift_group']) ? $json['shift_group'] : '-';
            
            $shift_order = 3;
            if ($shiftPagiFilter && $shift === $shiftPagiFilter) $shift_order = 1;
            elseif ($shiftSiangFilter && $shift === $shiftSiangFilter) $shift_order = 2;
            $shiftOrders[spl_object_hash($model)] = $shift_order;
            
            $model->gabungan_tanggal_sort = !empty($json['tanggal']) ? date('Y-m-d', strtotime($json['tanggal'])) : ($model->tanggalKartuProcessDyeingProcess ? date('Y-m-d', strtotime($model->tanggalKartuProcessDyeingProcess)) : '-');
            
            $allModels[] = $model;
        }
        foreach ($dataProviderPfp->getModels() as $model) {
            $model->tipe_laporan = 'PFP';
            
            $processIdPfp = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1;
            $json = [];
            $kpdp = $model->getKartuProcessPfpProcesses()->where(['process_id'=>$processIdPfp])->one();
            if ($kpdp) {
                try { $json = \yii\helpers\Json::decode($kpdp->value); } catch (\Throwable $t) {}
            }
            $shift = !empty($json['shift_operator']) ? $json['shift_operator'] : '-';
            
            $shift_order = 3;
            if ($shiftPagiFilter && $shift === $shiftPagiFilter) $shift_order = 1;
            elseif ($shiftSiangFilter && $shift === $shiftSiangFilter) $shift_order = 2;
            $shiftOrders[spl_object_hash($model)] = $shift_order;
            
            $model->gabungan_tanggal_sort = !empty($json['tanggal']) ? date('Y-m-d', strtotime($json['tanggal'])) : ($model->tanggalKartuProcessPfpProcess ? date('Y-m-d', strtotime($model->tanggalKartuProcessPfpProcess)) : '-');
            
            $allModels[] = $model;
        }
        
        usort($allModels, function($a, $b) use ($shiftOrders) {
            if ($a->gabungan_tanggal_sort == $b->gabungan_tanggal_sort) {
                $aOrder = isset($shiftOrders[spl_object_hash($a)]) ? $shiftOrders[spl_object_hash($a)] : 3;
                $bOrder = isset($shiftOrders[spl_object_hash($b)]) ? $shiftOrders[spl_object_hash($b)] : 3;
                return $aOrder <=> $bOrder;
            }
            return strcmp($a->gabungan_tanggal_sort, $b->gabungan_tanggal_sort);
        });
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $allModels,
            'pagination' => false,
        ]);
        
        $content = $this->renderPartial('print-persiapan-gabungan', [
            'dataProvider' => $dataProvider,
            'mcFilter' => $mcFilter,
            'shiftPagiFilter' => $shiftPagiFilter,
            'shiftSiangFilter' => $shiftSiangFilter,
            'tanggalFilter' => $tanggalFilter,
        ]);
        
        $title = 'LAPORAN HARIAN PERSIAPAN GABUNGAN';
        $shiftStr = [];
        if (!empty($shiftPagiFilter)) {
            $shiftStr[] = $shiftPagiFilter . ' (Pagi)';
        }
        if (!empty($shiftSiangFilter)) {
            $shiftStr[] = $shiftSiangFilter . ' (Siang)';
        }
        if (!empty($shiftStr)) {
            $title .= ' SHIFT: ' . implode(' DAN ', $shiftStr);
        }
        
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_BLANK,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
                .row {
                    margin: 0px 0px 0px 0px !important;
                    padding: 0px !important;
                }
                
                .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
                    border:0;
                    padding:5px 0 5px 0;
                    margin-left:-0.00001;
                }
                body, .row, .col-xs-12 { background-color: #fff; color: #000; }
                table { page-break-inside: auto; background-color: #fff; color: #000; }
                tr { page-break-inside: avoid; page-break-after: auto; }
            ',
            'options' => ['title' => 'Laporan Harian Persiapan Gabungan'],
            'methods' => [
                'SetTitle' => $title,
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        
        return $pdf->render();
    }

    public function actionPersiapanGabungan()
    {
        $searchModelDyeing = new \common\models\ar\TrnKartuProsesDyeingSearch();
        $searchModelPfp = new \common\models\ar\TrnKartuProsesPfpSearch();
        
        $params = Yii::$app->request->queryParams;
        $mcFilter = isset($params['mcFilter']) ? $params['mcFilter'] : [];
        if (!is_array($mcFilter) && $mcFilter !== '') {
            $mcFilter = [$mcFilter];
        }
        $shiftPagiFilter = isset($params['shiftPagiFilter']) ? $params['shiftPagiFilter'] : null;
        $shiftSiangFilter = isset($params['shiftSiangFilter']) ? $params['shiftSiangFilter'] : null;
        $tanggalFilter = isset($params['tanggalFilter']) ? $params['tanggalFilter'] : date('Y-m-d');
        
        // --- DYEING ---
        $dataProviderDyeing = $searchModelDyeing->search($params);
        $queryDyeing = $dataProviderDyeing->query->limit(2000);
        if (!empty($mcFilter) || !empty($shiftPagiFilter) || !empty($shiftSiangFilter) || !empty($tanggalFilter)) {
            $queryDyeing->innerJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
            if (!empty($mcFilter)) {
                $namaMesins = \common\models\ar\MstMesinProses::find()->select('nama_mesin')->where(['model_mesin' => $mcFilter])->column();
                $mcConditions = ['or'];
                if (!empty($namaMesins)) {
                    foreach ($namaMesins as $nm) {
                        $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $nm . '"'];
                    }
                }
                foreach ($mcFilter as $mc) {
                    $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $mc . '"'];
                }
                $queryDyeing->andWhere($mcConditions);
            }
            if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
                $shiftConditions = ['or'];
                if (!empty($shiftPagiFilter)) $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftPagiFilter . '"'];
                if (!empty($shiftSiangFilter)) $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftSiangFilter . '"'];
                $queryDyeing->andWhere($shiftConditions);
            }
            if (!empty($tanggalFilter)) {
                $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
                $dates = explode(' - ', $normalizedFilter);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $dateExpr = "split_part(split_part(kpdp.value, '\"tanggal\":\"', 2), '\"', 1)";
                    $queryDyeing->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                    $queryDyeing->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
                } else {
                    $queryDyeing->andWhere(['like', 'kpdp.value', '"tanggal":"' . $tanggalFilter . '"']);
                }
            }
        } else {
            $queryDyeing->leftJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
        }
        
        // --- PFP ---
        $dataProviderPfp = $searchModelPfp->search($params);
        $queryPfp = $dataProviderPfp->query->limit(2000);
        $processIdPfp = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar() ?: 1;
        
        if (!empty($mcFilter) || !empty($shiftPagiFilter) || !empty($shiftSiangFilter) || !empty($tanggalFilter)) {
            $queryPfp->innerJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = '.$processIdPfp);
            if (!empty($mcFilter)) {
                $namaMesins = \common\models\ar\MstMesinProses::find()->select('nama_mesin')->where(['model_mesin' => $mcFilter])->column();
                $mcConditions = ['or'];
                if (!empty($namaMesins)) {
                    foreach ($namaMesins as $nm) {
                        $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $nm . '"'];
                    }
                }
                foreach ($mcFilter as $mc) {
                    $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $mc . '"'];
                }
                $queryPfp->andWhere($mcConditions);
            }
            if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
                $shiftConditions = ['or'];
                if (!empty($shiftPagiFilter)) $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftPagiFilter . '"'];
                if (!empty($shiftSiangFilter)) $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftSiangFilter . '"'];
                $queryPfp->andWhere($shiftConditions);
            }
            if (!empty($tanggalFilter)) {
                $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
                $dates = explode(' - ', $normalizedFilter);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $dateExpr = "split_part(split_part(kppp.value, '\"tanggal\":\"', 2), '\"', 1)";
                    $queryPfp->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                    $queryPfp->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
                } else {
                    $queryPfp->andWhere(['like', 'kppp.value', '"tanggal":"' . $tanggalFilter . '"']);
                }
            }
        } else {
            $queryPfp->leftJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = '.$processIdPfp);
        }
        
        $dataProviderDyeing->pagination = false;
        $dataProviderPfp->pagination = false;
        
        $filterModel = new \yii\base\DynamicModel(['gabungan_shift', 'gabungan_tanggal', 'gabungan_nomor_wo', 'nomor_kartu']);
        $filterModel->addRule(['gabungan_shift', 'gabungan_tanggal', 'gabungan_nomor_wo', 'nomor_kartu'], 'string');
        $filterModel->load($params);

        $shiftOrders = [];
        $allModels = [];
        foreach ($dataProviderDyeing->getModels() as $model) {
            $model->tipe_laporan = 'Dyeing';
            
            $json = [];
            $kpdp = $model->getKartuProcessDyeingProcesses()->where(['process_id'=>1])->one();
            if ($kpdp) {
                try { $json = \yii\helpers\Json::decode($kpdp->value); } catch (\Throwable $t) {}
            }
            $shift = !empty($json['shift_group']) ? $json['shift_group'] : '-';
            $tanggal = !empty($json['tanggal']) ? date('j M Y', strtotime($json['tanggal'])) : ($model->tanggalKartuProcessDyeingProcess ? date('j M Y', strtotime($model->tanggalKartuProcessDyeingProcess)) : '-');
            $nomor_wo = $model->wo ? $model->wo->no : '-';
            
            // Apply inline filter
            if ($filterModel->gabungan_shift && stripos($shift, $filterModel->gabungan_shift) === false) continue;
            if ($filterModel->gabungan_tanggal && stripos($tanggal, $filterModel->gabungan_tanggal) === false) continue;
            if ($filterModel->gabungan_nomor_wo && stripos($nomor_wo, $filterModel->gabungan_nomor_wo) === false) continue;
            if ($filterModel->nomor_kartu && stripos($model->nomor_kartu, $filterModel->nomor_kartu) === false) continue;

            $model->gabungan_shift = $shift;
            $model->gabungan_tanggal = $tanggal;
            $model->gabungan_tanggal_sort = !empty($json['tanggal']) ? date('Y-m-d', strtotime($json['tanggal'])) : ($model->tanggalKartuProcessDyeingProcess ? date('Y-m-d', strtotime($model->tanggalKartuProcessDyeingProcess)) : '-');
            
            $shift_order = 3;
            if ($shiftPagiFilter && $shift === $shiftPagiFilter) $shift_order = 1;
            elseif ($shiftSiangFilter && $shift === $shiftSiangFilter) $shift_order = 2;
            $shiftOrders[spl_object_hash($model)] = $shift_order;
            
            $allModels[] = $model;
        }
        foreach ($dataProviderPfp->getModels() as $model) {
            $model->tipe_laporan = 'PFP';
            
            $processIdPfp = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1;
            $json = [];
            $kpdp = $model->getKartuProcessPfpProcesses()->where(['process_id'=>$processIdPfp])->one();
            if ($kpdp) {
                try { $json = \yii\helpers\Json::decode($kpdp->value); } catch (\Throwable $t) {}
            }
            $shift = !empty($json['shift_operator']) ? $json['shift_operator'] : '-';
            $tanggal = !empty($json['tanggal']) ? date('j M Y', strtotime($json['tanggal'])) : ($model->tanggalKartuProcessPfpProcess ? date('j M Y', strtotime($model->tanggalKartuProcessPfpProcess)) : '-');
            $nomor_wo = $model->orderPfp ? $model->orderPfp->no : '-';
            
            // Apply inline filter
            if ($filterModel->gabungan_shift && stripos($shift, $filterModel->gabungan_shift) === false) continue;
            if ($filterModel->gabungan_tanggal && stripos($tanggal, $filterModel->gabungan_tanggal) === false) continue;
            if ($filterModel->gabungan_nomor_wo && stripos($nomor_wo, $filterModel->gabungan_nomor_wo) === false) continue;
            if ($filterModel->nomor_kartu && stripos($model->nomor_kartu, $filterModel->nomor_kartu) === false) continue;

            $model->gabungan_shift = $shift;
            $model->gabungan_tanggal = $tanggal;
            $model->gabungan_tanggal_sort = !empty($json['tanggal']) ? date('Y-m-d', strtotime($json['tanggal'])) : ($model->tanggalKartuProcessPfpProcess ? date('Y-m-d', strtotime($model->tanggalKartuProcessPfpProcess)) : '-');
            
            $shift_order = 3;
            if ($shiftPagiFilter && $shift === $shiftPagiFilter) $shift_order = 1;
            elseif ($shiftSiangFilter && $shift === $shiftSiangFilter) $shift_order = 2;
            $shiftOrders[spl_object_hash($model)] = $shift_order;

            $allModels[] = $model;
        }
        
        usort($allModels, function($a, $b) use ($shiftOrders) {
            if ($a->gabungan_tanggal_sort == $b->gabungan_tanggal_sort) {
                $aOrder = isset($shiftOrders[spl_object_hash($a)]) ? $shiftOrders[spl_object_hash($a)] : 3;
                $bOrder = isset($shiftOrders[spl_object_hash($b)]) ? $shiftOrders[spl_object_hash($b)] : 3;
                return $aOrder <=> $bOrder;
            }
            return strcmp($a->gabungan_tanggal_sort, $b->gabungan_tanggal_sort);
        });
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $allModels,
            'pagination' => [
                'pageSize' => 50,
            ],
            // Removed default sort array so it preserves our custom sorting
        ]);
        
        $mesinOptions = [];
        $processDyeing = \common\models\ar\MstProcessDyeing::findOne(1);
        if ($processDyeing) {
            foreach ($processDyeing->mstMesinProseses as $mesin) {
                if (!empty($mesin->model_mesin)) $mesinOptions[$mesin->model_mesin] = $mesin->model_mesin;
            }
        }
        $processPfp = \common\models\ar\MstProcessPfp::find()->where(['order' => 1])->one();
        if ($processPfp) {
            foreach ($processPfp->mstMesinProseses as $mesin) {
                if (!empty($mesin->model_mesin)) $mesinOptions[$mesin->model_mesin] = $mesin->model_mesin;
            }
        }
        
        $mesinInputOptions = [];
        if ($processDyeing) {
            foreach ($processDyeing->mstMesinProseses as $mesin) {
                if (empty($mcFilter) || in_array($mesin->model_mesin, $mcFilter)) {
                    $mesinInputOptions[$mesin->nama_mesin] = $mesin->nama_mesin;
                }
            }
        }
        if ($processPfp) {
            foreach ($processPfp->mstMesinProseses as $mesin) {
                if (empty($mcFilter) || in_array($mesin->model_mesin, $mcFilter)) {
                    $mesinInputOptions[$mesin->nama_mesin] = $mesin->nama_mesin;
                }
            }
        }
        asort($mesinInputOptions);
        
        return $this->render('persiapan-gabungan', [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'mesinOptions' => $mesinOptions,
            'mcFilter' => $mcFilter,
            'shiftPagiFilter' => $shiftPagiFilter,
            'shiftSiangFilter' => $shiftSiangFilter,
            'tanggalFilter' => $tanggalFilter,
            'mesinInputOptions' => $mesinInputOptions,
        ]);
    }

    public function actionPersiapanDyeing()
    {
        $searchModel = new TrnKartuProsesDyeingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $mesinOptions = [];
        $process = \common\models\ar\MstProcessDyeing::findOne(1);
        if ($process) {
            foreach ($process->mstMesinProseses as $mesin) {
                if (!empty($mesin->model_mesin)) {
                    $mesinOptions[$mesin->model_mesin] = $mesin->model_mesin;
                }
            }
        }
        
        $params = Yii::$app->request->queryParams;
        $mcFilter = isset($params['mcFilter']) ? $params['mcFilter'] : [];
        if (!is_array($mcFilter) && $mcFilter !== '') {
            $mcFilter = [$mcFilter];
        }
        $shiftPagiFilter = isset($params['shiftPagiFilter']) ? $params['shiftPagiFilter'] : null;
        $shiftSiangFilter = isset($params['shiftSiangFilter']) ? $params['shiftSiangFilter'] : null;
        $tanggalFilter = isset($params['tanggalFilter']) ? $params['tanggalFilter'] : null;
        
        $mesinInputOptions = [];
        if ($process) {
            foreach ($process->mstMesinProseses as $mesin) {
                if (empty($mcFilter) || in_array($mesin->model_mesin, $mcFilter)) {
                    $mesinInputOptions[$mesin->nama_mesin] = $mesin->nama_mesin;
                }
            }
        }
        asort($mesinInputOptions);
        
        $query = $dataProvider->query;
        if (!empty($mcFilter) || !empty($shiftPagiFilter) || !empty($shiftSiangFilter) || !empty($tanggalFilter)) {
            $query->innerJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
            
            if (!empty($mcFilter)) {
                $namaMesins = \common\models\ar\MstMesinProses::find()
                    ->select('nama_mesin')
                    ->where(['model_mesin' => $mcFilter])
                    ->column();
                
                $mcConditions = ['or'];
                if (!empty($namaMesins)) {
                    foreach ($namaMesins as $nm) {
                        $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $nm . '"'];
                    }
                }
                foreach ($mcFilter as $mc) {
                    $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $mc . '"'];
                }
                $query->andWhere($mcConditions);
            }
            
            if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
                $shiftConditions = ['or'];
                if (!empty($shiftPagiFilter)) {
                    $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftPagiFilter . '"'];
                }
                if (!empty($shiftSiangFilter)) {
                    $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftSiangFilter . '"'];
                }
                $query->andWhere($shiftConditions);
            }
            
            if (!empty($tanggalFilter)) {
                $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
                $dates = explode(' - ', $normalizedFilter);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $dateExpr = "split_part(split_part(kpdp.value, '\"tanggal\":\"', 2), '\"', 1)";
                    $query->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                    $query->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
                } else {
                    $query->andWhere(['like', 'kpdp.value', '"tanggal":"' . $tanggalFilter . '"']);
                }
            }
        } else {
            $query->leftJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
        }
        
        $shiftSortExpr = "split_part(split_part(kpdp.value, '\"shift_group\":\"', 2), '\"', 1)";
        $dateSortExpr = "split_part(split_part(kpdp.value, '\"tanggal\":\"', 2), '\"', 1)";
        
        $dataProvider->sort->attributes['shift'] = [
            'asc' => [$shiftSortExpr => SORT_ASC],
            'desc' => [$shiftSortExpr => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['openDateRange'] = [
            'asc' => [$dateSortExpr => SORT_ASC],
            'desc' => [$dateSortExpr => SORT_DESC],
        ];
        
        // Set default order by date ascending (oldest first), then by shift
        if (empty(Yii::$app->request->queryParams['sort'])) {
            $query->orderBy([
                new \yii\db\Expression($dateSortExpr . " ASC"),
                new \yii\db\Expression($shiftSortExpr . " ASC"),
            ]);
        }

        return $this->render('persiapan-dyeing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mcFilter' => $mcFilter,
            'shiftPagiFilter' => $shiftPagiFilter,
            'shiftSiangFilter' => $shiftSiangFilter,
            'tanggalFilter' => $tanggalFilter,
            'mesinOptions' => $mesinOptions,
            'mesinInputOptions' => $mesinInputOptions,
        ]);
    }
    
    public function actionPrintPersiapanDyeing()
    {
        $tanggalFilter = Yii::$app->request->get('tanggalFilter');
        $shiftPagiFilter = Yii::$app->request->get('shiftPagiFilter');
        $shiftSiangFilter = Yii::$app->request->get('shiftSiangFilter');
        $mcFilter = Yii::$app->request->get('mcFilter', []);
        
        $query = \common\models\ar\TrnKartuProsesDyeing::find()
            ->joinWith(['wo.greige', 'woColor.moColor'])
            ->leftJoin('kartu_process_dyeing_process kpdp', 'kpdp.kartu_process_id = trn_kartu_proses_dyeing.id AND kpdp.process_id = 1');
            
        $query->where(['and', ['not', ['kpdp.value' => null]], ['!=', 'kpdp.value', '']]);
        
        if (!empty($mcFilter) && is_array($mcFilter)) {
            $namaMesins = \common\models\ar\MstMesinProses::find()->select('nama_mesin')->where(['model_mesin' => $mcFilter])->column();
            
            $mcConditions = ['or'];
            if (!empty($namaMesins)) {
                foreach ($namaMesins as $nm) {
                    $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $nm . '"'];
                }
            }
            foreach ($mcFilter as $mc) {
                $mcConditions[] = ['like', 'kpdp.value', '"no_mesin":"' . $mc . '"'];
            }
            $query->andWhere($mcConditions);
        }
        
        if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
            $shiftConditions = ['or'];
            if (!empty($shiftPagiFilter)) {
                $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftPagiFilter . '"'];
            }
            if (!empty($shiftSiangFilter)) {
                $shiftConditions[] = ['like', 'kpdp.value', '"shift_group":"' . $shiftSiangFilter . '"'];
            }
            $query->andWhere($shiftConditions);
        }
        
        if (!empty($tanggalFilter)) {
            $normalizedFilter = str_replace(' to ', ' - ', $tanggalFilter);
            $dates = explode(' - ', $normalizedFilter);
            if (count($dates) == 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $dateExpr = "split_part(split_part(kpdp.value, '\"tanggal\":\"', 2), '\"', 1)";
                $query->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                $query->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
            } else {
                $query->andWhere(['like', 'kpdp.value', '"tanggal":"' . $tanggalFilter . '"']);
            }
        }
        
        $dateSortExpr = "split_part(split_part(kpdp.value, '\"tanggal\":\"', 2), '\"', 1)";
        $shiftSortExpr = "split_part(split_part(kpdp.value, '\"shift_group\":\"', 2), '\"', 1)";
        $query->orderBy([
            new \yii\db\Expression($dateSortExpr . " ASC"),
            new \yii\db\Expression($shiftSortExpr . " ASC"),
        ]);
        
        $models = $query->all();
        
        $content = $this->renderPartial('print-persiapan-dyeing', [
            'models' => $models,
            'tanggalFilter' => $tanggalFilter,
            'shiftPagiFilter' => $shiftPagiFilter,
            'shiftSiangFilter' => $shiftSiangFilter,
        ]);
        
        $title = 'LAPORAN HARIAN PERSIAPAN DYEING';
        $shiftStr = [];
        if (!empty($shiftPagiFilter)) {
            $shiftStr[] = $shiftPagiFilter . ' (Pagi)';
        }
        if (!empty($shiftSiangFilter)) {
            $shiftStr[] = $shiftSiangFilter . ' (Siang)';
        }
        if (!empty($shiftStr)) {
            $title .= ' SHIFT: ' . implode(' DAN ', $shiftStr);
        }
        
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_BLANK,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
                .row {
                    margin: 0px 0px 0px 0px !important;
                    padding: 0px !important;
                }
                
                .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
                    border:0;
                    padding:5px 0 5px 0;
                    margin-left:-0.00001;
                }
                body, .row, .col-xs-12 { background-color: #fff; color: #000; }
                table { page-break-inside: auto; background-color: #fff; color: #000; }
                tr { page-break-inside: avoid; page-break-after: auto; }
            ',
            'options' => ['title' => 'Laporan Harian Persiapan Dyeing'],
            'methods' => [
                'SetTitle' => $title,
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        
        return $pdf->render();
    }

    public function actionUpdatePersiapanDyeing()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post('Inputan');
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $kartuProcessId = isset($item['kartu_process_id']) ? $item['kartu_process_id'] : null;
                    
                    if (empty($kartuProcessId) && !empty($item['nomor_kartu'])) {
                        $kp = \common\models\ar\TrnKartuProsesDyeing::find()
                            ->where(['nomor_kartu' => trim($item['nomor_kartu'])])
                            ->orderBy(['is_redyeing' => SORT_ASC, 'id' => SORT_DESC])
                            ->one();
                        if ($kp) {
                            $kartuProcessId = $kp->id;
                        } else {
                            Yii::$app->session->addFlash('error', 'Nomor Kartu ' . $item['nomor_kartu'] . ' tidak ditemukan.');
                            continue;
                        }
                    }
                    
                    if (empty($kartuProcessId)) continue;
                    
                    $kpdp = \common\models\ar\KartuProcessDyeingProcess::findOne([
                        'kartu_process_id' => $kartuProcessId,
                        'process_id' => 1
                    ]);
                    
                    if (!$kpdp) {
                        $kpdp = new \common\models\ar\KartuProcessDyeingProcess();
                        $kpdp->kartu_process_id = $kartuProcessId;
                        $kpdp->process_id = 1;
                        $kpdp->value = '{}';
                    }
                    
                    $json = \yii\helpers\Json::decode($kpdp->value);
                    if (!is_array($json)) $json = [];
                    
                    if (isset($item['tanggal'])) {
                        $json['tanggal'] = $item['tanggal'];
                    }
                    if (isset($item['no_mesin'])) {
                        $json['no_mesin'] = $item['no_mesin'];
                    }
                    if (isset($item['shift_operator'])) {
                        $json['shift_operator'] = $item['shift_operator'];
                    }
                    if (isset($item['keterangan'])) {
                        $json['keterangan'] = $item['keterangan'];
                    }
                    
                    $kpdp->value = \yii\helpers\Json::encode($json);
                    $kpdp->save(false);
                }
                Yii::$app->session->setFlash('success', 'Data berhasil disimpan.');
            }
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['persiapan-dyeing']);
    }

    public function actionRekapBulananProcessing()
    {
        $bulan = Yii::$app->request->get('bulan', date('n'));
        $tahun = Yii::$app->request->get('tahun', date('Y'));
        
        $startDate = date('Y-m-d', strtotime("$tahun-$bulan-01"));
        $endDate = date('Y-m-t', strtotime("$tahun-$bulan-01"));
        
        // Initial data structure
        $data = [
            'hisaka_total_produksi' => 0, 'hisaka_jml_masuk_packing' => 0, 'hisaka_proses_celup' => 0,
            'hisaka_proses_dasar_celup' => 0, 'hisaka_proses_topping' => 0, 'hisaka_proses_level' => 0,
            'hisaka_proses_stripping' => 0, 'hisaka_proses_relax' => 0, 'hisaka_proses_pfp' => 0,
            'hisaka_hari_kerja' => 0, 'hisaka_total_batch' => 0, 'hisaka_rata_per_hari_batch' => 0,
            'hisaka_rata_per_hari_kartu' => 0, 'hisaka_rata_per_hari_jumbo' => 0,
            'fukushin_proses_continuous' => 0, 'fukushin_perbaikan_cuci_ulang' => 0,
            'steamer_proses_redox' => 0, 'steamer_proses_disperse' => 0,
            'padding_proses_dyeing' => 0, 'padding_proses_printing' => 0,
            'stenter_produksi_stenter3' => 0, 'stenter_produksi_stenter4' => 0,
            'stenter_produksi_stenter5' => 0, 'stenter_produksi_stenter6' => 0,
            'stenter_setting_set_dyeing' => 0, 'stenter_setting_set_ulang_dyeing' => 0,
            'stenter_setting_set_pfp' => 0, 'stenter_setting_set_ulang_pfp' => 0,
            'stenter_setting_set_non_wr' => 0,
            'stenter_resin_rf_dyeing' => 0, 'stenter_resin_rf_ulang_dyeing' => 0,
            'stenter_resin_rf_ulang_murni' => 0, 'stenter_resin_setelah_perbaikan' => 0,
            'stenter_resin_jet_black_ulang' => 0, 'stenter_resin_rf_printing' => 0,
            'stenter_resin_rf_ulang_printing' => 0,
            'packing_printing' => 0, 'packing_dyeing' => 0, 'packing_makloon' => 0,
            'packing_total_packing' => 0, 'packing_hasil_kirim' => 0
        ];
        
        $machineMap = \yii\helpers\ArrayHelper::index(\common\models\ar\MstMesinProses::find()->all(), 'nama_mesin');
        $dyeingProcessMap = \yii\helpers\ArrayHelper::map(\common\models\ar\MstProcessDyeing::find()->all(), 'id', 'nama_proses');
        $pfpProcessMap = \yii\helpers\ArrayHelper::map(\common\models\ar\MstProcessPfp::find()->all(), 'id', 'nama_proses');
        
        // 1. Process Dyeing Processes
        $monthStr = sprintf('%04d-%02d-', $tahun, $bulan);
        $qDyeing = \common\models\ar\KartuProcessDyeingProcess::find()
            ->alias('kp')
            ->innerJoin('trn_kartu_proses_dyeing kpd', 'kp.kartu_process_id = kpd.id')
            ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesDyeing::STATUS_DELIVERED])
            ->andWhere(['not', ['kpd.status' => \common\models\ar\TrnKartuProsesDyeing::STATUS_BATAL]])
            ->andWhere(['like', 'kp.value', '"tanggal":"' . $monthStr])
            ->with(['kartuProcess.trnKartuProsesDyeingItems'])
            ->all();

        $hisakaKartuIds = [];
        $hisakaJumboKartuIds = [];

        foreach ($qDyeing as $kp) {
            $val = \yii\helpers\Json::decode($kp->value);
            $noMesin = isset($val['no_mesin']) ? $val['no_mesin'] : null;
            if (!$noMesin) continue;
            
            $modelMesin = isset($machineMap[$noMesin]) ? $machineMap[$noMesin]->model_mesin : '';
            $processName = isset($dyeingProcessMap[$kp->process_id]) ? $dyeingProcessMap[$kp->process_id] : '';
            
            $panjang = 0;
            if ($kp->kartuProcess && $kp->kartuProcess->trnKartuProsesDyeingItems) {
                foreach ($kp->kartuProcess->trnKartuProsesDyeingItems as $item) {
                    $panjang += $item->panjang_m;
                }
            }

            if (stripos($modelMesin, 'Hisaka') !== false) {
                $data['hisaka_total_produksi'] += $panjang;
                $data['hisaka_total_batch'] += 1;
                $hisakaKartuIds[$kp->kartu_process_id] = true;
                if (stripos($modelMesin, 'Jumbo') !== false) {
                    $hisakaJumboKartuIds[$kp->kartu_process_id] = true;
                }

                if (stripos($processName, 'Dyeing') !== false || stripos($processName, 'Celup') !== false) {
                    $data['hisaka_proses_celup'] += $panjang;
                }
                if (stripos($processName, 'Dasar Celup') !== false) {
                    $data['hisaka_proses_dasar_celup'] += $panjang;
                }
                if (stripos($processName, 'Toping') !== false && stripos($processName, 'Level') === false) {
                    $data['hisaka_proses_topping'] += $panjang;
                }
                if (stripos($processName, 'Level') !== false) {
                    $data['hisaka_proses_level'] += $panjang;
                }
                if (stripos($processName, 'Stripping') !== false || stripos($processName, 'Cuci H2O2') !== false) {
                    $data['hisaka_proses_stripping'] += $panjang;
                }
                if (stripos($processName, 'Relax') !== false) {
                    $data['hisaka_proses_relax'] += $panjang;
                }
            }
            
            if (stripos($modelMesin, 'Fukushin') !== false || stripos($modelMesin, 'Continuous') !== false) {
                $data['fukushin_proses_continuous'] += $panjang;
                if (stripos($processName, 'Cuci') !== false || stripos($processName, 'Ulang') !== false) {
                    $data['fukushin_perbaikan_cuci_ulang'] += $panjang;
                }
            }
            
            if (stripos($modelMesin, 'Steamer') !== false) {
                if (stripos($processName, 'Redox') !== false) $data['steamer_proses_redox'] += $panjang;
                if (stripos($processName, 'Disperse') !== false) $data['steamer_proses_disperse'] += $panjang;
            }
            
            if (stripos($modelMesin, 'Padding') !== false) {
                $data['padding_proses_dyeing'] += $panjang; // Assuming Dyeing card = padding dyeing
            }
            
            if (stripos($modelMesin, 'Stenter') !== false) {
                if ($noMesin == 'Stenter 3') $data['stenter_produksi_stenter3'] += $panjang;
                if ($noMesin == 'Stenter 4') $data['stenter_produksi_stenter4'] += $panjang;
                if ($noMesin == 'Stenter 5') $data['stenter_produksi_stenter5'] += $panjang;
                if ($noMesin == 'Stenter 6') $data['stenter_produksi_stenter6'] += $panjang;

                if (stripos($processName, 'Setting') !== false) {
                    if (stripos($processName, 'Ulang') !== false || stripos($processName, 'Perbaikan') !== false) {
                        $data['stenter_setting_set_ulang_dyeing'] += $panjang;
                    } else {
                        $data['stenter_setting_set_dyeing'] += $panjang;
                    }
                }
                if (stripos($processName, 'Resin') !== false || stripos($processName, 'RF') !== false) {
                    if (stripos($processName, 'Ulang Murni') !== false) {
                        $data['stenter_resin_rf_ulang_murni'] += $panjang;
                    } elseif (stripos($processName, 'Ulang') !== false) {
                        $data['stenter_resin_rf_ulang_dyeing'] += $panjang;
                    } elseif (stripos($processName, 'Setelah Perbaikan') !== false) {
                        $data['stenter_resin_setelah_perbaikan'] += $panjang;
                    } elseif (stripos($processName, 'Jet Black Ulang') !== false || stripos($processName, 'Perbaikan JB') !== false) {
                        $data['stenter_resin_jet_black_ulang'] += $panjang;
                    } else {
                        $data['stenter_resin_rf_dyeing'] += $panjang;
                    }
                }
                if (stripos($processName, 'Non WR') !== false) {
                    $data['stenter_setting_set_non_wr'] += $panjang;
                }
            }
        }

        // 2. Process PFP Processes
        $qPfp = \common\models\ar\KartuProcessPfpProcess::find()
            ->alias('kp')
            ->innerJoin('trn_kartu_proses_pfp kpd', 'kp.kartu_process_id = kpd.id')
            ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesPfp::STATUS_DELIVERED])
            ->andWhere(['not', ['kpd.status' => \common\models\ar\TrnKartuProsesPfp::STATUS_GAGAL_PROSES]])
            ->andWhere(['like', 'kp.value', '"tanggal":"' . $monthStr])
            ->with(['kartuProcess.trnKartuProsesPfpItems'])
            ->all();

        foreach ($qPfp as $kp) {
            $val = \yii\helpers\Json::decode($kp->value);
            $noMesin = isset($val['no_mesin']) ? $val['no_mesin'] : null;
            if (!$noMesin) continue;
            
            $modelMesin = isset($machineMap[$noMesin]) ? $machineMap[$noMesin]->model_mesin : '';
            $processName = isset($pfpProcessMap[$kp->process_id]) ? $pfpProcessMap[$kp->process_id] : '';
            
            $panjang = 0;
            if ($kp->kartuProcess && $kp->kartuProcess->trnKartuProsesPfpItems) {
                foreach ($kp->kartuProcess->trnKartuProsesPfpItems as $item) {
                    $panjang += $item->panjang_m;
                }
            }

            if (stripos($modelMesin, 'Hisaka') !== false) {
                $data['hisaka_total_produksi'] += $panjang;
                $data['hisaka_proses_pfp'] += $panjang;
                $data['hisaka_total_batch'] += 1;
                $hisakaKartuIds[$kp->kartu_process_id . '_pfp'] = true;
                if (stripos($modelMesin, 'Jumbo') !== false) {
                    $hisakaJumboKartuIds[$kp->kartu_process_id . '_pfp'] = true;
                }
            }

            if (stripos($modelMesin, 'Stenter') !== false) {
                if ($noMesin == 'Stenter 3') $data['stenter_produksi_stenter3'] += $panjang;
                if ($noMesin == 'Stenter 4') $data['stenter_produksi_stenter4'] += $panjang;
                if ($noMesin == 'Stenter 5') $data['stenter_produksi_stenter5'] += $panjang;
                if ($noMesin == 'Stenter 6') $data['stenter_produksi_stenter6'] += $panjang;

                if (stripos($processName, 'Setting') !== false) {
                    if (stripos($processName, 'Ulang') !== false) {
                        $data['stenter_setting_set_ulang_pfp'] += $panjang;
                    } else {
                        $data['stenter_setting_set_pfp'] += $panjang;
                    }
                }
            }
        }
        
        // 3. Process Printing (if exists)
        if (class_exists('\common\models\ar\KartuProcessPrintingProcess')) {
            $printingProcessMap = \yii\helpers\ArrayHelper::map(\common\models\ar\MstProcessPrinting::find()->all(), 'id', 'nama_proses');
            $qPrint = \common\models\ar\KartuProcessPrintingProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_printing kpd', 'kp.kartu_process_id = kpd.id')
                ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesPrinting::STATUS_DELIVERED])
                ->andWhere(['like', 'kp.value', '"tanggal":"' . $monthStr])
                ->with(['kartuProcess.trnKartuProsesPrintingItems'])
                ->all();

            foreach ($qPrint as $kp) {
                $val = \yii\helpers\Json::decode($kp->value);
                $noMesin = isset($val['no_mesin']) ? $val['no_mesin'] : null;
                if (!$noMesin) continue;
                
                $modelMesin = isset($machineMap[$noMesin]) ? $machineMap[$noMesin]->model_mesin : '';
                $processName = isset($printingProcessMap[$kp->process_id]) ? $printingProcessMap[$kp->process_id] : '';
                
                $panjang = 0;
                if ($kp->kartuProcess && $kp->kartuProcess->trnKartuProsesPrintingItems) {
                    foreach ($kp->kartuProcess->trnKartuProsesPrintingItems as $item) {
                        $panjang += $item->panjang_m;
                    }
                }

                if (stripos($modelMesin, 'Padding') !== false) {
                    $data['padding_proses_printing'] += $panjang;
                }
                
                if (stripos($modelMesin, 'Stenter') !== false) {
                    if ($noMesin == 'Stenter 3') $data['stenter_produksi_stenter3'] += $panjang;
                    if ($noMesin == 'Stenter 4') $data['stenter_produksi_stenter4'] += $panjang;
                    if ($noMesin == 'Stenter 5') $data['stenter_produksi_stenter5'] += $panjang;
                    if ($noMesin == 'Stenter 6') $data['stenter_produksi_stenter6'] += $panjang;

                    if (stripos($processName, 'Resin') !== false || stripos($processName, 'RF') !== false) {
                        if (stripos($processName, 'Ulang') !== false) {
                            $data['stenter_resin_rf_ulang_printing'] += $panjang;
                        } else {
                            $data['stenter_resin_rf_printing'] += $panjang;
                        }
                    }
                }
            }
        }
        
        // 4. Gudang Jadi (Packing)
        $qPacking = \common\models\ar\TrnGudangJadi::find()
            ->where(['>=', 'status', \common\models\ar\TrnGudangJadi::STATUS_STOCK])
            ->andWhere(['between', 'date', $startDate, $endDate])
            ->with(['wo.scGreige'])
            ->all();
            
        foreach ($qPacking as $gj) {
            $panjang = 0;
            // Need to sum from items if available or just use qty
            $panjang += $gj->qty; // Assumed qty is meters
            
            if ($gj->source == \common\models\ar\TrnGudangJadi::SOURCE_MAKLOON_PROSES || $gj->source == \common\models\ar\TrnGudangJadi::SOURCE_MAKLOON_FINISH) {
                $data['packing_makloon'] += $panjang;
            } else {
                $process = ($gj->wo && $gj->wo->scGreige) ? $gj->wo->scGreige->process : null;
                if ($process == \common\models\ar\TrnScGreige::PROCESS_PRINTING) {
                    $data['packing_printing'] += $panjang;
                } else {
                    $data['packing_dyeing'] += $panjang;
                }
            }
            $data['packing_total_packing'] += $panjang;
            $data['hisaka_jml_masuk_packing'] += $panjang;
            $data['packing_hasil_kirim'] += $panjang;
        }

        // Calculations
        // Hari kerja in month (excluding Sundays)
        $workdays = 0;
        for ($i = 1; $i <= date('t', strtotime($startDate)); $i++) {
            if (date('N', strtotime("$tahun-$bulan-$i")) < 7) $workdays++;
        }
        $data['hisaka_hari_kerja'] = $workdays;
        
        if ($workdays > 0) {
            $data['hisaka_rata_per_hari_batch'] = round($data['hisaka_total_batch'] / $workdays);
            $data['hisaka_rata_per_hari_kartu'] = round(count($hisakaKartuIds) / $workdays);
            $data['hisaka_rata_per_hari_jumbo'] = round(count($hisakaJumboKartuIds) / $workdays);
        }

        return $this->render('rekap-bulanan-processing', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'data' => $data
        ]);
    }
    
    public function actionUpdatePersiapanGabungan()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post('Inputan');
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $kartuProcessId = isset($item['kartu_process_id']) ? $item['kartu_process_id'] : null;
                    $tipe = isset($item['tipe']) ? $item['tipe'] : null;
                    if (empty($kartuProcessId) || empty($tipe)) continue;
                    
                    if ($tipe === 'Dyeing') {
                        $kpdp = \common\models\ar\KartuProcessDyeingProcess::findOne([
                            'kartu_process_id' => $kartuProcessId,
                            'process_id' => 1
                        ]);
                        if (!$kpdp) {
                            $kpdp = new \common\models\ar\KartuProcessDyeingProcess();
                            $kpdp->kartu_process_id = $kartuProcessId;
                            $kpdp->process_id = 1;
                            $kpdp->value = '{}';
                        }
                        $json = \yii\helpers\Json::decode($kpdp->value);
                        if (!is_array($json)) $json = [];
                        if (isset($item['tanggal'])) $json['tanggal'] = $item['tanggal'];
                        if (isset($item['no_mesin'])) $json['no_mesin'] = $item['no_mesin'];
                        if (isset($item['shift_operator'])) $json['shift_operator'] = $item['shift_operator'];
                        if (isset($item['keterangan'])) $json['keterangan'] = $item['keterangan'];
                        $kpdp->value = \yii\helpers\Json::encode($json);
                        $kpdp->save(false);
                    } else if ($tipe === 'PFP') {
                        $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order'=>1])->scalar() ?: 1;
                        $kpdp = \common\models\ar\KartuProcessPfpProcess::findOne([
                            'kartu_process_id' => $kartuProcessId,
                            'process_id' => $processId
                        ]);
                        if (!$kpdp) {
                            $kpdp = new \common\models\ar\KartuProcessPfpProcess();
                            $kpdp->kartu_process_id = $kartuProcessId;
                            $kpdp->process_id = $processId;
                            $kpdp->value = '{}';
                        }
                        $json = \yii\helpers\Json::decode($kpdp->value);
                        if (!is_array($json)) $json = [];
                        if (isset($item['tanggal'])) $json['tanggal'] = $item['tanggal'];
                        if (isset($item['no_mesin'])) $json['no_mesin'] = $item['no_mesin'];
                        if (isset($item['shift_operator'])) $json['shift_operator'] = $item['shift_operator'];
                        if (isset($item['keterangan'])) $json['keterangan'] = $item['keterangan'];
                        $kpdp->value = \yii\helpers\Json::encode($json);
                        $kpdp->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Data berhasil disimpan.');
            }
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['persiapan-gabungan']);
    }

    public function actionGetInfoByWo($wo_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $wo = \common\models\ar\TrnWo::findOne($wo_id);
        if (!$wo) return ['success' => false];
        
        // $lusi and $pakan are not directly on TrnWo, but greige has it
        $motifName = $wo->greigeNamaKain ?? '';
        $motif = trim($motifName); // simplified motif for WO
        
        $colors = [];
        $nks = [];
        $kartuProses = \common\models\ar\TrnKartuProsesDyeing::find()
            ->with(['woColor.moColor', 'trnKartuProsesDyeingItems'])
            ->where(['wo_id' => $wo_id])
            ->all();
            
        foreach ($kartuProses as $kp) {
            $warnaName = $kp->woColor->moColor->color ?? '';
            if (!isset($colors[$warnaName])) {
                $colors[$warnaName] = $warnaName;
            }
            
            $panjangTotal = 0;
            $jumlahRoll = 0;
            foreach ($kp->trnKartuProsesDyeingItems as $item) {
                $jumlahRoll++;
                $panjangTotal += ($item->stock->panjang_m ?? 0);
            }
            
            $panjangTotal = number_format((float)$panjangTotal, 0);
            $berat = number_format((float)$kp->berat, 1);
            $pbg = $panjangTotal . ' / ' . $berat . ' / ' . $jumlahRoll;
            
            $hasData = false;
            $kpdp = \common\models\ar\KartuProcessDyeingProcess::findOne([
                'kartu_process_id' => $kp->id,
                'process_id' => 1
            ]);
            if ($kpdp) {
                $json = \yii\helpers\Json::decode($kpdp->value);
                if (!empty($json['tanggal']) || !empty($json['no_mesin']) || !empty($json['keterangan']) || !empty($json['shift_operator'])) {
                    $hasData = true;
                }
            }
            
            $nks[] = [
                'id' => $kp->id,
                'nomor_kartu' => $kp->nomor_kartu,
                'warna' => $warnaName,
                'pbg' => $pbg,
                'has_data' => $hasData
            ];
        }
        
        return [
            'success' => true,
            'motif' => $motif,
            'colors' => array_values($colors),
            'nks' => $nks
        ];
    }

    public function actionGetInfoByOrderPfp($order_pfp_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $orderPfp = \common\models\ar\TrnOrderPfp::findOne($order_pfp_id);
        if (!$orderPfp) return ['success' => false];
        
        $motifName = $orderPfp->greige->nama_kain ?? '';
        $motif = trim($motifName);
        
        $nks = [];
        $kartuProses = \common\models\ar\TrnKartuProsesPfp::find()
            ->with(['trnKartuProsesPfpItems'])
            ->where(['order_pfp_id' => $order_pfp_id])
            ->all();
            
        foreach ($kartuProses as $kp) {
            $panjangTotal = 0;
            $jumlahRoll = 0;
            foreach ($kp->trnKartuProsesPfpItems as $item) {
                $jumlahRoll++;
                $panjangTotal += (float)($item->panjang_m ?? 0);
            }
            $panjangTotal = number_format((float)$panjangTotal, 0);
            $berat = number_format((float)$kp->berat, 1);
            $pbg = $panjangTotal . ' / ' . $berat . ' / ' . $jumlahRoll;
            
            $hasData = false;
            $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar() ?: 1;
            $kppp = \common\models\ar\KartuProcessPfpProcess::findOne([
                'kartu_process_id' => $kp->id,
                'process_id' => $processId
            ]);
            if ($kppp) {
                $json = \yii\helpers\Json::decode($kppp->value);
                if (!empty($json['tanggal']) || !empty($json['no_mesin']) || !empty($json['keterangan']) || !empty($json['shift_operator'])) {
                    $hasData = true;
                }
            }
            
            $nks[] = [
                'id' => $kp->id,
                'nomor_kartu' => $kp->nomor_kartu,
                'pbg' => $pbg,
                'has_data' => $hasData
            ];
        }
        
        return [
            'success' => true,
            'motif' => $motif,
            'nks' => $nks
        ];
    }

    public function actionPersiapanPfp()
    {
        $searchModel = new TrnKartuProsesPfpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar();
        if (!$processId) {
            $processId = 1; // Fallback
        }

        $mesinOptions = [];
        $process = \common\models\ar\MstProcessPfp::findOne($processId);
        if ($process) {
            foreach ($process->mstMesinProseses as $mesin) {
                if (!empty($mesin->model_mesin)) {
                    $mesinOptions[$mesin->model_mesin] = $mesin->model_mesin;
                }
            }
        }
        
        $params = Yii::$app->request->queryParams;
        $mcFilter = isset($params['mcFilter']) ? $params['mcFilter'] : [];
        if (!is_array($mcFilter) && $mcFilter !== '') {
            $mcFilter = [$mcFilter];
        }
        $shiftPagiFilter = isset($params['shiftPagiFilter']) ? $params['shiftPagiFilter'] : null;
        $shiftSiangFilter = isset($params['shiftSiangFilter']) ? $params['shiftSiangFilter'] : null;
        $tanggalFilter = isset($params['tanggalFilter']) ? $params['tanggalFilter'] : null;
        
        $mesinInputOptions = [];
        if ($process) {
            foreach ($process->mstMesinProseses as $mesin) {
                if (empty($mcFilter) || in_array($mesin->model_mesin, $mcFilter)) {
                    $mesinInputOptions[$mesin->nama_mesin] = $mesin->nama_mesin;
                }
            }
        }
        asort($mesinInputOptions);
        
        $query = $dataProvider->query;
        if (!empty($mcFilter) || !empty($shiftPagiFilter) || !empty($shiftSiangFilter) || !empty($tanggalFilter)) {
            $query->innerJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = ' . $processId);
            
            if (!empty($mcFilter)) {
                $namaMesins = \common\models\ar\MstMesinProses::find()
                    ->select('nama_mesin')
                    ->where(['model_mesin' => $mcFilter])
                    ->column();
                
                $mcConditions = ['or'];
                if (!empty($namaMesins)) {
                    foreach ($namaMesins as $nm) {
                        $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $nm . '"'];
                    }
                }
                foreach ($mcFilter as $mc) {
                    $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $mc . '"'];
                }
                $query->andWhere($mcConditions);
            }
            
            if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
                $shiftConditions = ['or'];
                if (!empty($shiftPagiFilter)) {
                    $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftPagiFilter . '"'];
                }
                if (!empty($shiftSiangFilter)) {
                    $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftSiangFilter . '"'];
                }
                $query->andWhere($shiftConditions);
            }
            
            if (!empty($tanggalFilter)) {
                $dates = explode(' to ', $tanggalFilter);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $dateExpr = "split_part(split_part(kppp.value, '\"tanggal\":\"', 2), '\"', 1)";
                    $query->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                    $query->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
                } else {
                    $query->andWhere(['like', 'kppp.value', '"tanggal":"' . $tanggalFilter . '"']);
                }
            }
        } else {
            $query->leftJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = ' . $processId);
        }
        
        $query->addGroupBy(['kppp.value']);
        
        $shiftSortExpr = "split_part(split_part(kppp.value, '\"shift_operator\":\"', 2), '\"', 1)";
        $dateSortExpr = "split_part(split_part(kppp.value, '\"tanggal\":\"', 2), '\"', 1)";
        
        $dataProvider->sort->attributes['shift'] = [
            'asc' => [$shiftSortExpr => SORT_ASC],
            'desc' => [$shiftSortExpr => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['openDateRange'] = [
            'asc' => [$dateSortExpr => SORT_ASC],
            'desc' => [$dateSortExpr => SORT_DESC],
        ];
        
        // Set default order by date ascending (oldest first), then by shift
        if (empty(Yii::$app->request->queryParams['sort'])) {
            $query->orderBy([
                new \yii\db\Expression($dateSortExpr . " ASC"),
                new \yii\db\Expression($shiftSortExpr . " ASC"),
            ]);
        }

        return $this->render('persiapan-pfp', [
            'searchModel'  => $searchModel,
            'dataProvider'=> $dataProvider,
            'mcFilter' => $mcFilter,
            'shiftPagiFilter' => $shiftPagiFilter,
            'shiftSiangFilter' => $shiftSiangFilter,
            'tanggalFilter' => $tanggalFilter,
            'mesinOptions' => $mesinOptions,
            'mesinInputOptions' => $mesinInputOptions,
            'processId' => $processId,
        ]);
    }
    
    public function actionPrintPersiapanPfp()
    {
        $tanggalFilter = Yii::$app->request->get('tanggalFilter');
        $shiftPagiFilter = Yii::$app->request->get('shiftPagiFilter');
        $shiftSiangFilter = Yii::$app->request->get('shiftSiangFilter');
        $mcFilter = Yii::$app->request->get('mcFilter', []);
        
        $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar();
        if (!$processId) {
            $processId = 1;
        }
        
        $query = \common\models\ar\TrnKartuProsesPfp::find()
            ->joinWith(['orderPfp.greige'])
            ->leftJoin('kartu_process_pfp_process kppp', 'kppp.kartu_process_id = trn_kartu_proses_pfp.id AND kppp.process_id = ' . $processId);
            
        $query->where(['and', ['not', ['kppp.value' => null]], ['!=', 'kppp.value', '']]);
        
        if (!empty($mcFilter) && is_array($mcFilter)) {
            $namaMesins = \common\models\ar\MstMesinProses::find()->select('nama_mesin')->where(['model_mesin' => $mcFilter])->column();
            
            $mcConditions = ['or'];
            if (!empty($namaMesins)) {
                foreach ($namaMesins as $nm) {
                    $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $nm . '"'];
                }
            }
            foreach ($mcFilter as $mc) {
                $mcConditions[] = ['like', 'kppp.value', '"no_mesin":"' . $mc . '"'];
            }
            $query->andWhere($mcConditions);
        }
        
        if (!empty($shiftPagiFilter) || !empty($shiftSiangFilter)) {
            $shiftConditions = ['or'];
            if (!empty($shiftPagiFilter)) {
                $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftPagiFilter . '"'];
            }
            if (!empty($shiftSiangFilter)) {
                $shiftConditions[] = ['like', 'kppp.value', '"shift_operator":"' . $shiftSiangFilter . '"'];
            }
            $query->andWhere($shiftConditions);
        }
        
        if (!empty($tanggalFilter)) {
            $dates = explode(' to ', $tanggalFilter);
            if (count($dates) == 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $dateExpr = "split_part(split_part(kppp.value, '\"tanggal\":\"', 2), '\"', 1)";
                $query->andWhere(['>=', new \yii\db\Expression($dateExpr), $startDate]);
                $query->andWhere(['<=', new \yii\db\Expression($dateExpr), $endDate]);
            } else {
                $query->andWhere(['like', 'kppp.value', '"tanggal":"' . $tanggalFilter . '"']);
            }
        }
        
        $dateSortExpr = "split_part(split_part(kppp.value, '\"tanggal\":\"', 2), '\"', 1)";
        $shiftSortExpr = "split_part(split_part(kppp.value, '\"shift_operator\":\"', 2), '\"', 1)";
        $query->orderBy([
            new \yii\db\Expression($dateSortExpr . " ASC"),
            new \yii\db\Expression($shiftSortExpr . " ASC"),
        ]);
        
        $models = $query->all();
        
        $content = $this->renderPartial('print-persiapan-pfp', [
            'models' => $models,
            'tanggalFilter' => $tanggalFilter,
            'shiftPagiFilter' => $shiftPagiFilter,
            'shiftSiangFilter' => $shiftSiangFilter,
        ]);
        
        $title = 'LAPORAN HARIAN PERSIAPAN PFP';
        $shiftStr = [];
        if (!empty($shiftPagiFilter)) {
            $shiftStr[] = $shiftPagiFilter . ' (Pagi)';
        }
        if (!empty($shiftSiangFilter)) {
            $shiftStr[] = $shiftSiangFilter . ' (Siang)';
        }
        if (!empty($shiftStr)) {
            $title .= ' SHIFT: ' . implode(' DAN ', $shiftStr);
        }
        
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_BLANK,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
                .row {
                    margin: 0px 0px 0px 0px !important;
                    padding: 0px !important;
                }
                
                .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
                    border:0;
                    padding:5px 0 5px 0;
                    margin-left:-0.00001;
                }
                body, .row, .col-xs-12 { background-color: #fff; color: #000; }
                table { page-break-inside: auto; background-color: #fff; color: #000; }
                tr { page-break-inside: avoid; page-break-after: auto; }
            ',
            'options' => ['title' => 'Laporan Harian Persiapan PFP'],
            'methods' => [
                'SetTitle' => $title,
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        
        return $pdf->render();
    }
    
    public function actionUpdatePersiapanPfp()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post('Inputan');
            if (!empty($data) && is_array($data)) {
                $processId = \common\models\ar\MstProcessPfp::find()->select('id')->where(['order' => 1])->scalar();
                if (!$processId) $processId = 1;
                
                foreach ($data as $item) {
                    $kartuProcessId = isset($item['kartu_process_id']) ? $item['kartu_process_id'] : null;
                    
                    if (empty($kartuProcessId) && !empty($item['nomor_kartu'])) {
                        $kp = \common\models\ar\TrnKartuProsesPfp::findOne(['nomor_kartu' => trim($item['nomor_kartu'])]);
                        if ($kp) {
                            $kartuProcessId = $kp->id;
                        } else {
                            Yii::$app->session->addFlash('error', 'Nomor Kartu ' . $item['nomor_kartu'] . ' tidak ditemukan.');
                            continue;
                        }
                    }
                    
                    if (empty($kartuProcessId)) continue;
                    
                    $kppp = \common\models\ar\KartuProcessPfpProcess::findOne([
                        'kartu_process_id' => $kartuProcessId,
                        'process_id' => $processId
                    ]);
                    
                    if (!$kppp) {
                        $kppp = new \common\models\ar\KartuProcessPfpProcess();
                        $kppp->kartu_process_id = $kartuProcessId;
                        $kppp->process_id = $processId;
                        $kppp->value = '{}';
                    }
                    
                    $json = \yii\helpers\Json::decode($kppp->value);
                    if (!is_array($json)) $json = [];
                    
                    if (isset($item['tanggal'])) {
                        $json['tanggal'] = $item['tanggal'];
                    }
                    if (isset($item['shift_operator'])) {
                        $json['shift_operator'] = $item['shift_operator'];
                    }
                    if (isset($item['no_mesin'])) {
                        $json['no_mesin'] = $item['no_mesin'];
                    }
                    if (isset($item['keterangan'])) {
                        $json['keterangan'] = $item['keterangan'];
                    }
                    
                    $kppp->value = \yii\helpers\Json::encode($json);
                    $kppp->save(false);
                }
                Yii::$app->session->setFlash('success', 'Data berhasil disimpan.');
            }
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['persiapan-pfp']);
    }
}