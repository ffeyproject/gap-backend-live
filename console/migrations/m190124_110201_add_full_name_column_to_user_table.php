<?php

use \yii\db\Migration;

class m190124_110201_add_full_name_column_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'full_name', $this->string()->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'full_name');
    }
}
