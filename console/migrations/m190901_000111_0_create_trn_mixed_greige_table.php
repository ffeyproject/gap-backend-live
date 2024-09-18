<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000111_0_create_trn_mixed_greige_table extends Migration
{
    const TABLE_NAME = "trn_mixed_greige";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'greige_id' => $this->integer()->unsigned()->notNull()->comment('di mix menjadi greige ini'),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');

        //penambahan kolom pada tabel trn_stock_greige
        $this->addColumn('trn_stock_greige', 'is_hasil_mix', $this->boolean()->notNull()->defaultValue(false));
    }
}
