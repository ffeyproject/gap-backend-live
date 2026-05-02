<?php

use yii\db\Migration;

/**
 * Class m260502_044846_fix_is_posted_data
 */
class m260502_044846_fix_is_posted_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Correct the default value to false
        $this->alterColumn('inspecting_item', 'is_posted', $this->boolean()->notNull()->defaultValue(false));
        $this->alterColumn('inspecting_mkl_bj_items', 'is_posted', $this->boolean()->notNull()->defaultValue(false));

        // 2. Fix existing data for TrnInspecting
        // Items in DRAFT status should be is_posted = false
        $this->execute("
            UPDATE inspecting_item 
            SET is_posted = false 
            FROM trn_inspecting 
            WHERE inspecting_item.inspecting_id = trn_inspecting.id 
            AND trn_inspecting.status = 1
        ");

        // 3. Fix existing data for InspectingMklBj
        // Items in DRAFT status should be is_posted = false
        $this->execute("
            UPDATE inspecting_mkl_bj_items 
            SET is_posted = false 
            FROM inspecting_mkl_bj 
            WHERE inspecting_mkl_bj_items.inspecting_id = inspecting_mkl_bj.id 
            AND inspecting_mkl_bj.status = 1
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260502_044846_fix_is_posted_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260502_044846_fix_is_posted_data cannot be reverted.\n";

        return false;
    }
    */
}
