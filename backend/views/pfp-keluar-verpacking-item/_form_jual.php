<?php
/* @var $this yii\web\View */

use common\models\ar\TrnSc;
use common\models\ar\TrnScGreige;
use common\models\ar\TrnStockGreige;

?>

<form class="form-horizontal" id="FormMutasi">
    <div class="form-group">
        <label for="NamaBuyer" class="col-sm-2 control-label">Nama Buyer</label>
        <div class="col-sm-6">
            <select id="NamaBuyer" class="form-control" name="customer_id"></select>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="is_resmi" id="isResmiResmi" value="1"> Resmi
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="is_resmi" id="isResmiTidakResmi" value="0"> Tidak Resmi
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Item</label>
        <div class="col-sm-6">
            <input type="text" id="MotifItem" class="form-control" value="Magnolia + camelia + DAISEN 03 + CLEMATIS">
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Quantity</label>
        <div class="col-sm-6">
            <input type="text" id="MotifQty" class="form-control" value="1.000 Yard + 5.000 Meter + 56.000 Kg + 90.000 Pcs">
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Grade</label>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="grade" id="gradeA" value="<?=TrnStockGreige::GRADE_A?>"> A Grade
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="grade" id="gradeB" value="<?=TrnStockGreige::GRADE_B?>"> B Grade
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="grade" id="gradeNG" value="<?=TrnStockGreige::GRADE_NG?>"> NG
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="harga" class="col-sm-2 control-label">Harga</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="harga">
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label for="no_po" class="col-sm-2 control-label">No. PO</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="no_po">
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Ongkir</label>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="ongkir" id="OngkirPembeli" value="<?=TrnSc::ONGKOS_ANGKUT_PEMESAN?>"> Pembeli
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="ongkir" id="OngkirPenjual" value="<?=TrnSc::ONGKOS_ANGKUT_PENJUAL?>"> Penjual
                </label>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <div class="form-group">
        <label for="pembayaran" class="col-sm-2 control-label">Pembayaran</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="pembayaran">
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label for="tglKirim" class="col-sm-2 control-label">Tgl. Pengiriman</label>
        <div class="col-sm-6">
            <div class="input-group date">
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
                <input type="text" class="form-control datepicker" name="tgl_awal" id="tglKirim" readonly>
            </div>
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label for="komisi" class="col-sm-2 control-label">Komisi</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="komisi">
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Jenis Order</label>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="jenisOrder" id="JoDyeing" value="<?=TrnScGreige::PROCESS_DYEING?>"> Dyeing
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="jenisOrder" id="JoPrinting" value="<?=TrnScGreige::PROCESS_PRINTING?>"> Printing
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label>
                    <input type="radio" name="jenisOrder" id="JoPfp" value="<?=TrnScGreige::PROCESS_PFP?>"> PFP
                </label>
            </div>
        </div>
    </div>

    <!--list kebawah dengan format no.wo / nama buyer-->
    <div class="form-group">
        <label class="col-sm-2 control-label">Ex WO / Nama Buyer</label>
        <div class="col-sm-6">
            <!--<select multiple class="form-control">
                <option>no_wo 2 / nama_buyer 1</option>
                <option>no_wo 2 / nama_buyer 2</option>
                <option>no_wo 3 / nama_buyer 3</option>
            </select>-->
            <ul id="ulEl"></ul>
        </div>
        <div class="col-sm-4"></div>
    </div>

    <div class="form-group">
        <label for="keterangan" class="col-sm-2 control-label">Keterangan</label>
        <div class="col-sm-6">
            <textarea class="form-control" rows="3" id="keterangan"></textarea>
        </div>
        <div class="col-sm-4"></div>
    </div>
</form>
