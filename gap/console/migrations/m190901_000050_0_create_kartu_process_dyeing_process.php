<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000050_0_create_kartu_process_dyeing_process extends Migration
{
    const TABLE_NAME = "kartu_process_dyeing_process";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'kartu_process_id' => $this->integer()->unsigned(),
            'process_id' => $this->integer()->unsigned(),
            'value' => $this->text()->notNull(),
            'note' => $this->text()
        ]);

        $this->addPrimaryKey('kartu_process_dyeing_process_pk', self::TABLE_NAME, ['kartu_process_id', 'process_id']);
        $this->addForeignKey('fk_kartu_process_dyeing_process_kp', self::TABLE_NAME, 'kartu_process_id', 'trn_kartu_proses_dyeing', 'id');
        $this->addForeignKey('fk_kartu_process_dyeing_process_pc', self::TABLE_NAME, 'process_id', 'mst_process_dyeing', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_kartu_process_dyeing_process_pc', self::TABLE_NAME);
        $this->dropForeignKey('fk_kartu_process_dyeing_process_kp', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
