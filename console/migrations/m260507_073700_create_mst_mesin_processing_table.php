<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_mesin_processing}}`.
 */
class m260507_073700_create_mst_mesin_processing_table extends Migration
{
    const TABLE_NAME = 'mst_mesin_processing';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'nama_mesin' => $this->string()->notNull(),
            'jenis_mesin' => $this->string(),
            'jenis_nozzle' => $this->string(),
            'ukuran_nozzle' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
