<?php
use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnStockGreige;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekap Processing Dyeing';
$this->params['breadcrumbs'][] = $this->title;

$currentYear = date('Y');
$woMonths = Yii::$app->db->createCommand("
    SELECT DISTINCT TO_CHAR(date, 'MM') AS m
    FROM trn_wo
    WHERE TO_CHAR(date, 'YYYY') = :year AND date IS NOT NULL
    ORDER BY m ASC
", [':year' => $currentYear])->queryColumn();

$indonesianMonths = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$monthOptions = [];
foreach ($woMonths as $m) {
    if (isset($indonesianMonths[$m])) {
        $monthOptions[$m] = $indonesianMonths[$m];
    }
}

if (empty($monthOptions)) {
    $monthOptions = $indonesianMonths;
}

$queryParams = Yii::$app->request->queryParams;

$queryParamsOnProcess = array_merge(['rekap'], $queryParams, ['status_rekap' => 'on_process']);
unset($queryParamsOnProcess['page']);

$queryParamsSelesai = array_merge(['rekap'], $queryParams, ['status_rekap' => 'selesai']);
unset($queryParamsSelesai['page']);

$queryParamsSemua = array_merge(['rekap'], $queryParams, ['status_rekap' => 'semua']);
unset($queryParamsSemua['page']);
$formatIndoDate = function($dateValue) {
    if (empty($dateValue)) {
        return '';
    }
    
    if (is_numeric($dateValue)) {
        $time = (int)$dateValue;
    } else {
        $time = strtotime($dateValue);
    }
    
    if (!$time) {
        return $dateValue;
    }
    
    $day = date('d', $time);
    $monthNum = (int)date('m', $time);
    $year = date('Y', $time);
    
    $indoMonths = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agt',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des'
    ];
    
    $monthName = isset($indoMonths[$monthNum]) ? $indoMonths[$monthNum] : date('M', $time);
    return "{$day}-{$monthName}-{$year}";
};

$formatProcessDate = function($dateValue) {
    if (empty($dateValue)) {
        return '';
    }
    
    if (is_numeric($dateValue)) {
        $time = (int)$dateValue;
    } else {
        $time = strtotime($dateValue);
    }
    
    if (!$time) {
        return $dateValue;
    }
    
    return date('j/n/y', $time);
};

$groupContentOptions = function($model, $key, $index, $column) {
    $models = array_values($column->grid->dataProvider->getModels());
    $pos = false;
    foreach ($models as $i => $m) {
        if ($m === $model) {
            $pos = $i;
            break;
        }
    }
    if ($pos === false) return [];

    $currentWoId = $model->wo_id;
    $prevWoId = $pos > 0 ? $models[$pos - 1]->wo_id : null;

    if ($currentWoId !== $prevWoId) {
        $rowspan = 1;
        for ($i = $pos + 1; $i < count($models); $i++) {
            if ($models[$i]->wo_id === $currentWoId) {
                $rowspan++;
            } else {
                break;
            }
        }
        return ['rowspan' => $rowspan, 'style' => 'vertical-align: middle; text-align: center; background-color: #fff;'];
    } else {
        return ['style' => 'display: none;'];
    }
};

$groupWarnaContentOptions = function($model, $key, $index, $column) {
    $models = array_values($column->grid->dataProvider->getModels());
    $pos = false;
    foreach ($models as $i => $m) {
        if ($m === $model) {
            $pos = $i;
            break;
        }
    }
    if ($pos === false) return [];

    $currentWoId = $model->wo_id;
    $currentColor = $model->woColor->moColor->color ?? null;
    
    $prevWoId = $pos > 0 ? $models[$pos - 1]->wo_id : null;
    $prevColor = $pos > 0 ? ($models[$pos - 1]->woColor->moColor->color ?? null) : null;

    if ($currentWoId !== $prevWoId || $currentColor !== $prevColor) {
        $rowspan = 1;
        for ($i = $pos + 1; $i < count($models); $i++) {
            $nextWoId = $models[$i]->wo_id;
            $nextColor = $models[$i]->woColor->moColor->color ?? null;
            if ($nextWoId === $currentWoId && $nextColor === $currentColor) {
                $rowspan++;
            } else {
                break;
            }
        }
        return ['rowspan' => $rowspan, 'style' => 'vertical-align: middle; text-align: center; background-color: #fff;'];
    } else {
        return ['style' => 'display: none;'];
    }
};

$allModels = $dataProvider->getModels();
$modelIds = array_filter(\yii\helpers\ArrayHelper::getColumn($allModels, 'id'));

$processMap = [];
$lastProcessesMap = [];
$actionLogMap = [];

$relaxProcessId = null;
$scutcherProcessId = null;
$relaxProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => 'Relaxing']);
$scutcherProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => 'Scutcher Relaxing']);
if ($relaxProcess) $relaxProcessId = $relaxProcess->id;
if ($scutcherProcess) $scutcherProcessId = $scutcherProcess->id;

