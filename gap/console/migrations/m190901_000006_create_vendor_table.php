<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000006_create_vendor_table extends Migration
{
    const TABLE_NAME = "mst_vendor";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'telp' => $this->string(50)->notNull(),
            'fax' => $this->string(50),
            'email' => $this->string(255),
            'address' => $this->text(),
            'cp_name' => $this->string(255),
            'aktif' => $this->boolean()->defaultValue(true)
        ]);

        //$this->execute("SELECT setval('mst_vendor_id_seq', (SELECT MAX(id) FROM mst_vendor)+1)");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
