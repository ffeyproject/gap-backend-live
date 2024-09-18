<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000010_create_bank_account_table extends Migration
{
    const TABLE_NAME = "mst_bank_account";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'bank_name' => $this->string(255)->notNull(),
            'acct_no' => $this->string(255)->notNull(),
            'acct_name' => $this->string(255)->notNull(),
            'swift_code' => $this->string(255),
            'address' => $this->text(),
            'correspondence' => $this->text(),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $now = time();

        $this->insert(self::TABLE_NAME, [
            'bank_name' => 'BANK PERMATA - CABANG CIMAHI',
            'acct_no' => '206-850-000',
            'acct_name' => 'PT. GAJAH ANGKASA PERKASA',
            'swift_code' => 'BBBAIDJA',
            'address' => 'JL. AMIR MAHMUD NO. 399B, BANDUNG 4011, INDONESIA',
            'correspondence' => 'STANDARD CHARTERED BANK NEW YORK USA',
            'created_at' => $now,
            'created_by' => 1,
            'updated_at' => $now,
            'updated_by' => 1,
        ]);

        $this->insert(self::TABLE_NAME, [
            'bank_name' => 'PT BANK DANAMON',
            'acct_no' => '0000-0-001593-3',
            'acct_name' => 'PT. GAJAH ANGKASA PERKASA',
            'swift_code' => 'BDINIDJA',
            'address' => 'PT. BANK DANAMON INDONESIA, TBK. JALAN SUDIRMAN NO. 30 - 32 BANDUNG 40181, INDONESIA',
            'correspondence' => 'STANDARD CHARTERED BANK NEW YORK USA',
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