if (!empty($modelIds)) {
    // 1. Fetch all process data for these models
    $allProcessData = (new \yii\db\Query())
        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
        ->where(['in', 'kartu_process_id', $modelIds])
        ->all();
        
    foreach ($allProcessData as $pd) {
        $processMap[$pd['kartu_process_id']][$pd['process_id']] = $pd;
    }
    
    // 2. Fetch last processes
    $lastProcessesRaw = Yii::$app->db->createCommand("
        SELECT DISTINCT ON (k.kartu_process_id) k.kartu_process_id, m.nama_proses
        FROM kartu_process_dyeing_process k
        INNER JOIN mst_process_dyeing m ON k.process_id = m.id
        WHERE k.kartu_process_id IN (" . implode(',', $modelIds) . ")
        AND k.value IS NOT NULL AND k.value <> ''
        ORDER BY k.kartu_process_id, m.order DESC
    ")->queryAll();
    
    foreach ($lastProcessesRaw as $row) {
        $lastProcessesMap[$row['kartu_process_id']] = $row['nama_proses'];
    }
    
    // 3. Fetch Action Logs for masuk_verpacking
    $actionLogsRaw = (new \yii\db\Query())
        ->select(['kartu_proses_id'])
        ->from(\common\models\ar\ActionLogKartuDyeing::tableName())
        ->where(['in', 'kartu_proses_id', $modelIds])
        ->andWhere(['action_name' => 'masuk_verpacking'])
        ->all();
        
    foreach ($actionLogsRaw as $log) {
        $actionLogMap[$log['kartu_proses_id']] = true;
    }
}

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'woDateRange',
        'label' => 'Tgl. WO',
        'value' => function($data) use ($formatIndoDate) {
            return $data->wo ? $formatIndoDate($data->wo->date) : null;
        },
        'format' => 'raw',
        'filterType' => GridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'convertFormat'=>true,
            'pluginOptions'=>[
                'locale'=>[
                    'format'=>'Y-m-d',
                    'separator'=>' to ',
                ]
            ]
        ],
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'label' => 'Tgl. Terima',
        'value' => function($data) use ($formatIndoDate) {
            return $data->wo ? $formatIndoDate($data->wo->tgl_kirim) : null;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'label' => 'Handling',
        'value' => function($data) {
            return ($data->wo && $data->wo->handling) ? $data->wo->handling->name : '-';
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'label' => 'Target Finish',
        'value' => function($data) {
            $cleanNum = function($val) {
                return (float)str_replace([' ', ','], ['', '.'], (string)$val);
            };
            return $data->wo ? (\Yii::$app->formatter->asDecimal($cleanNum($data->wo->colorQtyFinish), 1) .'M / '. \Yii::$app->formatter->asDecimal($cleanNum($data->wo->colorQtyFinishToYard), 1).'Y') : null;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'label' => 'Panjang',
        'value' => function($data) {
            $cleanNum = function($val) {
                return (float)str_replace([' ', ','], ['', '.'], (string)$val);
            };
            return $data->wo ? (\Yii::$app->formatter->asDecimal($cleanNum($data->wo->colorQtyBatchToMeter)) . ' M') : null;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'label' => 'Note WO',
        'value' => function($data) {
            $note = '';
            if ($data->wo && !empty($data->wo->note)) {
                $rawNote = $data->wo->note;
                $rawNote = html_entity_decode($rawNote, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $rawNote = strip_tags($rawNote);
                $rawNote = str_replace([chr(194).chr(160), '&nbsp;'], ' ', $rawNote);
                $rawNote = str_replace(["\r\n", "\r"], "\n", $rawNote);
                $rawNote = trim($rawNote);
                $note = preg_replace("/\n{2,}/", "\n", $rawNote);
            }
            return !empty($note) ? nl2br(\yii\helpers\Html::encode($note)) : '-';
        },
        'format' => 'raw',
        'vAlign' => 'middle',
        'contentOptions' => function($model, $key, $index, $column) use ($groupContentOptions) {
            $options = $groupContentOptions($model, $key, $index, $column);
            if (isset($options['style']) && strpos($options['style'], 'display: none') === false) {
                $options['style'] .= ' text-align: left; min-width: 150px; white-space: pre-wrap; font-size: 11px; color: #e05e5e;';
            }
            return $options;
        },
    ],
    [
        'label' => 'Memo Perubahan',
        'value' => function($data) {
            $memos = $data->wo ? $data->wo->trnWoMemos : [];
            $memoHtml = '';
            if (!empty($memos)) {
                $memoTexts = [];
                foreach ($memos as $memoModel) {
                    $mText = $memoModel->memo;
                    $mText = html_entity_decode($mText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $mText = strip_tags($mText);
                    $mText = str_replace([chr(194).chr(160), '&nbsp;'], ' ', $mText);
                    $mText = trim($mText);
                    if (!empty($mText)) {
                        $memoTexts[] = '<strong>No. ' . \yii\helpers\Html::encode($memoModel->no) . ':</strong> ' . nl2br(\yii\helpers\Html::encode($mText));
                    }
                }
                if (!empty($memoTexts)) {
                    $memoHtml = implode('<br>', $memoTexts);
                }
            }
            return !empty($memoHtml) ? $memoHtml : '<span style="color: #9e9e9e; font-style: italic;">Tidak ada Memo</span>';
        },
        'format' => 'raw',
        'vAlign' => 'middle',
        'contentOptions' => function($model, $key, $index, $column) use ($groupContentOptions) {
            $options = $groupContentOptions($model, $key, $index, $column);
            if (isset($options['style']) && strpos($options['style'], 'display: none') === false) {
                $options['style'] .= ' text-align: left; min-width: 150px; font-size: 11px; color: #1565c0;';
            }
            return $options;
        },
    ],
    [
        'attribute' => 'customerName',
        'label'=>'Buyer',
        'value'=>function($data){
            return $data->sc ? $data->sc->customerCode : '';
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'attribute' => 'woNo',
        'label'=>'No. WO',
        'value'=>function($data){
            return $data->wo->no;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],
    [
        'attribute' => 'motif',
        'label'=>'Motif',
        'value'=>function($data){
            return $data->wo->greigeNamaKain;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupContentOptions,
    ],

    [
        'attribute' => 'warna',
        'label'=>'Warna',
        'value'=>function($data, $key, $index, $column){
            $color = $data->woColor->moColor->color ?? '';
            $models = $column->grid->dataProvider->getModels();
            $currentWoId = $data->wo_id;
            $currentColor = $color;
            
            $count = 0;
            foreach ($models as $m) {
                if ($m->wo_id === $currentWoId && ($m->woColor->moColor->color ?? '') === $currentColor) {
                    $count++;
                }
            }
            if ($count > 1) {
                return $color . ' (' . $count . 'x)';
            }
            return $color;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupWarnaContentOptions,
    ],
    [
        'attribute' => 'nomor_kartu',
        'label'=>'NK',
        'contentOptions' => function($model, $key, $index, $column) use (&$actionLogMap, &$processMap, $relaxProcessId, $scutcherProcessId) {
            $hasLogs = isset($actionLogMap[$model->id]);
            if ($hasLogs || !empty($model->approved_history) || !empty($model->approved_at) || in_array($model->status, [4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16])) {
                return ['style' => 'font-weight: bold; text-align: center;'];
            }

            $hasRelax = false;
            $hasScutcher = false;
            
            if ($relaxProcessId !== null) {
                $pc = isset($processMap[$model->id][$relaxProcessId]) ? $processMap[$model->id][$relaxProcessId] : false;
                if ($pc !== false && !empty($pc['value'])) {
                    $v = \yii\helpers\Json::decode($pc['value']);
                    if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                        $hasRelax = true;
                    }
                }
            }
            
            if ($scutcherProcessId !== null) {
                $pc = isset($processMap[$model->id][$scutcherProcessId]) ? $processMap[$model->id][$scutcherProcessId] : false;
                if ($pc !== false && !empty($pc['value'])) {
                    $v = \yii\helpers\Json::decode($pc['value']);
                    if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                        $hasScutcher = true;
                    }
                }
            }
            
            if ($hasRelax && $hasScutcher) {
                return ['style' => 'background-color: #c8e6c9 !important; font-weight: bold; color: #1b5e20 !important; text-align: center;'];
            }
            return [];
        },
        'value'=>function($data){
            $viewUrl = \yii\helpers\Url::to(['/processing-dyeing/view', 'id' => $data->id]);
            $icon = \yii\helpers\Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $viewUrl, [
                'target' => '_blank',
                'title' => 'Lihat Detail',
                'style' => 'margin-left: 8px; color: #0288d1;'
            ]);
            $result = \yii\helpers\Html::encode($data->nomor_kartu) . ' ' . $icon;
            if ($data->is_redyeing) {
                $result .= '<br><span style="color:red; font-weight:bold; font-size:11px;">(Redyeing)</span>';
            }
            return $result;
        },
        'format'=>'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Matching Colour',
        'value' => function($data) use ($formatIndoDate) {
            return ($data->woColor && $data->woColor->date_ready_colour) ? $formatIndoDate($data->woColor->date_ready_colour) : null;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => $groupWarnaContentOptions,
    ],
    [
        'label' => 'Matching Toping',
        'value' => function($data) use ($formatIndoDate) {
            return $data->date_toping_matching ? $formatIndoDate($data->date_toping_matching) : null;
        },
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Panjang Greige',
        'value'=>function($data){
            return $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
        },
        'format'=>'decimal'
    ],

    [
        'label'=>'Berat Greige',
        'value'=>function($data){
            return $data->berat;
        },
    ],
    [
        'label'=>'Pcs',
        'value'=>function($data){
            return $data->getTrnKartuProsesDyeingItems()->count();
        },
        'format'=>'decimal'
    ],
    [
        'label' => 'Terakhir Proses',
        'attribute' => 'terakhir_proses',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \yii\helpers\ArrayHelper::map(\common\models\ar\MstProcessDyeing::find()->where(['use_jetblack' => false, 'perbaikan' => false])->orderBy('order')->all(), 'nama_proses', 'nama_proses'),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true,
                'placeholder' => 'Pilih...',
            ],
        ],
        'value' => function($data) use (&$lastProcessesMap) {
            return isset($lastProcessesMap[$data->id]) ? $lastProcessesMap[$data->id] : '-';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
];

// Query and append all processes from master process dynamically
$masterProcesses = \common\models\ar\MstProcessDyeing::find()
    ->where(['use_jetblack' => false, 'perbaikan' => false])
    ->orderBy('order')
    ->all();

foreach ($masterProcesses as $proc) {
    $isDyeing = ($proc->nama_proses === 'Dyeing');
    $isOrange = in_array($proc->nama_proses, [
        'Toping 1', 'Toping 2', 'Toping 3', 'Toping 4',
        'Cuci Ulang',
        'RC 1', 'RC 2', 'RC 3', 'RC 4', 'RC 5',
        'Toping Level',
        'Celup Rayon',
        'Tarik Ulang',
        'RF Ulang 1', 'RF Ulang 2', 'RF Ulang 3', 'RF Ulang 4'
    ]);
    $isPink = in_array($proc->nama_proses, ['Preset', 'Setting']);
    $isResinFinish = ($proc->nama_proses === 'Resin Finish');
    $isHeatCut = ($proc->nama_proses === 'Heat Cut');
    
    $gridColumns[] = [
        'label' => $proc->nama_proses,
        'attribute' => 'processDates[' . $proc->id . ']',
        'filterType' => GridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d',
                    'separator' => ' to ',
                ],
                'autoUpdateInput' => false,
            ],
            'pluginEvents' => [
                'apply.daterangepicker' => "function(ev, picker) {
                    var currentVisibleInput = this;
                    $('.process-date-filter').not(currentVisibleInput).each(function() {
                        $(this).val('');
                        $(this).closest('td').find('input').val('');
                    });
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')).trigger('change');
                }",
                'cancel.daterangepicker' => "function(ev, picker) {
                    $(this).val('').trigger('change');
                }",
            ],
            'options' => [
                'class' => 'form-control process-date-filter',
                'placeholder' => 'Cari Tgl...',
                'style' => 'text-align: center; font-size: 11px;',
            ],
        ],
        'headerOptions' => [
            'style' => 'text-align: center; vertical-align: middle;',
        ],
        'contentOptions' => function($model, $key, $index, $column) use ($proc, $isDyeing, $isOrange, $isPink, $isResinFinish, $isHeatCut, &$processMap, &$actionLogMap) {
            $hasLogs = isset($actionLogMap[$model->id]);
            $isPackFilled = ($hasLogs || !empty($model->approved_history) || !empty($model->approved_at) || in_array($model->status, [4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16]));
            
            if ($isPackFilled) {
                return ['style' => 'background-color: #fffde7 !important; color: #333333 !important; text-align: center;'];
            }

            $hasData = false;
            $pc = isset($processMap[$model->id][$proc->id]) ? $processMap[$model->id][$proc->id] : false;
            if ($pc !== false && !empty($pc['value'])) {
                $v = \yii\helpers\Json::decode($pc['value']);
                if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                    $hasData = true;
                }
            }
            
            if ($hasData) {
                if ($isDyeing) {
                    $bgColor = '#fff8e1'; // Premium Cream
                    $textColor = '#5d4037'; // Dark Brown text
                } elseif ($isResinFinish) {
                    $bgColor = '#bbdefb'; // Light Blue
                    $textColor = '#0d47a1'; // Dark Blue text
                } elseif ($isHeatCut) {
                    $bgColor = '#c8e6c9'; // Light Green
                    $textColor = '#1b5e20'; // Dark Green text
                } elseif ($isPink) {
                    $bgColor = '#f8bbd0'; // Pink
                    $textColor = '#880e4f'; // Dark Pink text
                } elseif ($isOrange) {
                    $bgColor = '#ffe0b2'; // Light Orange
                    $textColor = '#e65100'; // Dark Orange text
                } else {
                    $bgColor = '#e0e0e0'; // Grey for any other filled processes
                    $textColor = '#333333';
                }
                return ['style' => "background-color: {$bgColor} !important; color: {$textColor} !important; font-weight: bold; text-align: center;"];
            }
            return ['style' => 'text-align: center;'];
        },
        'value' => function($data) use ($proc, $formatProcessDate, &$processMap) {
            $tg = '-';
            $sh = '-';
            $mc = '-';
            $pc = isset($processMap[$data->id][$proc->id]) ? $processMap[$data->id][$proc->id] : false;

            if ($pc !== false) {
                $v = \yii\helpers\Json::decode($pc['value']);
                if (isset($v['tanggal']) && !empty($v['tanggal'])) {
                    $tg = $formatProcessDate($v['tanggal']);
                }
                if (isset($v['shift_group']) && !empty($v['shift_group'])) {
                    $sh = $v['shift_group'];
                }
                if (isset($v['no_mesin']) && !empty($v['no_mesin'])) {
                    $mc = $v['no_mesin'];
                }
            }

            return $tg . '-' . $sh . '-' . $mc;
        }
    ];

    if ($proc->nama_proses === 'Resin Finish') {
        $jetblackProcesses = \common\models\ar\MstProcessDyeing::find()
            ->where(['use_jetblack' => true, 'perbaikan' => false])
            ->orderBy('order')
            ->all();
        foreach ($jetblackProcesses as $jbProc) {
            $isJbPink = ($jbProc->nama_proses === 'Setting-2');
            $gridColumns[] = [
                'label' => $jbProc->nama_proses,
                'attribute' => 'processDates[' . $jbProc->id . ']',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' to ',
                        ],
                        'autoUpdateInput' => false,
                    ],
                    'pluginEvents' => [
                        'apply.daterangepicker' => "function(ev, picker) {
                            var currentVisibleInput = this;
                            $('.process-date-filter').not(currentVisibleInput).each(function() {
                                $(this).val('');
                                $(this).closest('td').find('input').val('');
                            });
                            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')).trigger('change');
                        }",
                        'cancel.daterangepicker' => "function(ev, picker) {
                            $(this).val('').trigger('change');
                        }",
                    ],
                    'options' => [
                        'class' => 'form-control process-date-filter',
                        'placeholder' => 'Cari Tgl...',
                        'style' => 'text-align: center; font-size: 11px;',
                    ],
                ],
                'headerOptions' => [
                    'style' => 'text-align: center; vertical-align: middle;',
                ],
                'contentOptions' => function($model, $key, $index, $column) use ($jbProc, $isJbPink, &$processMap, &$actionLogMap) {
                    $hasLogs = isset($actionLogMap[$model->id]);
                    $isPackFilled = ($hasLogs || !empty($model->approved_history) || !empty($model->approved_at) || in_array($model->status, [4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16]));
                    
                    if ($isPackFilled) {
                        return ['style' => 'background-color: #fffde7 !important; color: #333333 !important; text-align: center;'];
                    }

                    $hasData = false;
                    $pc = isset($processMap[$model->id][$jbProc->id]) ? $processMap[$model->id][$jbProc->id] : false;
                    if ($pc !== false && !empty($pc['value'])) {
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                            $hasData = true;
                        }
                    }
                    
                    if ($hasData) {
                        if ($isJbPink) {
                            $bgColor = '#f8bbd0'; // Pink
                            $textColor = '#880e4f'; // Dark Pink text
                        } else {
                            $bgColor = '#e0e0e0'; // Grey
                            $textColor = '#333333';
                        }
                        return ['style' => "background-color: {$bgColor} !important; color: {$textColor} !important; font-weight: bold; text-align: center;"];
                    }
                    return ['style' => 'text-align: center;'];
                },
                'value' => function($data) use ($jbProc, $formatProcessDate, &$processMap) {
                    $tg = '-';
                    $sh = '-';
                    $mc = '-';
                    $pc = isset($processMap[$data->id][$jbProc->id]) ? $processMap[$data->id][$jbProc->id] : false;

                    if ($pc !== false) {
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if (isset($v['tanggal']) && !empty($v['tanggal'])) {
                            $tg = $formatProcessDate($v['tanggal']);
                        }
                        if (isset($v['shift_group']) && !empty($v['shift_group'])) {
                            $sh = $v['shift_group'];
                        }
                        if (isset($v['no_mesin']) && !empty($v['no_mesin'])) {
                            $mc = $v['no_mesin'];
                        }
                    }

                    return $tg . '-' . $sh . '-' . $mc;
                }
            ];
        }
    }
}

