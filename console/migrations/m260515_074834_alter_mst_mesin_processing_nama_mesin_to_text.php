<?php

use yii\db\Migration;

/**
 * Class m260515_074834_alter_mst_mesin_processing_nama_mesin_to_text
 */
class m260515_074834_alter_mst_mesin_processing_nama_mesin_to_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('mst_mesin_processing', 'nama_mesin', $this->text()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('mst_mesin_processing', 'nama_mesin', $this->string(255)->notNull());
    }
}
