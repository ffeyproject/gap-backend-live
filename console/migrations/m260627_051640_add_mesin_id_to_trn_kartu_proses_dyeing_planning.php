<?php

use yii\db\Migration;

/**
 * Class m260627_051640_add_mesin_id_to_trn_kartu_proses_dyeing_planning
 */
class m260627_051640_add_mesin_id_to_trn_kartu_proses_dyeing_planning extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%trn_kartu_proses_dyeing_planning}}', 'mesin_id', $this->integer()->defaultValue(null));
        $this->addForeignKey(
            'fk_trn_kartu_proses_dyeing_planning_mesin',
            '{{%trn_kartu_proses_dyeing_planning}}',
            'mesin_id',
            '{{%mst_mesin_proses}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_trn_kartu_proses_dyeing_planning_mesin', '{{%trn_kartu_proses_dyeing_planning}}');
        $this->dropColumn('{{%trn_kartu_proses_dyeing_planning}}', 'mesin_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260627_051640_add_mesin_id_to_trn_kartu_proses_dyeing_planning cannot be reverted.\n";

        return false;
    }
    */
}