// Append final columns (Panjang Jadi, Pack)
$gridColumns[] = [
    'label' => 'Panjang Jadi',
    'value' => function($data) {
        $r = 0;
        $pc = (new \yii\db\Query())
            ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
            ->where(['kartu_process_id' => $data->id, 'process_id' => 11])
            ->one();
        if ($pc !== false) {
            $v = \yii\helpers\Json::decode($pc['value']);
            if (isset($v['panjang_jadi'])) {
                $r = $v['panjang_jadi'];
            }
        }
        
        if ($r === null || $r === '') {
            return '-';
        }
        
        $rClean = str_replace(' ', '', $r);
        if (is_numeric($rClean)) {
            return Yii::$app->formatter->asDecimal($rClean);
        }
        
        return $r;
    },
    'format' => 'raw'
];

$gridColumns[] = [
    'label' => 'Pack',
    'contentOptions' => function($model, $key, $index, $column) {
        $hasLogs = \common\models\ar\ActionLogKartuDyeing::find()
            ->where(['kartu_proses_id' => $model->id, 'action_name' => 'masuk_verpacking'])
            ->exists();
        if ($hasLogs || !empty($model->approved_history) || !empty($model->approved_at) || in_array($model->status, [4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16])) {
            return ['style' => 'background-color: #fffde7 !important; color: #333333 !important; font-weight: bold; text-align: center;'];
        }
        return [];
    },
    'value' => function($data) use ($formatIndoDate) {
        $packDates = [];
        
        // Ambil riwayat dari ActionLogKartuDyeing untuk mencakup semua data historis di masa lalu
        $logs = \common\models\ar\ActionLogKartuDyeing::find()
            ->where(['kartu_proses_id' => $data->id, 'action_name' => 'masuk_verpacking'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
            
        foreach ($logs as $index => $log) {
            $dateFormatted = $formatIndoDate($log->created_at);
            $packDates[] = 'Persetujuan Ke-' . ($index + 1) . ': ' . $dateFormatted;
        }
        
        // Jika karena suatu hal log kosong, gunakan kolom approved_history atau approved_at
        if (empty($packDates)) {
            if (!empty($data->approved_history)) {
                $history = \yii\helpers\Json::decode($data->approved_history);
                if (is_array($history)) {
                    foreach ($history as $index => $h) {
                        if (isset($h['time'])) {
                            $dateFormatted = $formatIndoDate($h['time']);
                            $packDates[] = 'Persetujuan Ke-' . ($index + 1) . ': ' . $dateFormatted;
                        }
                    }
                }
            }
        }
        
        if (empty($packDates) && !empty($data->approved_at)) {
            $packDates[] = 'Persetujuan Ke-1: ' . $formatIndoDate($data->approved_at);
        }
        
        return implode('<br>', $packDates);
    },
    'format' => 'raw'
];
// Process columns to add identifier classes for column hiding
$columnKeys = [];
foreach ($gridColumns as $index => &$col) {
    if (isset($col['class']) && $col['class'] === 'kartik\grid\SerialColumn') {
        $colKey = 'col-serial';
        $colLabel = 'No. Seri';
    } elseif (isset($col['attribute'])) {
        $attrForClass = $col['attribute'] === 'customerName' ? 'buyer' : $col['attribute'];
        $colKey = 'col-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $attrForClass));
        $colLabel = isset($col['label']) ? $col['label'] : \yii\helpers\Inflector::camel2words($col['attribute']);
    } elseif (isset($col['label'])) {
        $colKey = 'col-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $col['label']));
        $colLabel = $col['label'];
    } else {
        $colKey = 'col-column-' . $index;
        $colLabel = 'Kolom ' . ($index + 1);
    }
    
    if (!isset($col['headerOptions'])) {
        $col['headerOptions'] = [];
    }
    if (!isset($col['filterOptions'])) {
        $col['filterOptions'] = [];
    }
    
    $col['headerOptions']['class'] = (isset($col['headerOptions']['class']) ? $col['headerOptions']['class'] . ' ' : '') . $colKey;
    $col['filterOptions']['class'] = (isset($col['filterOptions']['class']) ? $col['filterOptions']['class'] . ' ' : '') . $colKey;

    if (isset($col['contentOptions']) && $col['contentOptions'] instanceof \Closure) {
        $existingClosure = $col['contentOptions'];
        $col['contentOptions'] = function($model, $key, $index, $column) use ($existingClosure, $colKey) {
            $options = call_user_func($existingClosure, $model, $key, $index, $column);
            if (!is_array($options)) {
                $options = [];
            }
            $options['class'] = (isset($options['class']) ? $options['class'] . ' ' : '') . $colKey;
            return $options;
        };
    } else {
        if (!isset($col['contentOptions'])) {
            $col['contentOptions'] = [];
        }
        $col['contentOptions']['class'] = (isset($col['contentOptions']['class']) ? $col['contentOptions']['class'] . ' ' : '') . $colKey;
    }
    
    $columnKeys[$colKey] = $colLabel;
}
unset($col);


$columnToggleDropdown = ' <div class="dropdown" style="display: inline-block; vertical-align: middle; margin-left: 10px;">' .
    '<button class="btn btn-default dropdown-toggle" type="button" id="dropdownColumnToggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">' .
        '<i class="glyphicon glyphicon-th-list"></i> Atur Kolom <span class="caret"></span>' .
    '</button>' .
    '<ul class="dropdown-menu" aria-labelledby="dropdownColumnToggle" style="padding: 10px; min-width: 250px; max-height: 400px; overflow-y: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.15); border-radius: 4px;">' .
        '<li style="padding: 5px; border-bottom: 1px solid #eee; margin-bottom: 5px;">' .
            '<label style="font-weight: bold; margin-bottom: 0; cursor: pointer; width: 100%; display: block;">' .
                '<input type="checkbox" id="toggle-all-columns" checked style="margin-right: 8px;"> Pilih Semua' .
            '</label>' .
        '</li>';

foreach ($columnKeys as $colKey => $colLabel) {
    $cleanLabel = trim(strip_tags($colLabel));
    $cleanLabel = preg_replace('/\s+/', ' ', $cleanLabel);
    $columnToggleDropdown .= '<li style="padding: 2px 5px;">' .
        '<label style="font-weight: normal; margin-bottom: 0; cursor: pointer; display: block; width: 100%;">' .
            '<input type="checkbox" class="col-toggle-checkbox" data-column="' . $colKey . '" checked style="margin-right: 8px;"> ' . $cleanLabel .
        '</label>' .
    '</li>';
}
$columnToggleDropdown .= '</ul></div>';
?>
<div class="kartu-proses-dyeing-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'type' => 'default',
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> Daftar Rekap Processing Dyeing</h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['rekap'], ['class' => 'btn btn-default']) . ' ' .
                        Html::dropDownList(
                            'woMonthDropdown',
                            $searchModel->woMonth,
                            $monthOptions,
                            [
                                'prompt' => '-- Pilih Bulan WO (' . date('Y') . ') --',
                                'class' => 'form-control',
                                'style' => 'display: inline-block; width: auto; margin-left: 10px; vertical-align: middle;',
                                'onchange' => 'filterWoMonth(this);'
                            ]
                        ) . $columnToggleDropdown . (!empty($searchModel->woMonth) ? ' ' . Html::a('<i class="glyphicon glyphicon-file"></i> Export Excel', array_merge(['export-excel'], Yii::$app->request->queryParams, ['woMonth' => $searchModel->woMonth, 'status_rekap' => $statusRekap]), ['id' => 'btn-export-excel', 'class' => 'btn btn-success', 'style' => 'vertical-align: middle; margin-left: 10px;']) : '') . '<br><br>' .
                        \yii\bootstrap\Nav::widget([
                            'options' => ['class' => 'nav nav-tabs', 'style' => 'margin-top: 5px; border-bottom: 2px solid #ddd;'],
                            'items' => [
                                [
                                    'label' => 'On Process',
                                    'url' => $queryParamsOnProcess,
                                    'active' => ($statusRekap === 'on_process'),
                                ],
                                [
                                    'label' => 'Selesai',
                                    'url' => $queryParamsSelesai,
                                    'active' => ($statusRekap === 'selesai'),
                                ],
                                [
                                    'label' => 'Semua (On Process & Selesai)',
                                    'url' => $queryParamsSemua,
                                    'active' => ($statusRekap === 'semua'),
                                ],
                            ],
                        ]),
            //'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
            //'footer'=>false
        ],
        'toolbar'=>[
            '{export}',
            //'{toggleData}'
        ],
        'showPageSummary'=>true,
        'bordered' => true,
        'striped' => false,
        'condensed' => true,
        'hover' => true,
        'rowOptions' => function($model, $key, $index, $grid) {
            $models = array_values($grid->dataProvider->getModels());
            $pos = false;
            foreach ($models as $i => $m) {
                if ($m->id === $model->id) {
                    $pos = $i;
                    break;
                }
            }
            $nextModel = ($pos !== false && isset($models[$pos + 1])) ? $models[$pos + 1] : null;
            
            $currentBuyer = $model->sc ? $model->sc->customerName : null;
            $nextBuyer = ($nextModel && $nextModel->sc) ? $nextModel->sc->customerName : null;
            
            // 1. Determine if Pack is filled (Yellow priority)
            $isPackFilled = false;
            $hasLogs = \common\models\ar\ActionLogKartuDyeing::find()
                ->where(['kartu_proses_id' => $model->id, 'action_name' => 'masuk_verpacking'])
                ->exists();
            if ($hasLogs || !empty($model->approved_history) || !empty($model->approved_at) || in_array($model->status, [4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16])) {
                $isPackFilled = true;
            }

            // 2. Determine if any Perbaikan process is filled (Red)
            $isPerbaikanFilled = false;
            if (!$isPackFilled) {
                $perbaikanProcessIds = \yii\helpers\ArrayHelper::getColumn(
                    \common\models\ar\MstProcessDyeing::find()
                        ->where(['nama_proses' => [
                            'Perbaikan JB',
                            'Toping 1', 'Toping 2', 'Toping 3', 'Toping 4',
                            'Cuci Ulang',
                            'RC 1', 'RC 2', 'RC 3', 'RC 4', 'RC 5',
                            'Toping Level',
                            'Celup Rayon',
                            'Tarik Ulang',
                            'RF Ulang 1', 'RF Ulang 2', 'RF Ulang 3', 'RF Ulang 4'
                        ]])
                        ->all(),
                    'id'
                );
                
                if (!empty($perbaikanProcessIds)) {
                    $hasPerbaikan = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id' => $model->id, 'process_id' => $perbaikanProcessIds])
                        ->andWhere(['not', ['value' => null]])
                        ->andWhere(['not', ['value' => '']])
                        ->all();
                    
                    foreach ($hasPerbaikan as $pc) {
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                            $isPerbaikanFilled = true;
                            break;
                        }
                    }
                }
            }

            // 3. Determine if Dyeing is filled (Blue)
            $isDyeingFilled = false;
            if (!$isPackFilled && !$isPerbaikanFilled) {
                $dyeingProcess = \common\models\ar\MstProcessDyeing::findOne(['nama_proses' => 'Dyeing']);
                if ($dyeingProcess !== null) {
                    $pc = (new \yii\db\Query())
                        ->from(\common\models\ar\KartuProcessDyeingProcess::tableName())
                        ->where(['kartu_process_id' => $model->id, 'process_id' => $dyeingProcess->id])
                        ->one();
                    if ($pc !== false && !empty($pc['value'])) {
                        $v = \yii\helpers\Json::decode($pc['value']);
                        if (!empty($v['tanggal']) || !empty($v['shift_group'])) {
                            $isDyeingFilled = true;
                        }
                    }
                }
            }

            // 4. Determine if Preset or Setting is filled (Pink) - Disabled per user request
            $isPinkFilled = false;

            $classes = [];
            if ($isPackFilled) {
                $classes[] = 'row-pack-filled';
            } elseif ($isPerbaikanFilled) {
                $classes[] = 'row-perbaikan-filled';
            } elseif ($isDyeingFilled) {
                $classes[] = 'row-dyeing-filled';
            } elseif ($isPinkFilled) {
                $classes[] = 'row-pink-filled';
            }

            if ($currentBuyer !== $nextBuyer) {
                if ($model->wo && !empty($model->wo->note)) {
                    // There will be a note row, so let's not apply group-end-row here
                    return !empty($classes) ? ['class' => implode(' ', $classes)] : [];
                }
                $classes[] = 'group-end-row';
            }
            return !empty($classes) ? ['class' => implode(' ', $classes)] : [];
        },
        'columns' => $gridColumns,
    ]); ?>
