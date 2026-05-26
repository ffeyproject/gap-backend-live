<?php

use yii\db\Migration;

/**
 * Class m260520_115933_alter_trn_hambatan_mesin_item_table
 */
class m260520_115933_alter_trn_hambatan_mesin_item_table extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk-trn_hambatan_mesin_item-hambatan', '{{%trn_hambatan_mesin_item}}');
        $this->dropColumn('{{%trn_hambatan_mesin_item}}', 'mst_jenis_hambatan_id');

        $this->createTable('{{%trn_hambatan_mesin_item_hambatan}}', [
            'trn_hambatan_mesin_item_id' => $this->integer()->notNull(),
            'mst_jenis_hambatan_id' => $this->integer()->notNull(),
            'PRIMARY KEY (trn_hambatan_mesin_item_id, mst_jenis_hambatan_id)'
        ]);

        $this->addForeignKey(
            'fk-trn_hambatan_mesin_item_hambatan-item',
            '{{%trn_hambatan_mesin_item_hambatan}}',
            'trn_hambatan_mesin_item_id',
            '{{%trn_hambatan_mesin_item}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-trn_hambatan_mesin_item_hambatan-hambatan',
            '{{%trn_hambatan_mesin_item_hambatan}}',
            'mst_jenis_hambatan_id',
            '{{%mst_jenis_hambatan}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-trn_hambatan_mesin_item_hambatan-hambatan', '{{%trn_hambatan_mesin_item_hambatan}}');
        $this->dropForeignKey('fk-trn_hambatan_mesin_item_hambatan-item', '{{%trn_hambatan_mesin_item_hambatan}}');
        $this->dropTable('{{%trn_hambatan_mesin_item_hambatan}}');

        $this->addColumn('{{%trn_hambatan_mesin_item}}', 'mst_jenis_hambatan_id', $this->integer()->notNull());
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_115933_alter_trn_hambatan_mesin_item_table cannot be reverted.\n";

        return false;
    }
    */
}
