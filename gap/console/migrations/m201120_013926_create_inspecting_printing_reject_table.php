<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspecting_printing_reject}}`.
 */
class m201120_013926_create_inspecting_printing_reject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspecting_printing_reject}}', [
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

        $this->addForeignKey('fk_inspecting_printing_reject_kartu_proses', '{{%inspecting_printing_reject}}', 'kartu_proses_id', 'trn_kartu_proses_printing', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inspecting_printing_reject}}');
    }
}
