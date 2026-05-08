<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_mesin_processing_forbidden_greige}}`.
 */
class m260507_075100_create_mst_mesin_processing_forbidden_greige_table extends Migration
{
    const TABLE_NAME = 'mst_mesin_processing_forbidden_greige';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'mesin_id' => $this->integer()->notNull(),
            'greige_id' => $this->integer()->notNull(),
        ]);

        // create index for column `mesin_id`
        $this->createIndex(
            '{{%idx-mst_mesin_processing_forbidden_greige-mesin_id}}',
            self::TABLE_NAME,
            'mesin_id'
        );

        // add foreign key for table `{{%mst_mesin_processing}}`
        $this->addForeignKey(
            '{{%fk-mst_mesin_processing_forbidden_greige-mesin_id}}',
            self::TABLE_NAME,
            'mesin_id',
            '{{%mst_mesin_processing}}',
            'id',
            'CASCADE'
        );

        // create index for column `greige_id`
        $this->createIndex(
            '{{%idx-mst_mesin_processing_forbidden_greige-greige_id}}',
            self::TABLE_NAME,
            'greige_id'
        );

        // add foreign key for table `{{%mst_greige}}`
        $this->addForeignKey(
            '{{%fk-mst_mesin_processing_forbidden_greige-greige_id}}',
            self::TABLE_NAME,
            'greige_id',
            '{{%mst_greige}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%mst_mesin_processing}}`
        $this->dropForeignKey(
            '{{%fk-mst_mesin_processing_forbidden_greige-mesin_id}}',
            self::TABLE_NAME
        );

        // drops index for column `mesin_id`
        $this->dropIndex(
            '{{%idx-mst_mesin_processing_forbidden_greige-mesin_id}}',
            self::TABLE_NAME
        );

        // drops foreign key for table `{{%mst_greige}}`
        $this->dropForeignKey(
            '{{%fk-mst_mesin_processing_forbidden_greige-greige_id}}',
            self::TABLE_NAME
        );

        // drops index for column `greige_id`
        $this->dropIndex(
            '{{%idx-mst_mesin_processing_forbidden_greige-greige_id}}',
            self::TABLE_NAME
        );

        $this->dropTable(self::TABLE_NAME);
    }
}
