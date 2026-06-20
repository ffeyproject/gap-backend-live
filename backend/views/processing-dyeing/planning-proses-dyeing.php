<?php

use common\models\ar\TrnKartuProsesDyeing;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ar\TrnKartuProsesDyeingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider|null */
/* @var $showTable bool */
/* @var $processOptions \common\models\ar\MstProcessDyeing[] */
/* @var $planningIds array */
/* @var $terakhirProsesNames array */
/* @var $tanggal string */
/* @var $lastProcessesMap array */
/* @var $processMap array */

$this->title = 'Planning Proses Dyeing';
$this->params['breadcrumbs'][] = ['label' => 'Processing', 'url' => ['/processing-dyeing/index']];
$this->params['breadcrumbs'][] = $this->title;

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
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
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

?>

<div class="planning-proses-dyeing-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- BAGIAN 1: Filter Form -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Planning</h3>
        </div>
        <div class="box-body">
            <form method="get" action="<?= Url::to(['planning-proses-dyeing']) ?>" id="planning-filter-form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih planning:</label>
                            <?= Select2::widget([
                                'name' => 'planning_ids',
                                'value' => $planningIds,
                                'data' => ArrayHelper::map($processOptions, 'id', 'nama_proses'),
                                'options' => [
                                    'placeholder' => 'Bisa pilih lebih dari 1...',
                                    'multiple' => true,
                                    'id' => 'planning-ids-select',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih terakhir proses:</label>
                            <?= Select2::widget([
                                'name' => 'terakhir_proses_names',
                                'value' => $terakhirProsesNames,
                                'data' => ArrayHelper::map($processOptions, 'nama_proses', 'nama_proses'),
                                'options' => [
                                    'placeholder' => 'Bisa pilih lebih dari 1...',
                                    'multiple' => true,
                                    'id' => 'terakhir-proses-select',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Tanggal:</label>
                            <?= DatePicker::widget([
                                'name' => 'tanggal',
                                'value' => $tanggal,
                                'type' => DatePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                ],
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'tanggal-picker',
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" name="tampilkan" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Tampilkan</button>
                        <?= Html::a('<i class="fa fa-refresh"></i> Reset', ['planning-proses-dyeing'], ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- BAGIAN 4: Keterangan Planning -->
    <?php if ($showTable && !empty($planningIds)): 
        $selectedPlanningProcesses = \common\models\ar\MstProcessDyeing::find()
            ->where(['id' => $planningIds])
            ->orderBy(['order' => SORT_ASC])
            ->all();
    ?>
        <div class="row">
        <?php foreach ($selectedPlanningProcesses as $proc): ?>
            <div class="col-md-3">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= Html::encode($proc->nama_proses) ?></h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-bordered table-condensed" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="width: 20px"></th>
                                    <th>Keterangan</th>
                                    <th style="width: 40px; text-align: center;">Jml</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $opts = isset($planningOptionsMap[$proc->id]) ? $planningOptionsMap[$proc->id] : [];
                                foreach ($opts as $opt): 
                                    $jml = isset($planningCounts[$opt->id]) ? $planningCounts[$opt->id] : 0;
                                ?>
                                    <tr>
                                        <td style="background-color: <?= Html::encode($opt->color) ?>;"></td>
                                        <td style="padding: 2px;">
                                            <input type="text" class="form-control input-sm opt-label-input" 
                                                   data-id="<?= $opt->id ?>" 
                                                   data-proc="<?= $proc->id ?>"
                                                   value="<?= Html::encode($opt->label) ?>" 
                                                   placeholder="kosong..."
                                                   style="border: none; background: transparent; box-shadow: none; height: 24px; padding: 2px 5px;">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <span class="badge bg-light-blue jml-badge" data-opt="<?= $opt->id ?>" data-proc="<?= $proc->id ?>"><?= $jml ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- BAGIAN 2: Data Table -->
    <?php if ($showTable && $dataProvider !== null): ?>
        <?php
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
                'value'=>function($data){
                    return $data->woColor->moColor->color ?? '';
                },
                'format' => 'raw',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => $groupWarnaContentOptions,
            ],
            [
                'attribute' => 'nama_warna',
                'label' => 'Nama Warna',
                'value' => function($data) {
                    return !empty($data->nama_warna) ? $data->nama_warna : '';
                },
                'format' => 'raw',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => $groupWarnaContentOptions,
            ],
            [
                'attribute' => 'nomor_kartu',
                'label'=>'NK',
                'value'=>function($data){
                    $viewUrl = Url::to(['/processing-dyeing/view', 'id' => $data->id]);
                    $icon = Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $viewUrl, [
                        'target' => '_blank',
                        'title' => 'Lihat Detail',
                        'style' => 'margin-left: 8px; color: #0288d1;'
                    ]);
                    $result = Html::encode($data->nomor_kartu) . ' ' . $icon;
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
                'value' => function($data) use (&$lastProcessesMap) {
                    return isset($lastProcessesMap[$data->id]) ? $lastProcessesMap[$data->id] : '-';
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
        ];

        // Query and append selected planning processes dynamically
        // Construct beforeHeader for grouped headers
        $beforeHeaderColumns = [
            ['content' => '', 'options' => ['colspan' => 21, 'class' => 'text-center']],
        ];

        foreach ($selectedPlanningProcesses as $proc) {
            $siapCount = isset($siapCounts[$proc->id]) ? $siapCounts[$proc->id] : 0;
            $opts = isset($planningOptionsMap[$proc->id]) ? $planningOptionsMap[$proc->id] : [];
            
            $selectData = [];
            $colorMap = [];
            foreach ($opts as $opt) {
                $colorMap[$opt->id] = $opt->color;
                if ($opt->label !== '') {
                    $selectData[$opt->id] = $opt->label;
                }
            }

            $beforeHeaderColumns[] = [
                'content' => Html::encode($proc->nama_proses),
                'options' => ['colspan' => 3, 'class' => 'text-center warning', 'style' => 'vertical-align: middle; font-weight: bold; background-color: #fcf8e3;']
            ];

            $gridColumns[] = [
                'attribute' => "siap_{$proc->id}",
                'label' => "Siap (<span class='siap-count' data-proc='{$proc->id}'>{$siapCount}</span>x)",
                'encodeLabel' => false,
                'filter' => [1 => 'Sudah', 0 => 'Belum'],
                'headerOptions' => ['style' => 'text-align: center; vertical-align: middle; background-color: #f4f4f4; white-space: pre-line; min-width: 80px;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;'],
                'value' => function($data) use ($proc, &$kartuPlanningsMap) {
                    $kp = isset($kartuPlanningsMap[$data->id][$proc->id]) ? $kartuPlanningsMap[$data->id][$proc->id] : null;
                    $is_siap = $kp ? $kp->is_siap : false;
                    $siapChecked = $is_siap ? 'checked' : '';
                    return '<input type="checkbox" class="kp-siap-chk" data-kp="'.$data->id.'" data-proc="'.$proc->id.'" '.$siapChecked.'>';
                },
                'format' => 'raw',
            ];

            $gridColumns[] = [
                'attribute' => "opt_{$proc->id}",
                'label' => "Keterangan",
                'filter' => $selectData,
                'filterInputOptions' => [
                    'class' => 'form-control kp-filter-opt-select',
                    'data' => [
                        'proc' => $proc->id,
                        'colors' => $colorMap,
                    ],
                    'prompt' => ''
                ],
                'headerOptions' => ['style' => 'text-align: center; vertical-align: middle; background-color: #f4f4f4;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; padding: 5px;'],
                'value' => function($data) use ($proc, &$kartuPlanningsMap, $selectData, $colorMap) {
                    $kp = isset($kartuPlanningsMap[$data->id][$proc->id]) ? $kartuPlanningsMap[$data->id][$proc->id] : null;
                    $option_id = $kp ? $kp->option_id : '';
                    return Html::dropDownList('opt', $option_id, $selectData, [
                        'class' => 'form-control input-sm kp-opt-select',
                        'prompt' => '- Pilih -',
                        'data' => [
                            'kp' => $data->id,
                            'proc' => $proc->id,
                            'colors' => $colorMap,
                        ],
                        'style' => 'min-width: 120px;'
                    ]);
                },
                'format' => 'raw',
            ];

            $gridColumns[] = [
                'attribute' => "catatan_{$proc->id}",
                'label' => "Catatan",
                'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Search'],
                'headerOptions' => ['style' => 'text-align: center; vertical-align: middle; background-color: #f4f4f4;'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; padding: 5px;'],
                'value' => function($data) use ($proc, &$kartuPlanningsMap) {
                    $kp = isset($kartuPlanningsMap[$data->id][$proc->id]) ? $kartuPlanningsMap[$data->id][$proc->id] : null;
                    $catatan = $kp ? $kp->catatan : '';
                    return Html::textInput('catatan', $catatan, [
                        'class' => 'form-control input-sm kp-catatan-input',
                        'placeholder' => 'isi catatan...',
                        'data-kp' => $data->id,
                        'data-proc' => $proc->id,
                        'style' => 'min-width: 140px;'
                    ]);
                },
                'format' => 'raw',
            ];
        }
        ?>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-table"></i> Hasil Teropong Planning</h3>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-columns"></i> Atur Kolom <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu" id="column-selector" style="max-height: 300px; overflow-y: auto; padding: 10px;">
                            <!-- Checkboxes will be populated by JS -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="top-scrollbar" style="overflow-x: auto; margin-bottom: 5px; height: 15px;">
                    <div class="top-scrollbar-fake-content" style="height: 1px;"></div>
                </div>
                <div class="table-wrapper" style="overflow-x: auto;">

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => $gridColumns,
                    'responsive' => true,
                    'hover' => true,
                    'resizableColumns' => false,
                    'beforeHeader' => [
                        [
                            'columns' => $beforeHeaderColumns
                        ]
                    ],
                    'panel' => [
                        'type' => GridView::TYPE_DEFAULT,
                        'heading' => false,
                        'before' => '',
                        'after' => '',
                    ],
                ]); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$updateOptUrl = Url::to(['update-planning-option']);
$updateKpUrl = Url::to(['update-kartu-planning']);

$jsVariables = "var updateOptUrl = '{$updateOptUrl}';\nvar updateKpUrl = '{$updateKpUrl}';\n";

$js = <<<'JS'
$(document).ready(function() {
    // 1. Sync Top Scrollbar
    var gridContainer = $('.table-wrapper .kv-grid-container');
    if (gridContainer.length === 0) {
        gridContainer = $('.table-wrapper .table-responsive');
    }
    if (gridContainer.length === 0) {
        gridContainer = $('.table-wrapper');
    }

    function syncScroll() {
        var tableWidth = gridContainer.find('table').outerWidth();
        $('.top-scrollbar-fake-content').width(tableWidth);
    }
    
    // Run after a short delay to ensure table is fully rendered
    setTimeout(syncScroll, 500);
    $(window).on('resize', syncScroll);

    $('.top-scrollbar').on('scroll', function() {
        gridContainer.scrollLeft($(this).scrollLeft());
    });
    gridContainer.on('scroll', function() {
        $('.top-scrollbar').scrollLeft($(this).scrollLeft());
    });

    // 2. Atur Kolom (Column Visibility Toggle)
    var table = $('.table-wrapper table');
    var thead = table.find('thead');
    // The last tr in thead is usually the filter row (which has td, not th). We need the actual header row.
    var trHeaders = thead.find('tr').not('.filters').last(); 

    // Clear and build the column selector
    $('#column-selector').empty();
    
    // Add "Pilih Semua" option
    var liPilihSemua = $('<li><label style="font-weight: bold; margin-bottom: 0; cursor: pointer; display: block; padding: 5px 15px; white-space: nowrap;"><input type="checkbox" id="col-toggle-all" checked style="margin-right: 10px; vertical-align: text-top;"> Pilih Semua</label></li>');
    $('#column-selector').append(liPilihSemua);
    $('#column-selector').append('<li role="separator" class="divider" style="margin: 5px 0;"></li>');
    
    trHeaders.find('th').each(function(index) {
        var th = $(this);
        var label = th.text().trim();
        if (label === '') {
            label = 'Kolom ' + (index + 1);
        }
        
        var li = $('<li><label style="font-weight: normal; margin-bottom: 0; cursor: pointer; display: block; padding: 5px 15px; white-space: nowrap;"><input type="checkbox" checked class="col-toggle" data-idx="'+index+'" style="margin-right: 10px; vertical-align: text-top;"> ' + label + '</label></li>');
        $('#column-selector').append(li);
        
        // Restore saved state from localStorage if exists
        var savedState = localStorage.getItem('planning_col_' + index);
        if (savedState === 'hidden') {
            li.find('input').prop('checked', false);
            hideColumn(index);
        }
    });

    // Update "Pilih Semua" state initially
    function updatePilihSemuaState() {
        var total = $('.col-toggle').length;
        var checked = $('.col-toggle:checked').length;
        $('#col-toggle-all').prop('checked', total > 0 && total === checked);
    }
    updatePilihSemuaState();

    // Prevent dropdown from closing when clicking inside
    $('#column-selector').on('click', function(e) {
        e.stopPropagation();
    });

    // Handle "Pilih Semua" click
    $('#col-toggle-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.col-toggle').each(function() {
            if ($(this).is(':checked') !== isChecked) {
                $(this).prop('checked', isChecked).trigger('change');
            }
        });
    });

    $('.col-toggle').on('change', function() {
        var idx = $(this).data('idx');
        if ($(this).is(':checked')) {
            showColumn(idx);
            localStorage.setItem('planning_col_' + idx, 'visible');
        } else {
            hideColumn(idx);
            localStorage.setItem('planning_col_' + idx, 'hidden');
        }
        updatePilihSemuaState();
        syncScroll();
    });

    function hideColumn(idx) {
        table.find('tr').each(function() {
            $(this).find('th, td').eq(idx).hide();
        });
    }

    function showColumn(idx) {
        table.find('tr').each(function() {
            $(this).find('th, td').eq(idx).show();
        });
    }

    // Auto-save Planning Option labels (Bagian 4)
    $('.opt-label-input').on('change', function() {
        var el = $(this);
        var id = el.data('id');
        var proc = el.data('proc');
        var label = el.val().trim();
        
        $.post(updateOptUrl, {id: id, label: label}, function(res) {
            if (!res.success) {
                alert('Gagal menyimpan keterangan');
            } else {
                el.css('background-color', '#e8f5e9');
                setTimeout(function() { el.css('background-color', 'transparent'); }, 1000);
                
                $('.kp-opt-select[data-proc="' + proc + '"], .kp-filter-opt-select[data-proc="' + proc + '"]').each(function() {
                    var selectEl = $(this);
                    var optionEl = selectEl.find('option[value="' + id + '"]');
                    
                    if (label === '') {
                        if (optionEl.length > 0) {
                            optionEl.remove();
                        }
                    } else {
                        if (optionEl.length > 0) {
                            optionEl.text(label);
                        } else {
                            selectEl.append(new Option(label, id));
                        }
                    }
                });
                colorizeSelect(); // re-apply colors
            }
        });
    });

    // Helper function to colorize Select options
    function colorizeSelect() {
        $('.kp-opt-select, .kp-filter-opt-select').each(function() {
            var selectEl = $(this);
            var colors = selectEl.data('colors');
            if (typeof colors === 'string') {
                try { colors = JSON.parse(colors); } catch(e) {}
            }
            if (!colors) return;
            
            var val = selectEl.val();
            if (val && colors[val]) {
                selectEl.css('background-color', colors[val]);
            } else {
                selectEl.css('background-color', '#fff');
            }
            
            selectEl.find('option').each(function() {
                var optVal = $(this).attr('value');
                if (optVal && colors[optVal]) {
                    $(this).css('background-color', colors[optVal]);
                } else {
                    $(this).css('background-color', '#fff');
                }
            });
        });
    }
    colorizeSelect();

    // For the filter select, apply color on change
    $(document).on('change', '.kp-filter-opt-select', function() {
        colorizeSelect();
    });

    // Live update counters
    function updateLiveCounts() {
        $('.jml-badge').text('0');
        var optCounts = {};
        $('.kp-opt-select').each(function() {
            var val = $(this).val();
            if (val) {
                optCounts[val] = (optCounts[val] || 0) + 1;
            }
        });
        $.each(optCounts, function(optId, count) {
            $('.jml-badge[data-opt="' + optId + '"]').text(count);
        });

        var siapCounts = {};
        $('.kp-siap-chk').each(function() {
            var proc = $(this).data('proc');
            if ($(this).is(':checked')) {
                siapCounts[proc] = (siapCounts[proc] || 0) + 1;
            }
        });
        $('.siap-count').each(function() {
            var proc = $(this).data('proc');
            var count = siapCounts[proc] || 0;
            $(this).text(count);
        });
    }

    // Auto-save Grid Inputs (Bagian 2)
    $('.kp-siap-chk, .kp-opt-select, .kp-catatan-input').on('change', function() {
        var el = $(this);
        var kp = el.data('kp');
        var proc = el.data('proc');
        var field = '';
        var value = '';
        
        if (el.hasClass('kp-siap-chk')) {
            field = 'is_siap';
            value = el.is(':checked') ? 1 : 0;
            updateLiveCounts();
        } else if (el.hasClass('kp-opt-select')) {
            field = 'option_id';
            value = el.val();
            colorizeSelect();
            updateLiveCounts();
        } else if (el.hasClass('kp-catatan-input')) {
            field = 'catatan';
            value = el.val();
            
            el.css('background-color', '#e8f5e9');
            setTimeout(function() { el.css('background-color', 'transparent'); }, 1000);
        }
        
        $.post(updateKpUrl, {
            kartu_process_id: kp,
            process_id: proc,
            field: field,
            value: value
        }, function(res) {
            if (!res.success) {
                alert('Gagal menyimpan data');
            }
        });
    });

    // Pressing Enter inside Catatan saves it without submitting the form
    $('.kp-catatan-input').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $(this).blur();
        }
    });
});
JS;
$this->registerJs($jsVariables . $js);
?>