</div>

<style>
/* Scroll container untuk freeze header */
.kartu-proses-dyeing-index .table-responsive {
    max-height: calc(100vh - 290px) !important;
    overflow-y: auto !important;
    overflow-x: auto !important;
    border: 1.5px solid #666666 !important;
}

/* Custom Premium Scrollbar for Table Responsive to make it highly visible and easy to grab */
.kartu-proses-dyeing-index .table-responsive::-webkit-scrollbar {
    width: 10px !important;
    height: 12px !important; /* Thicker horizontal scrollbar */
}
.kartu-proses-dyeing-index .table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1 !important;
    border-radius: 6px !important;
}
.kartu-proses-dyeing-index .table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1 !important;
    border-radius: 6px !important;
    border: 2px solid #f1f1f1 !important;
}
.kartu-proses-dyeing-index .table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8 !important;
}

/* --- HORIZONTAL STICKY / FREEZE PANELS FOR METADATA COLUMNS --- */
/* Set standard widths and left offsets to align perfectly when frozen */
.col-serial {
    width: 50px !important; min-width: 50px !important; max-width: 50px !important;
}
.col-id {
    width: 60px !important; min-width: 60px !important; max-width: 60px !important;
}
.col-wodaterange {
    width: 100px !important; min-width: 100px !important; max-width: 100px !important;
}
.col-tgl--terima {
    width: 90px !important; min-width: 90px !important; max-width: 90px !important;
}
.col-handling {
    width: 90px !important; min-width: 90px !important; max-width: 90px !important;
}
.col-target-finish {
    width: 110px !important; min-width: 110px !important; max-width: 110px !important;
}
.col-panjang {
    width: 90px !important; min-width: 90px !important; max-width: 90px !important;
}
.col-note-wo {
    width: 200px !important; min-width: 200px !important; max-width: 200px !important;
}
.col-memo-perubahan {
    width: 180px !important; min-width: 180px !important; max-width: 180px !important;
}
.col-buyer {
    width: 80px !important; min-width: 80px !important; max-width: 80px !important;
}
.col-wono {
    width: 115px !important; min-width: 115px !important; max-width: 115px !important;
}
.col-motif {
    width: 150px !important; min-width: 150px !important; max-width: 150px !important;
}
.col-warna {
    width: 100px !important; min-width: 100px !important; max-width: 100px !important;
}
.col-nomor-kartu {
    width: 100px !important; min-width: 100px !important; max-width: 100px !important;
}
.col-matching-colour {
    width: 110px !important; min-width: 110px !important; max-width: 110px !important;
}
.col-matching-toping {
    width: 110px !important; min-width: 110px !important; max-width: 110px !important;
}

