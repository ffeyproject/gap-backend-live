<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Synchron Verpacking';
$this->params['breadcrumbs'][] = ['label' => 'PROCESSING', 'url' => '#'];
$this->params['breadcrumbs'][] = ['label' => 'Dyeing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="processing-dyeing-synchron-verpacking">
    <div class="box box-solid" style="border-radius: 8px; border-top: 3px solid #3c8dbc; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div class="box-body" style="padding: 30px;">
            <h3 style="font-weight: 700; color: #2c3e50; margin-top: 0;"><i class="glyphicon glyphicon-transfer" style="color: #3c8dbc; margin-right: 10px;"></i> Sinkronisasi Data Verpacking (Tanggal Packing)</h3>
            <p class="text-muted" style="margin-bottom: 25px; font-size: 14px;">
                Fitur ini akan mengisi tanggal <strong>approved_at</strong> (tanggal masuk packing) pada kartu proses Dyeing yang masih kosong.<br/>
                Sistem akan mencari tanggal aktivitas paling pertama pada Riwayat Perubahan Kartu Proses dari kartu yang bersangkutan.<br/>
                Silakan pilih bulan Buka Greige untuk membatasi ruang lingkup data yang disinkronisasi.
            </p>

            <form method="post" action="<?= Url::to(['synchron-verpacking']) ?>">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #555;">Pilih Bulan Buka Greige:</label>
                    <select name="month" class="form-control" style="max-width: 300px; border-radius: 4px; border: 1px solid #ccc;">
                        <?php foreach ($monthOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= $selectedMonth === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin memulai proses sinkronisasi untuk bulan yang dipilih?');" style="border-radius: 4px; font-weight: bold; background: linear-gradient(135deg, #3498db, #2980b9); border: none; box-shadow: 0 2px 5px rgba(52,152,219,0.3);">
                    <i class="glyphicon glyphicon-refresh"></i> Mulai Sinkronisasi
                </button>
            </form>
        </div>
    </div>
</div>
