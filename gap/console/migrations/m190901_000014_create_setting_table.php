<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000014_create_setting_table extends Migration
{
    const TABLE_NAME = "sys_setting";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'nama_perusahaan' => $this->string(255)->notNull(),
            'alamat' => $this->text()->notNull(),
            'telp' => $this->string(50)->notNull(),
            'fax' => $this->string(50)->notNull(),
            'email' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $now = time();

        $this->insert(self::TABLE_NAME, [
            'nama_perusahaan' => 'PT. Gajah Angkasa Perkasa',
            'alamat' => 'Jl. Jend. Sudirman No.823, Bandung, Jawa Barat, Indonesia - 40213',
            'telp' => '+62 22 6030130, +62 8281 9090130',
            'fax' => '+62 22 6030849',
            'email' => 'gajahangkasa@mail.com',
            'created_at' => $now,
            'created_by' => 1,
            'updated_at' => $now,
            'updated_by' => 1,
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
