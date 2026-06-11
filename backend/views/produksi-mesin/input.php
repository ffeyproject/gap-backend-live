<?php
use yii\helpers\Html;

$this->title = 'Input Data Produksi Mesin';
$this->params['breadcrumbs'][] = ['label' => 'Input Produksi Mesin', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Input Data';

// Convert no_mesin array to string for display if needed
$noMesinStr = is_array($no_mesin) ? implode(', ', $no_mesin) : $no_mesin;
?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Jenis Mesin:</strong> <?= Html::encode($jenis_mesin) ?> | 
            <strong>No Mesin:</strong> <?= Html::encode($noMesinStr) ?> | 
            <strong>Tanggal:</strong> <?= Html::encode($tanggal) ?> | 
            <strong>Shift:</strong> <?= Html::encode($shift) ?>
        </div>
    </div>
</div>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Data Proses Dyeing (Existing)</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Nama Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <!-- Sesuai request: Tanggal, Shift, Perbaikan, Ulang, Gangguan Produksi dihilangkan -->
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="14" class="text-center"><em>Data existing akan ditampilkan di sini...</em></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Data Proses PFP (Existing)</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Nama Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <!-- Sesuai request: Tanggal, Shift, Perbaikan, Ulang, Gangguan Produksi, Use Jetblack dihilangkan -->
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="14" class="text-center"><em>Data existing akan ditampilkan di sini...</em></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">TAMBAHAN INPUT</h3>
    </div>
    <div class="box-body">
        
        <h4>INPUT DYEING</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-input-dyeing">
                <tr>
                    <td><input type="text" class="form-control" placeholder="Cari WO..."></td>
                    <td><input type="text" class="form-control" placeholder="Cari NK..."></td>
                    <td><input type="text" class="form-control" readonly></td>
                    <td><input type="text" class="form-control" readonly></td>
                    <td>
                        <select class="form-control">
                            <option>Pilih Proses...</option>
                        </select>
                    </td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="text" class="form-control" value="<?= Html::encode($noMesinStr) ?>"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><button class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
        <button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>

        <hr>

        <h4>INPUT PFP</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>WO</th>
                    <th>NK</th>
                    <th>Motif</th>
                    <th>Warna</th>
                    <th>Proses</th>
                    <th>Start</th>
                    <th>Stop</th>
                    <th>No Mesin</th>
                    <th>Temp</th>
                    <th>Speed</th>
                    <th>Gramasi</th>
                    <th>Program Number</th>
                    <th>Density</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-input-pfp">
                <tr>
                    <td><input type="text" class="form-control" placeholder="Cari WO..."></td>
                    <td><input type="text" class="form-control" placeholder="Cari NK..."></td>
                    <td><input type="text" class="form-control" readonly></td>
                    <td><input type="text" class="form-control" readonly></td>
                    <td>
                        <select class="form-control">
                            <option>Pilih Proses...</option>
                        </select>
                    </td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="time" class="form-control"></td>
                    <td><input type="text" class="form-control" value="<?= Html::encode($noMesinStr) ?>"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><button class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
        <button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Set Inputan</button>

    </div>
    <div class="box-footer">
        <button class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Simpan Data Input</button>
    </div>
</div>
