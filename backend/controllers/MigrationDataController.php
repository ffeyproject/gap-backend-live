<?php
namespace backend\controllers;

use backend\modules\rawdata\models\MstHandling;
use common\models\ar\MstGreige;
use common\models\ar\MstProcessDyeing;
use common\models\ar\MstProcessPfp;
use common\models\ar\MstProcessPrinting;
use common\models\ar\TrnStockGreige;
use yii\db\Query;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;

class MigrationDataController extends Controller
{
    /**
     */
    public function actionAuthItem(){
        $rows = (new Query())
            ->from('auth_item')
            ->orderBy('type')
            ->all();

        $outs = [];
        foreach ($rows as $value) {
            $outs[] = '$this->insert(\'auth_item\', [\'name\'=>\''.$value['name'].'\', \'type\'=>\''.$value['type'].'\', \'created_at\'=>\''.$value['created_at'].'\', \'updated_at\'=>\''.$value['updated_at'].'\']);';
        }
        $out = implode('<br>', $outs);
        echo $out;
        //BaseVarDumper::dump($rows, 10, true);
    }

    /**
     */
    public function actionAuthItemChild(){
        $rows = (new Query())
            ->from('auth_item_child')
            ->all();

        $outs = [];
        foreach ($rows as $value) {
            $outs[] = '$this->insert(\'auth_item_child\', [\'parent\'=>\''.$value['parent'].'\', \'child\'=>\''.$value['child'].'\']);';
        }
        $out = implode('<br>', $outs);
        echo $out;
        //BaseVarDumper::dump($rows, 10, true);
    }

    /**
     */
    public function actionGreigeGroup(){
        $rows = (new Query())
            ->from('mst_greige_group')
            ->orderBy('id')
            ->all();

        $outs = [];
        foreach ($rows as $value) {
            $kainName = str_replace("'", '\"', $value['nama_kain']);
            $sulamPinggir = str_replace("'", '\"', $value['sulam_pinggir']);
            $outs[] = '$this->insert(\'mst_greige_group\', [\'id\'=>\''.$value['id'].'\', \'jenis_kain\'=>\''.$value['jenis_kain'].'\', \'nama_kain\'=>\''.$kainName.'\', \'qty_per_batch\'=>\''.$value['qty_per_batch'].'\', \'unit\'=>\''.$value['unit'].'\', \'nilai_penyusutan\'=>\''.$value['nilai_penyusutan'].'\', \'gramasi_kain\'=>\''.$value['gramasi_kain'].'\', \'sulam_pinggir\'=>\''.$sulamPinggir.'\', \'created_at\'=>\''.$value['created_at'].'\', \'created_by\'=>\''.$value['created_by'].'\', \'updated_at\'=>\''.$value['updated_at'].'\', \'updated_by\'=>\''.$value['updated_by'].'\', \'aktif\'=>\''.$value['aktif'].'\']);';
        }
        $outs[] = '$this->execute("SELECT setval(\'mst_greige_group_id_seq\', (SELECT MAX(id) FROM mst_greige_group)+1)");';
        $out = implode('<br>', $outs);
        echo $out;
        //BaseVarDumper::dump($rows, 10, true);
    }

    /**
     */
    public function actionGreige(){
        $model = new MstGreige();
        $tableName = MstGreige::tableName();

        $datas = (new Query())
            ->from($tableName)
            ->orderBy('id')
            ->all();

        $outs = [];
        foreach ($datas as $data) {
            $field =[];
            foreach ($model->attributes as $key=>$attribute) {
                if($data[$key] === false){
                    $val = '0';
                }elseif ($data[$key] === true){
                    $val = '1';
                }else{
                    if(in_array($key, ['stock', 'stock_pfp', 'stock_wip', 'stock_ef', 'booked', 'booked_pfp', 'booked_wip', 'booked_ef'])){
                        $val = '0';
                    }else{
                        if(in_array($key, ['nama_kain', 'alias'])){
                            $val = str_replace("'", '\"', $data[$key]);
                        }else{
                            $val = $data[$key];
                        }
                    }
                }

                $field[] = "'{$key}' => '{$val}'";
            }
            $fieldStr = implode(', ', $field);
            $outs[] = "\$this->insert('{$tableName}', [{$fieldStr}]);";
        }
        $outs[] = "\$this->execute(\"SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName})+1)\");";
        $out = implode('<br>', $outs);
        echo $out;
    }

