<?php

use yii\db\Migration;

/**
 * Class m260520_115604_create_trn_hambatan_mesin_tables
 */
class m260520_115604_create_trn_hambatan_mesin_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%trn_hambatan_mesin}}', [
            'id' => $this->primaryKey(),
            'mst_mesin_proses_id' => $this->integer()->notNull(),
            'tanggal' => $this->date()->notNull(),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-trn_hambatan_mesin-mesin',
            '{{%trn_hambatan_mesin}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%trn_hambatan_mesin_item}}', [
            'id' => $this->primaryKey(),
            'trn_hambatan_mesin_id' => $this->integer()->notNull(),
            'start_time' => $this->string(50)->notNull(),
            'stop_time' => $this->string(50)->notNull(),
            'mst_jenis_hambatan_id' => $this->integer()->notNull(),
            'no_kartu' => $this->string(100),
            'keterangan' => $this->text(),
        ]);

        $this->addForeignKey(
            'fk-trn_hambatan_mesin_item-header',
            '{{%trn_hambatan_mesin_item}}',
            'trn_hambatan_mesin_id',
            '{{%trn_hambatan_mesin}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-trn_hambatan_mesin_item-hambatan',
            '{{%trn_hambatan_mesin_item}}',
            'mst_jenis_hambatan_id',
            '{{%mst_jenis_hambatan}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-trn_hambatan_mesin_item-hambatan', '{{%trn_hambatan_mesin_item}}');
        $this->dropForeignKey('fk-trn_hambatan_mesin_item-header', '{{%trn_hambatan_mesin_item}}');
        $this->dropTable('{{%trn_hambatan_mesin_item}}');

        $this->dropForeignKey('fk-trn_hambatan_mesin-mesin', '{{%trn_hambatan_mesin}}');
        $this->dropTable('{{%trn_hambatan_mesin}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_115604_create_trn_hambatan_mesin_tables cannot be reverted.\n";

        return false;
    }
    */
}
