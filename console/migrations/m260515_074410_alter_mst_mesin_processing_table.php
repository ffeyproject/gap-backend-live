<?php

use yii\db\Migration;

/**
 * Class m260515_074410_alter_mst_mesin_processing_table
 */
class m260515_074410_alter_mst_mesin_processing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('mst_mesin_processing', 'jenis_mesin');
        $this->dropColumn('mst_mesin_processing', 'jenis_nozzle');
        $this->dropColumn('mst_mesin_processing', 'ukuran_nozzle');

        $this->addColumn('mst_mesin_processing', 'relax_mesin', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'relax_jenis_nozzle', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'relax_ukuran_nozzle', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'relax_catatan', $this->text()->null());
        
        $this->addColumn('mst_mesin_processing', 'celup_mesin', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'celup_jenis_nozzle', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'celup_ukuran_nozzle', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'celup_catatan', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('mst_mesin_processing', 'jenis_mesin', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'jenis_nozzle', $this->string(255)->null());
        $this->addColumn('mst_mesin_processing', 'ukuran_nozzle', $this->string(255)->null());

        $this->dropColumn('mst_mesin_processing', 'relax_mesin');
        $this->dropColumn('mst_mesin_processing', 'relax_jenis_nozzle');
        $this->dropColumn('mst_mesin_processing', 'relax_ukuran_nozzle');
        $this->dropColumn('mst_mesin_processing', 'relax_catatan');

        $this->dropColumn('mst_mesin_processing', 'celup_mesin');
        $this->dropColumn('mst_mesin_processing', 'celup_jenis_nozzle');
        $this->dropColumn('mst_mesin_processing', 'celup_ukuran_nozzle');
        $this->dropColumn('mst_mesin_processing', 'celup_catatan');
    }
}
