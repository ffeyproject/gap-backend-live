<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ar\TrnKartuProsesDyeing */
/* @var $items common\models\ar\TrnKartuProsesDyeingItem[] */

$this->title = 'Split Kartu Proses Dyeing: ' . $model->nomor_kartu;
$this->params['breadcrumbs'][] = ['label' => 'Processing Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Detail ' . $model->nomor_kartu, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Split';

// Parse prospective split numbers for frontend display
$nomor_kartu = $model->nomor_kartu;
$parts = explode('/', $nomor_kartu);
$part1 = $parts[0];
$part2 = isset($parts[1]) ? '/' . $parts[1] : '';

// Find the next available split suffix (S1, S2, S3, etc.)
$seq = 1;
$s_nomor = '';
while (true) {
    $s_nomor = $part1 . 'S' . $seq . $part2;
    $exists = \common\models\ar\TrnKartuProsesDyeing::find()->where(['nomor_kartu' => $s_nomor])->exists();
    if (!$exists) {
        break;
    }
    $seq++;
}

// Custom stylish CSS for premium look and micro-interactions
$this->registerCss("
    :root {
        --primary-gradient: linear-gradient(135deg, #7158e2, #cd84f1);
        --s1-gradient: linear-gradient(135deg, #20bf6b, #0be881);
        --s2-gradient: linear-gradient(135deg, #0984e3, #00cec9);
        --danger-gradient: linear-gradient(135deg, #ff4d4d, #ff7675);
    }
    
    .split-header-card {
        background: linear-gradient(135deg, #1e272e, #2d3436);
        color: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        position: relative;
        overflow: hidden;
    }
    .split-header-card::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .split-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 10px 0;
        letter-spacing: 0.5px;
    }
    
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .meta-item {
        background: rgba(255, 255, 255, 0.08);
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .meta-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #a4b0be;
        margin-bottom: 3px;
        font-weight: 600;
    }
    .meta-val {
        font-size: 14px;
        font-weight: 600;
    }

    .workspace-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid #f1f2f6;
    }
    
    .table-split-items th {
        background-color: #f8f9fa;
        color: #2f3542;
        font-weight: 600;
        border-bottom: 2px solid #e4e7eb !important;
    }
    
    .row-interactive {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .row-interactive:hover {
        background-color: #f1f2f6 !important;
    }
    .row-interactive.selected-s1 {
        background-color: rgba(32, 191, 107, 0.05) !important;
    }
    .row-interactive.selected-s2 {
        background-color: rgba(9, 132, 227, 0.05) !important;
    }

    /* Custom Checkbox Style */
    .custom-control {
        position: relative;
        display: inline-block;
        width: 22px;
        height: 22px;
    }
    .custom-control input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .custom-checkbox-btn {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #dfe4ea;
        transition: .3s;
        border-radius: 6px;
    }
    .custom-control input:checked + .custom-checkbox-btn {
        background: var(--s1-gradient);
    }
    .custom-checkbox-btn:after {
        content: '';
        position: absolute;
        display: none;
    }
    .custom-control input:checked + .custom-checkbox-btn:after {
        display: block;
    }
    .custom-control .custom-checkbox-btn:after {
        left: 8px;
        top: 4px;
        width: 6px;
        height: 11px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Elegant badges */
    .destination-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 20px;
        color: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .badge-s1 {
        background: var(--s1-gradient);
    }
    .badge-s2 {
        background: var(--s2-gradient);
    }

    .btn-gradient-split {
        background: var(--primary-gradient);
        color: #fff !important;
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(113, 88, 226, 0.3);
        transition: all 0.3s ease;
    }
    .btn-gradient-split:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(113, 88, 226, 0.4);
    }
    .btn-gradient-split:active {
        transform: translateY(1px);
    }
    
    .btn-back-stylish {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-back-stylish:hover {
        background-color: #f1f2f6;
    }

    .summary-bar {
        display: flex;
        justify-content: space-around;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid #e4e7eb;
    }
    .summary-card-inner {
        text-align: center;
    }
    .summary-count {
        font-size: 20px;
        font-weight: 800;
    }
    .summary-count.s1 { color: #20bf6b; }
    .summary-count.s2 { color: #0984e3; }
    .summary-label {
        font-size: 11px;
        color: #747d8c;
        text-transform: uppercase;
        font-weight: 600;
    }
");
?>

<div class="processing-dyeing-split-view">

    <!-- Header Card -->
    <div class="split-header-card">
        <div class="row">
            <div class="col-md-8">
                <h1 class="split-title">✂️ Split Kartu Proses Dyeing</h1>
                <p style="color: #ced6e0; margin-bottom: 0;">
                    Pindahkan item gulungan (rolls) pilihan dari kartu proses induk ini ke dalam kartu split baru (<strong><?= Html::encode($s_nomor) ?></strong>). Item yang tidak dicentang akan tetap berada pada kartu induk ini.
                </p>
            </div>
            <div class="col-md-4 text-right">
                <?= Html::a('← Kembali ke Detail', ['view', 'id' => $model->id], ['class' => 'btn btn-default btn-back-stylish']) ?>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-label">Nomor Kartu Induk</div>
                <div class="meta-val"><?= Html::encode($model->nomor_kartu) ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">No. Dokumentasi</div>
                <div class="meta-val"><?= Html::encode($model->no) ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Nomor WO</div>
                <div class="meta-val"><?= Html::encode($model->wo->no) ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Motif Greige</div>
                <div class="meta-val"><?= Html::encode($model->wo->greige->nama_kain) ?></div>
            </div>
        </div>
    </div>

    <!-- Main split workspace -->
    <?php $form = ActiveForm::begin(['id' => 'split-form']); ?>

    <div class="workspace-card">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #2f3542;">
             распределение Roll / Item Workspace
        </h3>

        <!-- Dynamic Summary counters -->
        <div class="summary-bar">
            <div class="summary-card-inner">
                <div class="summary-count" id="total-rolls-count"><?= count($items) ?></div>
                <div class="summary-label">Total Rolls</div>
            </div>
            <div style="border-left: 1px solid #dfe4ea;"></div>
            <div class="summary-card-inner">
                <div class="summary-count s1" id="s1-rolls-count">0</div>
                <div class="summary-label">Pindah Ke Split (<?= Html::encode($s_nomor) ?>)</div>
            </div>
            <div style="border-left: 1px solid #dfe4ea;"></div>
            <div class="summary-card-inner">
                <div class="summary-count s2" id="s2-rolls-count"><?= count($items) ?></div>
                <div class="summary-label">Tetap di Induk (<?= Html::encode($model->nomor_kartu) ?>)</div>
            </div>
        </div>

        <p class="text-info" style="font-weight: 600; margin-bottom: 15px;">
            💡 <strong>Petunjuk:</strong> Centang item yang ingin Anda pindahkan ke dalam kartu split baru <strong><?= Html::encode($s_nomor) ?></strong>. Item yang tidak dicentang akan tetap berada di kartu induk <strong><?= Html::encode($model->nomor_kartu) ?></strong>.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-split-items" id="itemsTable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">Pilih S1</th>
                        <th class="text-center">Roll No / ID</th>
                        <th>Grade</th>
                        <th>Tube</th>
                        <th>Lot MC Weaving</th>
                        <th class="text-right">Panjang (M)</th>
                        <th>Keterangan Weaving</th>
                        <th class="text-center">Tujuan Kartu Baru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                        <?php 
                            $stock = $item->stock; 
                            $gradeLabel = $stock->gradeName;
                            $tsdLabel = $stock->kondisiGreige;
                        ?>
                        <tr class="row-interactive selected-s2" data-item-id="<?= $item->id ?>">
                            <td class="text-center" style="vertical-align: middle;">
                                <label class="custom-control">
                                    <input type="checkbox" name="selected_items[]" value="<?= $item->id ?>" class="split-checkbox" onchange="updateRowState(this);">
                                    <span class="custom-checkbox-btn"></span>
                                </label>
                            </td>
                            <td class="text-center" style="vertical-align: middle; font-weight: 600;">
                                <?= Html::encode($stock->id) ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <span class="label label-default" style="font-size: 11px; padding: 4px 8px;"><?= Html::encode($gradeLabel) ?></span>
                            </td>
                            <td style="vertical-align: middle; font-weight: 600;">
                                <?= Html::encode($item->tubeOptions()[$item->tube] ?? '-') ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?= Html::encode($stock->lot_lusi) ?> / <?= Html::encode($stock->lot_pakan) ?>
                            </td>
                            <td class="text-right" style="vertical-align: middle; font-weight: 700; color: #2f3542;">
                                <?= number_format($item->panjang_m, 2) ?> M
                            </td>
                            <td style="vertical-align: middle; color: #747d8c;">
                                <?= Html::encode($tsdLabel) ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <span class="destination-badge badge-s2" id="badge-dest-<?= $item->id ?>">
                                    <?= Html::encode($model->nomor_kartu) ?> (Induk)
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px;" class="text-right">
            <?= Html::submitButton('⚡ Jalankan Split Kartu', [
                'class' => 'btn btn-gradient-split',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin memecah kartu proses ini? Item yang dicentang akan dipindahkan ke kartu split baru, sedangkan sisanya tetap berada pada kartu induk.',
                    'method' => 'post',
                ]
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// Dynamic client-side reactive workspace interactions script
$s1NomorJs = json_encode($s_nomor);
$s2NomorJs = json_encode($model->nomor_kartu);
$js = <<<JS
    // Make whole table rows clickable (excluding checkbox directly to prevent double toggle)
    $('#itemsTable tbody tr').on('click', function(e) {
        if ($(e.target).is('input') || $(e.target).is('.custom-checkbox-btn') || $(e.target).is('label')) {
            return;
        }
        var chk = $(this).find('.split-checkbox');
        chk.prop('checked', !chk.prop('checked')).trigger('change');
    });

    window.updateRowState = function(chk) {
        var row = $(chk).closest('tr');
        var itemId = row.data('item-id');
        var badge = $('#badge-dest-' + itemId);
        
        if (chk.checked) {
            row.removeClass('selected-s2').addClass('selected-s1');
            badge.removeClass('badge-s2').addClass('badge-s1');
            badge.text({$s1NomorJs} + ' (Split)');
        } else {
            row.removeClass('selected-s1').addClass('selected-s2');
            badge.removeClass('badge-s1').addClass('badge-s2');
            badge.text({$s2NomorJs} + ' (Induk)');
        }
        
        // Update summary counters
        var total = $('.split-checkbox').length;
        var s1Count = $('.split-checkbox:checked').length;
        var s2Count = total - s1Count;
        
        $('#s1-rolls-count').text(s1Count);
        $('#s2-rolls-count').text(s2Count);
    }
JS;
$this->registerJs($js);
?>
