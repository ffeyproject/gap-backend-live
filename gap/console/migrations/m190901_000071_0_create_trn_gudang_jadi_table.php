<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000071_0_create_trn_gudang_jadi_table extends Migration
{
    const TABLE_NAME = "trn_gudang_jadi";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->dropColumn('trn_inspecting', 'jenis_gudang');

        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'jenis_gudang' => $this->tinyInteger(1)->comment('1=Lokal, 2=Export, 3=Grade B'),
            'wo_id' => $this->integer()->unsigned()->notNull(),
            'source' => $this->smallInteger()->notNull()->comment('Asal kain, 1=Packing/Inspecting 2=Makloon 3=Retur Buyer'),
            'source_ref' => $this->string()->comment('Nomor referensi source, misalnya nomor inspecting, no surat terima dari makloon, dll.'),
            'unit' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Mengacu pada MstGreigeGroup::unitOptions()'),
            'qty' => $this->integer()->unsigned()->notNull()->comment('Unit menyesuaikan pada kuantiti penerimaan, tidak lagi harus sama dengan greige group'),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
            'date' => $this->date()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=stock, 2=out, 3=siap kirim'),
            'note' => $this->text(),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
