<?php

use yii\db\Migration;

/**
 * Class m260618_064716_alter_trn_hambatan_mesin_tables
 */
class m260618_064716_alter_trn_hambatan_mesin_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Alter trn_hambatan_mesin
        $this->addColumn('{{%trn_hambatan_mesin}}', 'shift', $this->string(50)->null());
        
        // Move shift data from item to header (if exists, just take the first item's shift per header)
        $this->execute("
            UPDATE trn_hambatan_mesin thm
            SET shift = (
                SELECT shift FROM trn_hambatan_mesin_item thmi 
                WHERE thmi.trn_hambatan_mesin_id = thm.id 
                LIMIT 1
            )
        ");
        
        // Now it's safe to drop mst_mesin_proses_id from header (but first copy to item!)
        $this->addColumn('{{%trn_hambatan_mesin_item}}', 'mst_mesin_proses_id', $this->integer()->null());
        
        $this->execute("
            UPDATE trn_hambatan_mesin_item thmi
            SET mst_mesin_proses_id = (
                SELECT mst_mesin_proses_id FROM trn_hambatan_mesin thm 
                WHERE thm.id = thmi.trn_hambatan_mesin_id
            )
        ");
        
        // Remove foreign key before dropping
        $this->dropForeignKey('fk-trn_hambatan_mesin-mesin', '{{%trn_hambatan_mesin}}');
        
        // Drop columns
        $this->dropColumn('{{%trn_hambatan_mesin}}', 'mst_mesin_proses_id');
        $this->dropColumn('{{%trn_hambatan_mesin_item}}', 'shift');
        
        // Add foreign key to new column
        $this->addForeignKey(
            'fk-trn_hambatan_mesin_item-mesin',
            '{{%trn_hambatan_mesin_item}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%trn_hambatan_mesin_item}}', 'shift', $this->string(50)->null());
        $this->addColumn('{{%trn_hambatan_mesin}}', 'mst_mesin_proses_id', $this->integer()->null());
        
        // Revert data
        $this->execute("
            UPDATE trn_hambatan_mesin_item thmi
            SET shift = (
                SELECT shift FROM trn_hambatan_mesin thm 
                WHERE thm.id = thmi.trn_hambatan_mesin_id
            )
        ");
        
        $this->execute("
            UPDATE trn_hambatan_mesin thm
            SET mst_mesin_proses_id = (
                SELECT mst_mesin_proses_id FROM trn_hambatan_mesin_item thmi 
                WHERE thmi.trn_hambatan_mesin_id = thm.id 
                LIMIT 1
            )
        ");
        
        $this->dropForeignKey('fk-trn_hambatan_mesin_item-mesin', '{{%trn_hambatan_mesin_item}}');
        
        $this->dropColumn('{{%trn_hambatan_mesin_item}}', 'mst_mesin_proses_id');
        $this->dropColumn('{{%trn_hambatan_mesin}}', 'shift');
        
        $this->addForeignKey(
            'fk-trn_hambatan_mesin-mesin',
            '{{%trn_hambatan_mesin}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
