<?php

use yii\db\Migration;

/**
 * Handles the creation of table `trn_stock_greige_daily`.
 */
class m260924_091500_create_trn_stock_greige_daily_table extends Migration
{
    const TABLE_NAME = "trn_stock_greige_daily";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'date' => $this->date()->notNull(),
            'greige_id' => $this->integer()->unsigned()->notNull(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'total_panjang' => $this->double()->notNull()->defaultValue(0)->comment('Total kuantiti stock (meter/yard) pada hari tersebut'),
            'total_roll' => $this->integer()->notNull()->defaultValue(0)->comment('Total jumlah roll/pcs pada hari tersebut'),
            'grade_a' => $this->double()->defaultValue(0),
            'grade_b' => $this->double()->defaultValue(0),
            'grade_c' => $this->double()->defaultValue(0),
            'grade_d' => $this->double()->defaultValue(0),
            'grade_e' => $this->double()->defaultValue(0),
            'grade_ng' => $this->double()->defaultValue(0),
            'grade_lain' => $this->double()->defaultValue(0),
            'note' => $this->text(),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->createIndex(
            'uq_trn_stock_greige_daily_date_greige',
            self::TABLE_NAME,
            ['date', 'greige_id'],
            true
        );

        $this->createIndex(
            'idx_trn_stock_greige_daily_date',
            self::TABLE_NAME,
            'date'
        );

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME, 'greige_id', 'mst_greige', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_created_by', self::TABLE_NAME, 'created_by', 'user', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_updated_by', self::TABLE_NAME, 'updated_by', 'user', 'id');

        // Tambahkan RBAC permissions jika auth_item ada
        $auth = Yii::$app->authManager;
        if ($auth !== null) {
            $routes = [
                '/trn-stock-greige-daily/*',
                '/trn-stock-greige-daily/index',
                '/trn-stock-greige-daily/matrix',
                '/trn-stock-greige-daily/save-snapshot',
                '/trn-stock-greige-daily/export-excel',
                '/trn-stock-greige-daily/delete',
                '/trn-stock-greige/save-daily-stock',
            ];
            foreach ($routes as $route) {
                if (!$auth->getPermission($route)) {
                    $perm = $auth->createPermission($route);
                    $auth->add($perm);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_updated_by', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_created_by', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
