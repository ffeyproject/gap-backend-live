<?php
use yii\helpers\Html;
use backend\components\ajax_modal\AjaxModal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $topBuyers array */
/* @var $summary array */
/* @var $currentYear string */
/* @var $isMarketing bool */

$this->title = 'Dashboard';

if ($isMarketing):
    $this->title = 'Dashboard Marketing';
    echo AjaxModal::widget([
        'id' => 'modalMonthly',
        'size' => 'modal-lg',
        'header' => '<h4 class="modal-title">Ringkasan Sales Contract</h4>',
    ]);
?>
<div class="site-index">

    <div class="row">
        <div class="col-md-12">
            <h1 class="text-primary"><i class="glyphicon glyphicon-dashboard"></i> Dashboard Marketing - <?= $currentYear ?></h1>
            <p class="text-muted">Selamat datang kembali, <strong><?= Yii::$app->user->identity->username ?></strong>.</p>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary" style="box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="glyphicon glyphicon-file" style="font-size: 4em; opacity: 0.8;"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div style="font-size: 2.5em; font-weight: bold;"><?= number_format($summary['total_sc'] ?? 0) ?></div>
                            <div style="font-size: 1.2em;">Total SC (<?= $currentYear ?>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-success" style="box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="glyphicon glyphicon-tags" style="font-size: 4em; opacity: 0.8;"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div style="font-size: 2.5em; font-weight: bold;"><?= number_format($summary['total_qty_batch'] ?? 0, 2) ?></div>
                            <div style="font-size: 1.2em;">Total Qty Batch (<?= $currentYear ?>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px;">
        <div class="col-md-8">
            <div class="panel panel-default" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div class="panel-heading" style="background-color: #f8f9fa; border-bottom: 2px solid #337ab7;">
                    <h3 class="panel-title" style="font-weight: bold; color: #333;">
                        <i class="glyphicon glyphicon-stats text-primary"></i> Daftar Buyer Tahun <?= $currentYear ?>
                    </h3>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead style="background-color: #f1f1f1;">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Nama Buyer</th>
                                <th class="text-right" style="padding-right: 20px;">Total Qty Batch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topBuyers)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted" style="padding: 30px;">
                                        <i class="glyphicon glyphicon-info-sign"></i> Tidak ada data kontrak yang ditemukan untuk tahun <?= $currentYear ?>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topBuyers as $index => $buyer): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td style="font-weight: 500;">
                                            <?= Html::a(Html::encode($buyer['buyer_name']), ['sc-by-month', 'buyerName' => $buyer['buyer_name']], [
                                                'class' => 'text-primary',
                                                'style' => 'text-decoration: underline; cursor: pointer;',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modalMonthly',
                                                'data-title' => 'Detail Bulanan: ' . $buyer['buyer_name'],
                                            ]) ?>
                                        </td>
                                        <td class="text-right" style="padding-right: 20px;">
                                            <span class="label label-primary" style="font-size: 0.9em;"><?= number_format($buyer['total_qty_batch'], 2) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer text-right" style="background-color: #fff;">
                    <small class="text-muted">* Klik pada nama buyer untuk melihat detail per bulan. Status: Semua kecuali Draft dan Batal</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(document).on('click', '.ajax-modal-click', function(e){
    e.preventDefault();
    var href = $(this).attr('href');
    var title = $(this).data('title');
    var modal = $('#modalMonthly');
    
    modal.find('.modal-title').html(title);
    modal.find('.modal-body').html('<div class="ajax-modal-loader"></div>');
    
    $.get(href).done(function(result){
        modal.find('.modal-body').html(result);
    }).fail(function(xhr){
        modal.find('.modal-body').html('<div class="text-danger">Gagal memuat data.</div>');
    });
});
JS;
$this->registerJs($js);

else: 
    // PREMIUM WELCOME DESIGN FOR NON-MARKETING USERS
    $bgImage = 'grand_production_background_1777652809309.png'; // Path to generated image
    $assetUrl = Yii::getAlias('@web');
?>
<style>
    .welcome-container {
        height: 80vh;
        width: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: url('<?= $assetUrl . "/" . $bgImage ?>') no-repeat center center;
        background-size: cover;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .welcome-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(20, 30, 48, 0.7) 0%, rgba(36, 59, 85, 0.7) 100%);
    }

    .glass-card {
        position: relative;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 30px;
        padding: 60px;
        text-align: center;
        max-width: 700px;
        width: 90%;
        color: white;
        animation: fadeInUp 1s ease-out;
        box-shadow: 0 25px 45px rgba(0,0,0,0.2);
    }

    .glass-card h1 {
        font-size: 3.5em;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(to right, #fff, #a5c9ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .glass-card p {
        font-size: 1.4em;
        opacity: 0.9;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .welcome-icon {
        font-size: 5em;
        margin-bottom: 30px;
        color: #3498db;
        animation: float 3s ease-in-out infinite;
    }

    .btn-explore {
        background: linear-gradient(45deg, #3498db, #2ecc71);
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        color: white;
        font-size: 1.2em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3);
    }

    .btn-explore:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 15px 30px rgba(46, 204, 113, 0.4);
        color: white;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    .feature-dots {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 40px;
    }

    .dot {
        width: 12px;
        height: 12px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
    }

    .dot.active {
        background: #3498db;
        box-shadow: 0 0 10px #3498db;
    }
</style>

<div class="welcome-container">
    <div class="welcome-overlay"></div>
    <div class="glass-card">
        <div class="welcome-icon">
            <i class="glyphicon glyphicon-globe"></i>
        </div>
        <h1>Selamat Datang!</h1>
        <p>Anda telah berhasil masuk ke sistem <strong>Produksi Online</strong>.</p>
        
        <div style="margin-top: 20px;">
            <a href="<?= Url::to(['/site/index']) ?>" class="btn btn-explore">Akses Dashboard Utama <i class="glyphicon glyphicon-th-large" style="font-size: 0.8em; margin-left: 10px;"></i></a>
        </div>

        <div class="feature-dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
</div>
<?php endif; ?>