/* Horizontal Sticky Behavior for Frozen Columns */
.col-serial, .col-id, .col-wodaterange, .col-tgl--terima, .col-handling, .col-target-finish, .col-panjang, .col-note-wo, .col-memo-perubahan, .col-buyer, .col-wono, .col-motif, .col-warna, .col-nomor-kartu, .col-matching-colour, .col-matching-toping {
    position: sticky !important;
    z-index: 5 !important;
}

/* Base background for standard body cells in frozen columns to prevent overlapping text */
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-serial,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-id,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-tgl--terima,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-handling,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-target-finish,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-panjang,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-note-wo,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-memo-perubahan,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-buyer,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-wono,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-motif,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-warna,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-nomor-kartu,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-matching-colour,
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td.col-matching-toping {
    background-color: #ffffff !important;
}

/* Ensure Hover rows look consistent on sticky cells */
.kartu-proses-dyeing-index .table tbody tr:hover td.col-serial,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-id,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-wodaterange,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-buyer,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-wono,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-motif,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-tgl--kirim,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-hand,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-t--finish,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-panjang,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-warna,
.kartu-proses-dyeing-index .table tbody tr:hover td.col-nomor-kartu {
    background-color: #f5f8fa !important;
}

/* --- ROW DYEING FILLED (BLUE) SHADING --- */
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td {
    background-color: #e3f2fd !important;
}
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-serial,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-id,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-buyer,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-wono,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-motif,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-tgl--kirim,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-hand,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-t--finish,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-panjang,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-warna,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-dyeing-filled > td.col-nomor-kartu {
    background-color: #e3f2fd !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td {
    background-color: #bbdefb !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-serial,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-id,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-wodaterange,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-buyer,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-wono,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-motif,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-tgl--kirim,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-hand,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-t--finish,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-panjang,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-warna,
.kartu-proses-dyeing-index .table tbody tr.row-dyeing-filled:hover td.col-nomor-kartu {
    background-color: #bbdefb !important;
}

/* --- ROW PERBAIKAN FILLED (RED) SHADING --- */
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td {
    background-color: #ffebee !important;
}
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-serial,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-id,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-buyer,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-wono,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-motif,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-tgl--kirim,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-hand,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-t--finish,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-panjang,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-warna,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-perbaikan-filled > td.col-nomor-kartu {
    background-color: #ffebee !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td {
    background-color: #ffcdd2 !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-serial,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-id,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-wodaterange,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-buyer,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-wono,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-motif,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-tgl--kirim,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-hand,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-t--finish,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-panjang,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-warna,
.kartu-proses-dyeing-index .table tbody tr.row-perbaikan-filled:hover td.col-nomor-kartu {
    background-color: #ffcdd2 !important;
}

/* --- ROW PACK FILLED (YELLOW) SHADING --- */
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td {
    background-color: #fffde7 !important; /* Premium ultra-soft light yellow */
}
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-serial,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-id,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-buyer,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-wono,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-motif,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-tgl--kirim,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-hand,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-t--finish,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-panjang,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-warna,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pack-filled > td.col-nomor-kartu {
    background-color: #fffde7 !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td {
    background-color: #fff59d !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-serial,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-id,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-wodaterange,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-buyer,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-wono,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-motif,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-tgl--kirim,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-hand,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-t--finish,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-panjang,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-warna,
.kartu-proses-dyeing-index .table tbody tr.row-pack-filled:hover td.col-nomor-kartu {
    background-color: #fff59d !important;
}

/* --- ROW PINK FILLED (PRESET/SETTING) SHADING --- */
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td {
    background-color: #fce4ec !important; /* Premium soft pink */
}
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-serial,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-id,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-buyer,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-wono,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-motif,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-tgl--kirim,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-hand,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-t--finish,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-panjang,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-warna,
.kartu-proses-dyeing-index .table-bordered > tbody > tr.row-pink-filled > td.col-nomor-kartu {
    background-color: #fce4ec !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td {
    background-color: #f8bbd0 !important;
}
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-serial,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-id,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-wodaterange,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-buyer,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-wono,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-motif,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-tgl--kirim,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-hand,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-t--finish,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-panjang,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-warna,
.kartu-proses-dyeing-index .table tbody tr.row-pink-filled:hover td.col-nomor-kartu {
    background-color: #f8bbd0 !important;
}

/* Visual divider line right after NK (Nomor Kartu) column */
.kartu-proses-dyeing-index .table-bordered th.col-nomor-kartu,
.kartu-proses-dyeing-index .table-bordered td.col-nomor-kartu {
    border-right: 2.5px solid #444444 !important;
}

/* Note container in afterRow: slide horizontally alongside the headers! */
.kartu-proses-dyeing-index .table-bordered > tbody > tr.note-row-group > td.col-note-container {
    position: sticky !important;
    left: 110px !important;
    z-index: 6 !important;
    width: 445px !important; min-width: 445px !important; max-width: 445px !important;
}

/* Memo container in afterRow: slide horizontally next to note container! */
.kartu-proses-dyeing-index .table-bordered > tbody > tr.note-row-group > td.col-memo-container {
    position: sticky !important;
    left: 555px !important;
    z-index: 6 !important;
    width: 200px !important; min-width: 200px !important; max-width: 200px !important;
}

/* 1. Paksa semua border luar dan dalam tampil tegas, hilangkan efek hilangnya garis vertikal akibat grouping Kartik */
.kartu-proses-dyeing-index .table-bordered {
    border: 1.5px solid #666666 !important;
    border-collapse: separate !important; /* separate diperlukan agar sticky positioning bekerja mulus dengan border-collapse */
    border-spacing: 0 !important;
}

.kartu-proses-dyeing-index .table-bordered > thead > tr > th {
    border: 1px solid #777777 !important;
    background-color: #ecf0f5 !important; /* Abu-abu terang profesional */
    color: #222222 !important;
    font-weight: bold;
    text-align: center;
    vertical-align: middle !important;
}

/* Freeze baris header pertama (Kolom Label) */
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th {
    position: sticky !important;
    top: 0 !important;
    background-color: #ecf0f5 !important;
    z-index: 20 !important;
    box-shadow: inset 0 -1px 0 #777, inset 0 1px 0 #777;
}

/* Intersection styles for first row headers inside frozen columns */
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-serial,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-id,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-tgl--terima,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-handling,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-target-finish,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-panjang,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-note-wo,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-memo-perubahan,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-buyer,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-wono,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-motif,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-warna,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-nomor-kartu,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-matching-colour,
.kartu-proses-dyeing-index .table-bordered > thead > tr:first-child > th.col-matching-toping {
    z-index: 30 !important; /* Must be higher than normal sticky headers and normal sticky cells */
}

/* Kotak pencarian filter di bagian atas tabel */
.kartu-proses-dyeing-index .table-bordered .filters td {
    border: 1px solid #888888 !important;
    background-color: #f9f9f9 !important;
}

/* Freeze baris header kedua (Kolom Filter Pencarian) */
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td {
    position: sticky !important;
    top: 38px !important; /* Pas di bawah baris header pertama */
    background-color: #f9f9f9 !important;
    z-index: 19 !important;
    box-shadow: inset 0 -1px 0 #888, inset 0 1px 0 #888;
}

/* Intersection styles for filter row inside frozen columns */
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-serial,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-id,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-wodaterange,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-tgl--terima,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-handling,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-target-finish,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-panjang,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-note-wo,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-memo-perubahan,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-buyer,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-wono,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-motif,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-warna,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-nomor-kartu,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-matching-colour,
.kartu-proses-dyeing-index .table-bordered > thead > tr.filters > td.col-matching-toping {
    z-index: 29 !important; /* Higher than normal filter cells and body cells */
}

/* 2. Paksa semua sel body memiliki garis tepi (top, bottom, left, right) yang lengkap, tegas, dan berwarna abu-abu solid */
.kartu-proses-dyeing-index .table-bordered > tbody > tr > td {
    border: 1px solid #b0b0b0 !important;
    background-color: #ffffff !important; /* Latar belakang putih bersih, hilangkan warna lavender/ungu yang tidak rapi */
    color: #333333 !important;
    padding: 8px !important;
}

/* 3. Garis pembatas yang tebal, solid, dan menyatu (continuous) saat berganti Buyer */
.kartu-proses-dyeing-index .table tbody tr.group-end-row td {
    border-bottom: 2.5px solid #222222 !important; /* Garis hitam tebal menyatu dari ujung kiri ke kanan */
}

/* 4. Efek hover baris yang lembut */
.kartu-proses-dyeing-index .table tbody tr:hover td {
    background-color: #f5f8fa !important;
}

/* CSS Premium untuk dropdown pengatur kolom */
#dropdownColumnToggle + .dropdown-menu {
    border: 1px solid #ccc;
    box-shadow: 0 6px 12px rgba(0,0,0,0.175) !important;
    padding: 12px !important;
}
#dropdownColumnToggle + .dropdown-menu li label {
    padding: 4px 8px;
    border-radius: 3px;
    transition: background-color 0.2s;
}
#dropdownColumnToggle + .dropdown-menu li label:hover {
    background-color: #f1f3f5;
}
</style>

