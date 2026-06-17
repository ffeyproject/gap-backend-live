<?php

use yii\db\Migration;

/**
 * Class m260617_073332_add_nama_warna_to_trn_kartu_proses_dyeing_table
 */
class m260617_073332_add_nama_warna_to_trn_kartu_proses_dyeing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('trn_kartu_proses_dyeing');
        if (!isset($table->columns['nama_warna'])) {
            $this->addColumn('trn_kartu_proses_dyeing', 'nama_warna', $this->string(255)->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->schema->getTableSchema('trn_kartu_proses_dyeing');
        if (isset($table->columns['nama_warna'])) {
            $this->dropColumn('trn_kartu_proses_dyeing', 'nama_warna');
        }
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260617_073332_add_nama_warna_to_trn_kartu_proses_dyeing_table cannot be reverted.\n";

        return false;
    }
    */
}
