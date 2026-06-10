<?php

namespace backend\scratch;

class ControllerPatch {
    public function actionLaporanRekapMesin()
    {
        $selectedModelMesins = \Yii::$app->request->get('model_mesins', []);
        if (!is_array($selectedModelMesins)) {
            $selectedModelMesins = [$selectedModelMesins];
        }
        
        $dateRange = \Yii::$app->request->get('date_range', date('Y-m-d') . ' to ' . date('Y-m-d'));
        $dates = explode(' to ', $dateRange);
        $startDate = $dates[0];
        $endDate = isset($dates[1]) ? $dates[1] : $dates[0];

        // Get options for dropdown
        $modelMesinOptions = \common\models\ar\MstMesinProses::find()
            ->select('model_mesin')
            ->distinct()
            ->where(['not', ['model_mesin' => null]])
            ->andWhere(['not', ['model_mesin' => '']])
            ->orderBy(['model_mesin' => SORT_ASC])
            ->column();

        $summary = ['batch' => 0, 'kartu' => 0, 'jumbo' => 0, 'perbaikan' => 0];
        $rekapProses = [];
        $rekapMesin = [];
        $rekapShift = [];
        $prosesOrderMap = [];
        
        $allProsesNames = [];
        
        // Map perbaikan
        $perbaikanProcesses = \common\models\ar\MstProcessDyeing::find()
            ->where(['perbaikan' => true])
            ->select('nama_proses')
            ->column();

        if (!empty($selectedModelMesins)) {
            $machines = \common\models\ar\MstMesinProses::find()
                ->where(['in', 'model_mesin', $selectedModelMesins])
                ->all();
            
            $machineNames = \yii\helpers\ArrayHelper::getColumn($machines, 'nama_mesin');
            $machineMap = \yii\helpers\ArrayHelper::index($machines, 'nama_mesin');
            
            // Build date conditions
            $dateConditions = ['or'];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $dateConditions[] = ['like', 'kp.value', '"tanggal":"' . $currentDate . '"'];
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }

            // DYEING
            $queryDyeing = \common\models\ar\KartuProcessDyeingProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_dyeing kpd', 'kp.kartu_process_id = kpd.id')
                ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesDyeing::STATUS_DELIVERED])
                ->andWhere(['not', ['kpd.status' => \common\models\ar\TrnKartuProsesDyeing::STATUS_BATAL]])
                ->andWhere($dateConditions)
                ->with(['kartuProcess.trnKartuProsesDyeingItems', 'process']);

            $dyeingRecords = $queryDyeing->all();

            // PFP
            $queryPfp = \common\models\ar\KartuProcessPfpProcess::find()
                ->alias('kp')
                ->innerJoin('trn_kartu_proses_pfp kpd', 'kp.kartu_process_id = kpd.id')
                ->where(['>=', 'kpd.status', \common\models\ar\TrnKartuProsesPfp::STATUS_DELIVERED])
                ->andWhere(['not', ['kpd.status' => \common\models\ar\TrnKartuProsesPfp::STATUS_GAGAL_PROSES]])
                ->andWhere($dateConditions)
                ->with(['kartuProcess.trnKartuProsesPfpItems', 'process']);
                
            $pfpRecords = $queryPfp->all();

            // Tambahan Input (Percobaan / Manual Order additions)
            $tambahanQuery = \common\models\ar\TrnRekapProsesMesinInput::find()
                ->alias('kp')
                ->where(['in', 'mst_mesin_proses_id', \yii\helpers\ArrayHelper::getColumn($machines, 'id')])
                ->andWhere(['between', 'tanggal', $startDate, $endDate])
                ->all();

            // Process function
            $processRecord = function($valJson, $processName, $tipeKategori, $isPerbaikan, $isJumbo) use (
                &$rekapProses, &$rekapMesin, &$rekapShift, &$summary, $machineNames, $machineMap, $selectedModelMesins
            ) {
                $vals = \yii\helpers\Json::decode($valJson);
                $noMesin = isset($vals['no_mesin']) ? $vals['no_mesin'] : null;
                $tanggalVal = isset($vals['tanggal']) ? $vals['tanggal'] : null;
                $shift = isset($vals['shift_group']) ? $vals['shift_group'] : '-';
                
                if (!$noMesin || !in_array($noMesin, $machineNames)) return;

                $mesinModel = isset($machineMap[$noMesin]) ? $machineMap[$noMesin] : null;
                $modelMesin = $mesinModel ? $mesinModel->model_mesin : '';
                
                $isStenter = stripos($modelMesin, 'stenter') !== false;
                
                // Grouping Toping 1-5 to "Toping"
                $processKey = $processName;
                if (preg_match('/^Toping\s+[1-5]$/i', $processName)) {
                    $processKey = 'Toping';
                }

                $isNoLength = (preg_match('/^RC\s+[1-5]$/i', $processName) || stripos($processName, 'Cuci Ulang') !== false);
                
                $panjang = 0;
                if (!$isNoLength) {
                    $panjang = $isStenter ? floatval($vals['panjang_jadi'] ?? 0) : floatval($vals['panjang_greige'] ?? 0);
                }

                // Update summary
                $summary['kartu']++;
                if ($isJumbo || stripos($modelMesin, 'Hisaka Jumbo') !== false) {
                    $summary['jumbo']++;
                }
                if ($isPerbaikan) {
                    $summary['perbaikan']++;
                }
                
                // Init Process Array
                if (!isset($rekapProses[$processKey])) {
                    $rekapProses[$processKey] = ['dyeing' => ['p' => 0, 'c' => 0], 'pfp' => ['p' => 0, 'c' => 0], 'total' => ['p' => 0, 'c' => 0]];
                }
                
                $rekapProses[$processKey][$tipeKategori]['p'] += $panjang;
                $rekapProses[$processKey][$tipeKategori]['c']++;
                $rekapProses[$processKey]['total']['p'] += $panjang;
                $rekapProses[$processKey]['total']['c']++;
                
                // Init Mesin Array
                if (!isset($rekapMesin[$noMesin])) {
                    $rekapMesin[$noMesin] = ['total' => ['p' => 0, 'c' => 0]];
                }
                if (!isset($rekapMesin[$noMesin][$processKey])) {
                    $rekapMesin[$noMesin][$processKey] = ['p' => 0, 'c' => 0];
                }
                $rekapMesin[$noMesin][$processKey]['p'] += $panjang;
                $rekapMesin[$noMesin][$processKey]['c']++;
                $rekapMesin[$noMesin]['total']['p'] += $panjang;
                $rekapMesin[$noMesin]['total']['c']++;

                // Init Shift Array
                if (!isset($rekapShift[$shift])) {
                    $rekapShift[$shift] = ['total' => ['p' => 0, 'c' => 0]];
                }
                if (!isset($rekapShift[$shift][$processKey])) {
                    $rekapShift[$shift][$processKey] = ['p' => 0, 'c' => 0];
                }
                $rekapShift[$shift][$processKey]['p'] += $panjang;
                $rekapShift[$shift][$processKey]['c']++;
                $rekapShift[$shift]['total']['p'] += $panjang;
                $rekapShift[$shift]['total']['c']++;
            };

            foreach ($dyeingRecords as $rec) {
                $processName = $rec->process ? $rec->process->nama_proses : '';
                $isPerbaikan = in_array($processName, $perbaikanProcesses);
                $isJumbo = false; // Add logic if Jumbo is determined from items. Usually just Machine.
                $processRecord($rec->value, $processName, 'dyeing', $isPerbaikan, $isJumbo);
            }

            foreach ($pfpRecords as $rec) {
                $processName = $rec->process ? $rec->process->nama_proses : '';
                $isPerbaikan = false; // PFP doesn't have perbaikan? Or we just map it.
                $processRecord($rec->value, $processName, 'pfp', $isPerbaikan, false);
            }

            foreach ($tambahanQuery as $rec) {
                $processName = $rec->nama_proses;
                $isPerbaikan = in_array($processName, $perbaikanProcesses);
                
                $valJson = \yii\helpers\Json::encode([
                    'no_mesin' => $rec->mstMesinProses ? $rec->mstMesinProses->nama_mesin : '',
                    'tanggal' => $rec->tanggal,
                    'shift_group' => $rec->shift,
                    'panjang_jadi' => $rec->panjang_jadi,
                    'panjang_greige' => $rec->panjang_greige
                ]);
                $processRecord($valJson, $processName, stripos($rec->tipe, 'pfp') !== false ? 'pfp' : 'dyeing', $isPerbaikan, false);
            }

            $summary['batch'] = max(0, $summary['kartu'] - $summary['jumbo']);
        }

        // Get process names for column headers and sort them by MstProcessDyeing->urutan
        $allProsesNames = array_keys($rekapProses);
        
        return $this->render('laporan-rekap-mesin', [
            'modelMesinOptions' => $modelMesinOptions,
            'selectedModelMesins' => $selectedModelMesins,
            'dateRange' => $dateRange,
            'summary' => $summary,
            'rekapProses' => $rekapProses,
            'rekapMesin' => $rekapMesin,
            'rekapShift' => $rekapShift,
            'allProsesNames' => $allProsesNames,
        ]);
    }
}