<?php
$jsCode = <<<JS
window.filterWoMonth = function(selectElement) {
    var val = selectElement.value;
    var searchKey = "TrnKartuProsesDyeingSearch[woMonth]";
    var url = window.location.href;
    var urlParts = url.split("?");
    var newUrl = urlParts[0];
    
    if (urlParts.length > 1) {
        var params = urlParts[1].split("&");
        var newParams = [];
        for (var i = 0; i < params.length; i++) {
            var p = params[i].split("=");
            var decodedKey = decodeURIComponent(p[0]);
            if (decodedKey !== searchKey && decodedKey !== "page" && decodedKey !== "_pjax") {
                newParams.push(params[i]);
            }
        }
        if (val) {
            newParams.push(encodeURIComponent(searchKey) + "=" + val);
        }
        if (newParams.length > 0) {
            newUrl += "?" + newParams.join("&");
        }
    } else {
        if (val) {
            newUrl += "?" + encodeURIComponent(searchKey) + "=" + val;
        }
    }
    
    window.location.href = newUrl;
};

// Mencegah dropdown tertutup otomatis saat checkbox diklik
$('.kartu-proses-dyeing-index').on('click', '#dropdownColumnToggle + .dropdown-menu', function(e) {
    e.stopPropagation();
});

// Fungsi menyesuaikan colspan dari container Memo dan Note sesuai dengan jumlah kolom yang terlihat
function updateColspans() {
    var leftOffset = 0;
    
    function processStickyCol(selector, defaultWidth) {
        var isVisible = $(selector + ':first').is(':visible');
        var width = isVisible ? defaultWidth : 0;
        if (isVisible) {
            $(selector).each(function() {
                this.style.setProperty('left', leftOffset + 'px', 'important');
            });
        }
        leftOffset += width;
    }

    processStickyCol('.col-serial', 50);
    processStickyCol('.col-id', 60);
    processStickyCol('.col-wodaterange', 100);
    processStickyCol('.col-tgl--terima', 90);
    processStickyCol('.col-handling', 90);
    processStickyCol('.col-target-finish', 110);
    processStickyCol('.col-panjang', 90);
    processStickyCol('.col-note-wo', 200);
    processStickyCol('.col-memo-perubahan', 180);
    processStickyCol('.col-buyer', 80);
    processStickyCol('.col-wono', 115);
    processStickyCol('.col-motif', 150);
    processStickyCol('.col-warna', 100);
    processStickyCol('.col-nomor-kartu', 100);
    processStickyCol('.col-matching-colour', 110);
    processStickyCol('.col-matching-toping', 110);
}

