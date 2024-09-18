<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspecting_dyeing_reject}}`.
 */
class m201120_013936_create_inspecting_dyeing_reject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspecting_dyeing_reject}}', [
            'id' => $this->primaryKey(),
            'kartu_proses_id' => $this->integer()->notNull(),
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

        $this->addForeignKey('fk_inspecting_dyeing_reject_kartu_proses', '{{%inspecting_dyeing_reject}}', 'kartu_proses_id', 'trn_kartu_proses_dyeing', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inspecting_dyeing_reject}}');
    }
}
