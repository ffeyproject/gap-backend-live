<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ar\TrnStockGreige;

/* @var $this yii\web\View */
/* @var $model common\models\ar\MstGreige */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Greiges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$stockPerGrade = $model->stockPerGrade;
echo Dialog::widget(['overrideYiiConfirm' => true]);

?>



<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    <?= Html::a('Add New', ['create'], ['class' => 'btn btn-default']) ?>
</p>

<div class="box">
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'value' => $model->group->nama_kain,
                    'label' => 'Group'
                ],
                'nama_kain',
                'alias',
                'no_dok_referensi',
                'gap',
                'created_at:datetime',
                'created_by',
                'updated_at:datetime',
                'updated_by',
                'aktif:boolean',
                'stock:decimal',
                'stock_opname:decimal',
                'available:decimal',
                [
                    'value' => $model->getTotalPanjangMGudangInspect(),
                    'label' => 'Stock Gudang Inspect'
                ],
                // [
                //     'value' => $model->getTotalPanjangMGudangStockOpname(),
                //     'label' => 'Stock Opname'
                // ],
                'booked_wo:decimal',
                'booked_opfp:decimal',
                'booked:decimal',
                'stock_pfp:decimal',
                'available_pfp:decimal',
                'booked_pfp:decimal',
                'stock_wip:decimal',
                'booked_wip:decimal',
                'stock_ef:decimal',
                'booked_ef:decimal'
            ],
        ]) ?>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Stock Available for greige" <?=$model->group->nama_kain?>".</h3>
        <div class="box-tools pull-right">
            <span class="label label-info">Satuan: <?=$model->group->unitName?></span>
        </div>
    </div>
    <div class="box-body">
        <p>
            Menampilkan Available Stock untuk greige "<?=$model->group->nama_kain?>" berdasarkan Grade.
        </p>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Grade A</th>
                    <th>Grade B</th>
                    <th>Grade C</th>
                    <th>Grade D</th>
                    <th>Grade E</th>
                    <th>NO Grade</th>
                    <th>A+</th>
                    <th>A*</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_A]) ? $stockPerGrade[TrnStockGreige::GRADE_A] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_B]) ? $stockPerGrade[TrnStockGreige::GRADE_B] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_C]) ? $stockPerGrade[TrnStockGreige::GRADE_C] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_D]) ? $stockPerGrade[TrnStockGreige::GRADE_D] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_E]) ? $stockPerGrade[TrnStockGreige::GRADE_E] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_NG]) ? $stockPerGrade[TrnStockGreige::GRADE_NG] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_A_PLUS]) ? $stockPerGrade[TrnStockGreige::GRADE_A_PLUS] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade[TrnStockGreige::GRADE_A_ASTERISK]) ? $stockPerGrade[TrnStockGreige::GRADE_A_ASTERISK] : 0 ?>
                    </td>
                    <td><?= isset($stockPerGrade['total']) ? $stockPerGrade['total'] : 0 ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="box-footer"></div>
</div>