// Fungsi menerapkan visibilitas kolom dari localStorage dan meng-update URL Export Excel
function applyColumnVisibility() {
    var hiddenColumns = JSON.parse(localStorage.getItem('rekap_dyeing_hidden_columns')) || [];
    
    // Set default semua checkbox dalam keadaan tercentang
    $('.col-toggle-checkbox').prop('checked', true);
    $('#toggle-all-columns').prop('checked', true);

    // Sembunyikan kolom yang tersimpan dalam daftar sembunyi
    hiddenColumns.forEach(function(colClass) {
        $('.' + colClass).hide();
        $('.col-toggle-checkbox[data-column="' + colClass + '"]').prop('checked', false);
    });

    // Update status checkbox "Pilih Semua"
    if (hiddenColumns.length > 0) {
        $('#toggle-all-columns').prop('checked', false);
    }

    // Sesuaikan colspan baris Note dan Memo
    updateColspans();

    // Update URL Export Excel dengan list kolom tersembunyi dan parameter anti-cache
    var btnExport = $('#btn-export-excel');
    if (btnExport.length > 0) {
        var baseHref = btnExport.attr('href');
        if (baseHref) {
            // Bersihkan parameter hidden_cols dan _t lama jika ada
            var cleanHref = baseHref.replace(/&hidden_cols=[^&]*/g, '').replace(/&_t=[^&]*/g, '');
            var timestamp = Date.now();
            if (hiddenColumns.length > 0) {
                btnExport.attr('href', cleanHref + '&hidden_cols=' + hiddenColumns.join(',') + '&_t=' + timestamp);
            } else {
                btnExport.attr('href', cleanHref + '&_t=' + timestamp);
            }
        }
    }
}

