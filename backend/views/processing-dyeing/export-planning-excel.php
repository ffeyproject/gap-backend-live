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

// Convert string to array
$visibleColsArr = $visibleColsStr !== '' ? explode(',', $visibleColsStr) : [];

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
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Tgl. Terima',
        'value' => function($data) use ($formatIndoDate) {
            return $formatIndoDate($data->date);
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Tgl. Janji',
        'value' => function($data) use ($formatIndoDate) {
            return $data->wo ? $formatIndoDate($data->wo->tgl_kirim) : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'woNo',
        'label' => 'No. WO',
        'value' => function($data) {
            return $data->wo ? $data->wo->no : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'motif',
        'label' => 'Motif',
        'value' => function($data) {
            return $data->wo && $data->wo->greige ? $data->wo->greige->nama_kain : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'warna',
        'label' => 'Warna',
        'value' => function($data) {
            return $data->woColor->moColor->color ?? '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'nama_warna',
        'label' => 'Nama Warna',
        'value' => function($data) {
            return !empty($data->nama_warna) ? $data->nama_warna : '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'nomor_kartu',
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
        'label' => 'L/B',
        'value' => function($data) {
            return ($data->panjang ?? 0) . ' / ' . ($data->berat ?? 0);
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'marketingName',
        'label' => 'Marketing',
        'value' => function($data) {
            return $data->mo && $data->mo->scGreige && $data->mo->scGreige->sc && $data->mo->scGreige->sc->marketing 
                ? $data->mo->scGreige->sc->marketing->full_name 
                : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'customerName',
        'label' => 'Customer',
        'value' => function($data) {
            return $data->sc && $data->sc->cust ? $data->sc->cust->name : null;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Toping / Matching',
        'attribute' => 'toping_matching',
        'value' => function($data) {
            return $data->toping_matching ? 'Ya' : 'Tidak';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Tgl Toping / Matching',
        'attribute' => 'dateReangeTopingMatching',
        'value' => function($data) use ($formatIndoDate) {
            return $formatIndoDate($data->date_toping_matching);
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'status',
        'label' => 'Status',
        'value' => function($data) {
            $statuses = [
                1 => 'Draft', 2 => 'Posted', 3 => 'Approved', 
                4 => 'Inspected', 5 => 'Ganti Greige', 6 => 'Batal'
            ];
            return isset($statuses[$data->status]) ? $statuses[$data->status] : '';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Terakhir Proses',
        'value' => function($data) use (&$lastProcessesMap) {
            return isset($lastProcessesMap[$data->id]) ? $lastProcessesMap[$data->id] : '-';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Keterangan Planning Dyeing',
        'value' => function($data) {
            return $data->memo_pg;
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Tgl. Masuk Packing',
        'attribute' => 'dateRangeMasukPacking',
        'value' => function($data) use ($formatIndoDate) {
            return $formatIndoDate($data->approved_at);
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Ready Colour',
        'attribute' => 'ready_colour',
        'value' => function($data) {
            return $data->woColor->ready_colour ? 'Ya' : 'Tidak';
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
    [
        'label' => 'Tgl Ready Colour',
        'attribute' => 'dateRangeReadyColour',
        'value' => function($data) use ($formatIndoDate) {
            return $formatIndoDate($data->woColor->date_ready_colour);
        },
        'hAlign' => 'center',
        'vAlign' => 'middle',
    ],
];

// Rebuild dynamic columns
$beforeHeaderColumns = [
    ['content' => '', 'options' => ['colspan' => 21, 'class' => 'text-center']],
];

// Determine the starting index of dynamic columns
$dynStartIdx = count($gridColumns);

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
