<?php
use yii\helpers\Html;
use kartik\grid\GridView;

$formatIndoDate = function($date) {
    if (!$date) return null;
    $time = strtotime($date);
    $d = date('d', $time);
    $m = date('n', $time);
    $y = date('y', $time);
    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    return $d . ' ' . $months[$m] . ' ' . $y;
};

$visibleColsArr = $visibleColsStr !== '' ? explode(',', $visibleColsStr) : [];

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'], // 0
    [
        'attribute' => 'id', // 1
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'woDateRange', // 2
        'label' => 'Tgl. WO',
        'value' => function($data) use ($formatIndoDate) {
            return $data->wo ? $formatIndoDate($data->wo->date) : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Tgl. Terima', // 3
        'value' => function($data) use ($formatIndoDate) {
            return $data->wo ? $formatIndoDate($data->wo->tgl_kirim) : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Handling', // 4
        'value' => function($data) {
            return ($data->wo && $data->wo->handling) ? $data->wo->handling->name : '-';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Target Finish', // 5
        'value' => function($data) {
            $cleanNum = function($val) {
                return (float)str_replace([' ', ','], ['', '.'], (string)$val);
            };
            return $data->wo ? (\Yii::$app->formatter->asDecimal($cleanNum($data->wo->colorQtyFinish), 1) .'M / '. \Yii::$app->formatter->asDecimal($cleanNum($data->wo->colorQtyFinishToYard), 1).'Y') : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Panjang', // 6
        'value' => function($data) {
            $cleanNum = function($val) {
                return (float)str_replace([' ', ','], ['', '.'], (string)$val);
            };
            return $data->wo ? (\Yii::$app->formatter->asDecimal($cleanNum($data->wo->colorQtyBatchToMeter)) . ' M') : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Note WO', // 7
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
            return !empty($note) ? $note : '-';
        },
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Memo Perubahan', // 8
        'value' => function($data) {
            $memos = $data->wo ? $data->wo->trnWoMemos : [];
            $memoTexts = [];
            if (!empty($memos)) {
                foreach ($memos as $memoModel) {
                    $mText = $memoModel->memo;
                    $mText = html_entity_decode($mText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $mText = strip_tags($mText);
                    $mText = str_replace([chr(194).chr(160), '&nbsp;'], ' ', $mText);
                    $mText = trim($mText);
                    if (!empty($mText)) {
                        $memoTexts[] = 'No. ' . $memoModel->no . ': ' . $mText;
                    }
                }
            }
            return !empty($memoTexts) ? implode(" | ", $memoTexts) : 'Tidak ada Memo';
        },
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'customerName', // 9
        'label'=>'Buyer',
        'value'=>function($data){
            return $data->sc ? $data->sc->customerCode : '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'woNo', // 10
        'label'=>'No. WO',
        'value'=>function($data){
            return $data->wo ? $data->wo->no : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'motif', // 11
        'label'=>'Motif',
        'value'=>function($data){
            return $data->wo ? $data->wo->greigeNamaKain : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'warna', // 12
        'label'=>'Warna',
        'value'=>function($data){
            return $data->woColor->moColor->color ?? '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'nama_warna', // 13
        'label' => 'Nama Warna',
        'value' => function($data) {
            return !empty($data->nama_warna) ? $data->nama_warna : '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'nomor_kartu', // 14
        'label'=>'NK',
        'value'=>function($data){
            $result = $data->nomor_kartu;
            if ($data->is_redyeing) {
                $result .= ' (Redyeing)';
            }
            return $result;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Matching Colour', // 15
        'value' => function($data) use ($formatIndoDate) {
            return ($data->woColor && $data->woColor->date_ready_colour) ? $formatIndoDate($data->woColor->date_ready_colour) : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Matching Toping', // 16
        'value' => function($data) use ($formatIndoDate) {
            return $data->date_toping_matching ? $formatIndoDate($data->date_toping_matching) : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Panjang Greige', // 17
        'value'=>function($data){
            return $data->getTrnKartuProsesDyeingItems()->sum('panjang_m');
        },
        'format'=>'decimal',
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Berat Greige', // 18
        'value'=>function($data){
            return $data->berat;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label'=>'Pcs', // 19
        'value'=>function($data){
            return $data->getTrnKartuProsesDyeingItems()->count();
        },
        'format'=>'decimal',
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Terakhir Proses', // 20
        'value' => function($data) use (&$lastProcessesMap) {
            return isset($lastProcessesMap[$data->id]) ? $lastProcessesMap[$data->id] : '-';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
];

// Determine the starting index of dynamic columns
$dynStartIdx = count($gridColumns);

// Rebuild dynamic columns
$beforeHeaderColumns = [
    ['content' => '', 'options' => ['colspan' => $dynStartIdx, 'class' => 'text-center']],
];

// Fetch selected processes based on filter
$selectedProcessIds = [];
if (!empty($planningIds)) {
    $selectedProcessIds = $planningIds;
} else {
    foreach ($processOptions as $proc) {
        $selectedProcessIds[] = $proc->id;
    }
}
$selectedPlanningProcesses = [];
foreach ($processOptions as $proc) {
    if (in_array($proc->id, $selectedProcessIds)) {
        $selectedPlanningProcesses[] = $proc;
    }
}

foreach ($selectedPlanningProcesses as $proc) {
    $opts = isset($planningOptionsMap[$proc->id]) ? $planningOptionsMap[$proc->id] : [];
    $selectData = [];
    foreach ($opts as $opt) {
        if ($opt->label !== '') {
            $selectData[$opt->id] = $opt->label;
        }
    }

    $beforeHeaderColumns[] = [
        'content' => Html::encode($proc->nama_proses),
        'options' => ['colspan' => 3, 'class' => 'text-center warning', 'style' => 'vertical-align: middle; font-weight: bold; background-color: #fcf8e3;']
    ];

    $gridColumns[] = [
        'label' => "Siap",
        'value' => function($data) use ($proc, &$kartuPlanningsMap) {
            $kp = isset($kartuPlanningsMap[$data->id][$proc->id]) ? $kartuPlanningsMap[$data->id][$proc->id] : null;
            return ($kp && $kp->is_siap) ? 'Sudah' : 'Belum';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ];

    $gridColumns[] = [
        'label' => "Keterangan",
        'value' => function($data) use ($proc, &$kartuPlanningsMap, $selectData) {
            $kp = isset($kartuPlanningsMap[$data->id][$proc->id]) ? $kartuPlanningsMap[$data->id][$proc->id] : null;
            $option_id = $kp ? $kp->option_id : '';
            return isset($selectData[$option_id]) ? $selectData[$option_id] : '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ];

    $gridColumns[] = [
        'label' => "Catatan",
        'value' => function($data) use ($proc, &$kartuPlanningsMap) {
            $kp = isset($kartuPlanningsMap[$data->id][$proc->id]) ? $kartuPlanningsMap[$data->id][$proc->id] : null;
            return $kp ? $kp->catatan : '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ];
}

// Now filter columns based on visibleColsArr
$filteredGridColumns = [];
$colIndexMap = []; // Maps old index to new index, or false if hidden

if (!empty($visibleColsArr)) {
    // Convert array of string ints to actual ints
    $visibleColsArr = array_map('intval', $visibleColsArr);
    
    foreach ($gridColumns as $i => $col) {
        if (in_array($i, $visibleColsArr)) {
            $filteredGridColumns[] = $col;
            $colIndexMap[$i] = true;
        } else {
            $colIndexMap[$i] = false;
        }
    }
} else {
    // If visibleCols is empty for some reason, show all
    $filteredGridColumns = $gridColumns;
    foreach ($gridColumns as $i => $col) {
        $colIndexMap[$i] = true;
    }
}

// Adjust beforeHeader colspan
// Base columns span
$baseSpan = 0;
for ($i = 0; $i < $dynStartIdx; $i++) {
    if ($colIndexMap[$i]) $baseSpan++;
}
$filteredBeforeHeaderColumns = [];
if ($baseSpan > 0) {
    $filteredBeforeHeaderColumns[] = ['content' => '', 'options' => ['colspan' => $baseSpan, 'class' => 'text-center']];
}

// Dynamic columns span
$curIdx = $dynStartIdx;
foreach ($selectedPlanningProcesses as $proc) {
    $procSpan = 0;
    for ($i = 0; $i < 3; $i++) {
        if ($colIndexMap[$curIdx + $i]) $procSpan++;
    }
    $curIdx += 3;
    
    if ($procSpan > 0) {
        $filteredBeforeHeaderColumns[] = [
            'content' => Html::encode($proc->nama_proses),
            'options' => ['colspan' => $procSpan, 'class' => 'text-center warning', 'style' => 'vertical-align: middle; font-weight: bold; background-color: #fcf8e3;']
        ];
    }
}

?>

<style>
    table, th, td {
        border: 1px solid #dddddd;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    th, td {
        padding: 5px;
    }
    th {
        background-color: #f4f4f4;
    }
</style>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $filteredGridColumns,
    'layout' => '{items}',
    'export' => false,
    'beforeHeader' => [
        [
            'columns' => $filteredBeforeHeaderColumns
        ]
    ],
]); ?>