// Jalankan saat halaman pertama kali dimuat
applyColumnVisibility();

// Ketika pjax selesai memuat ulang grid, terapkan kembali visibilitas kolom
$(document).on('pjax:end', function() {
    applyColumnVisibility();
});

// Toggle per kolom
$('.kartu-proses-dyeing-index').on('change', '.col-toggle-checkbox', function() {
    var colClass = $(this).data('column');
    var isChecked = $(this).is(':checked');
    var hiddenColumns = JSON.parse(localStorage.getItem('rekap_dyeing_hidden_columns')) || [];

    if (isChecked) {
        $('.' + colClass).show();
        hiddenColumns = hiddenColumns.filter(function(item) {
            return item !== colClass;
        });
    } else {
        $('.' + colClass).hide();
        if (hiddenColumns.indexOf(colClass) === -1) {
            hiddenColumns.push(colClass);
        }
    }

    localStorage.setItem('rekap_dyeing_hidden_columns', JSON.stringify(hiddenColumns));

    // Update status checkbox "Pilih Semua"
    var totalCheckboxes = $('.col-toggle-checkbox').length;
    var checkedCheckboxes = $('.col-toggle-checkbox:checked').length;
    $('#toggle-all-columns').prop('checked', totalCheckboxes === checkedCheckboxes);

    // Menerapkan perubahan visibilitas dan meng-update URL link export
    applyColumnVisibility();
});

// Toggle semua kolom (Pilih Semua)
$('.kartu-proses-dyeing-index').on('change', '#toggle-all-columns', function() {
    var isChecked = $(this).is(':checked');
    $('.col-toggle-checkbox').prop('checked', isChecked);
    
    var hiddenColumns = [];

    $('.col-toggle-checkbox').each(function() {
        var colClass = $(this).data('column');
        if (isChecked) {
            $('.' + colClass).show();
        } else {
            $('.' + colClass).hide();
            hiddenColumns.push(colClass);
        }
    });

    localStorage.setItem('rekap_dyeing_hidden_columns', JSON.stringify(hiddenColumns));
    
    // Menerapkan perubahan visibilitas dan meng-update URL link export
    applyColumnVisibility();
});

// Otomatis membersihkan input filter tanggal proses di kolom lain saat satu filter diisi/difokuskan/dibuka
$(document).on('mousedown click focus show.daterangepicker input', '.process-date-filter', function(e) {
    var currentInput = this;
    $('.process-date-filter').each(function() {
        if (this !== currentInput) {
            $(this).val('');
            $(this).closest('td').find('input').val('');
        }
    });
});
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
?>
