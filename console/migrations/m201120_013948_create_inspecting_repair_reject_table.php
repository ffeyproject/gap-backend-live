<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspecting_repair_reject}}`.
 */
class m201120_013948_create_inspecting_repair_reject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspecting_repair_reject}}', [
            'id' => $this->primaryKey(),
            'memo_repair_id' => $this->integer()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'date' => $this->date()->notNull(),
            'untuk_bagian' => $this->string(),
            'pcs' => $this->string(),
            'keterangan' => $this->text(),
            'penerima' => $this->string(),
            'mengetahui' => $this->string(),
            'pengirim' => $this->string(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addForeignKey('fk_inspecting_repair_reject_kartu_proses', '{{%inspecting_repair_reject}}', 'memo_repair_id', 'trn_memo_repair', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inspecting_repair_reject}}');
    }
}
