<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000005_create_customer_table extends Migration
{
    const TABLE_NAME = "mst_customer";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'cust_no' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'telp' => $this->string(50)->notNull(),
            'fax' => $this->string(50),
            'email' => $this->string(255),
            'address' => $this->text(),
            'cp_name' => $this->string(255),
            'cp_phone' => $this->string(50),
            'cp_email' => $this->string(255),
            'npwp' => $this->string(255),
            'aktif' => $this->boolean()->defaultValue(true)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