    /**
     */
    public function actionStockGreige(){
        $model = new TrnStockGreige();
        $tableName = TrnStockGreige::tableName();

        $datas = (new Query())
            ->from($tableName)
            ->orderBy('id')
            ->all();

        $outs = [];
        foreach ($datas as $data) {
            $field =[];
            foreach ($model->attributes as $key=>$attribute) {
                if($data[$key] === false){
                    $val = '0';
                }elseif ($data[$key] === true){
                    $val = '1';
                }else{
                    $val = $data[$key];
                }
                $field[] = "'{$key}' => '{$val}'";
            }
            $fieldStr = implode(', ', $field);
            $outs[] = "\$this->insert('{$tableName}', [{$fieldStr}]);";
        }
        $outs[] = "\$this->execute(\"SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName})+1)\");";
        $out = implode('<br>', $outs);
        echo $out;
    }

    public function actionMstProcessDyeing(){
        $model = new MstProcessDyeing();
        $tableName = MstProcessDyeing::tableName();
        $datas = (new Query())
            ->from($tableName)
            ->orderBy('id')
            ->all();
        $outs = [];
        foreach ($datas as $data) {
            $field =[];
            foreach ($model->attributes as $key=>$attribute) {
                if($data[$key] === false){
                    $val = '0';
                }elseif ($data[$key] === true){
                    $val = '1';
                }else{
                    $val = $data[$key];
                }
                $field[] = "'{$key}' => '{$val}'";
            }
            $fieldStr = implode(', ', $field);
            $outs[] = "\$this->insert('{$tableName}', [{$fieldStr}]);";
        }
        $outs[] = "\$this->execute(\"SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName})+1)\");";
        $out = implode('<br>', $outs);
        echo $out;
    }

    public function actionMstProcessPfp(){
        $model = new MstProcessPfp();
        $tableName = MstProcessPfp::tableName();
        $datas = (new Query())
            ->from($tableName)
            ->orderBy('id')
            ->all();
        $outs = [];
        foreach ($datas as $data) {
            $field =[];
            foreach ($model->attributes as $key=>$attribute) {
                if($data[$key] === false){
                    $val = '0';
                }elseif ($data[$key] === true){
                    $val = '1';
                }else{
                    $val = $data[$key];
                }
                $field[] = "'{$key}' => '{$val}'";
            }
            $fieldStr = implode(', ', $field);
            $outs[] = "\$this->insert('{$tableName}', [{$fieldStr}]);";
        }
        $outs[] = "\$this->execute(\"SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName})+1)\");";
        $out = implode('<br>', $outs);
        echo $out;
    }

    public function actionMstProcessPrinting(){
        $model = new MstProcessPrinting();
        $tableName = MstProcessPrinting::tableName();
        $datas = (new Query())
            ->from($tableName)
            ->orderBy('id')
            ->all();
        $outs = [];
        foreach ($datas as $data) {
            $field =[];
            foreach ($model->attributes as $key=>$attribute) {
                if($data[$key] === false){
                    $val = '0';
                }elseif ($data[$key] === true){
                    $val = '1';
                }else{
                    $val = $data[$key];
                }
                $field[] = "'{$key}' => '{$val}'";
            }
            $fieldStr = implode(', ', $field);
            $outs[] = "\$this->insert('{$tableName}', [{$fieldStr}]);";
        }
        $outs[] = "\$this->execute(\"SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName})+1)\");";
        $out = implode('<br>', $outs);
        echo $out;
    }

    public function actionMstHandling(){
        $model = new MstHandling();
        $tableName = MstHandling::tableName();
        $datas = (new Query())
            ->from($tableName)
            ->orderBy('id')
            ->all();
        $outs = [];
        foreach ($datas as $data) {
            $field =[];
            foreach ($model->attributes as $key=>$attribute) {
                if($data[$key] === false){
                    $val = '0';
                }elseif ($data[$key] === true){
                    $val = '1';
                }else{
                    $val = $data[$key];
                }
                $field[] = "'{$key}' => '{$val}'";
            }
            $fieldStr = implode(', ', $field);
            $outs[] = "\$this->insert('{$tableName}', [{$fieldStr}]);";
        }
        $outs[] = "\$this->execute(\"SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName})+1)\");";
        $out = implode('<br>', $outs);
        echo $out;
    }
}
