<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kartu_process_pfp_process}}`.
 */
class m190901_000103_0_add_color_column_to_trn_order_celup_table extends Migration
{
    const TABLE_NAME = "trn_order_celup";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(self::TABLE_NAME, 'color', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn(self::TABLE_NAME, 'color');
    }
}
