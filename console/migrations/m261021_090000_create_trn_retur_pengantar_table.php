<?php

use yii\db\Migration;

/**
 * Surat pengantar stok hasil retur buyer (trn_gudang_jadi source Retur) ke bagian/divisi lain.
 * Tujuan Processing: stok dipindah ke Gudang Mutasi (trn_stock_greige) agar bisa dipakai di Kartu Proses Mutasi.
 */
class m261021_090000_create_trn_retur_pengantar_table extends Migration
{
    /**
     * Route baru didaftarkan ke permission retur yang sudah ada, supaya user yang sudah punya akses retur langsung bisa memakai.
     * [route => permission]
     */
    private $routes = [
        '/trn-retur-pengantar/index' => 'Melihat Penerimaan Retur',
        '/trn-retur-pengantar/view' => 'Melihat Penerimaan Retur',
        '/trn-retur-pengantar/create' => 'Membuat dan Update Penerimaan Retur',
        '/trn-retur-pengantar/update' => 'Membuat dan Update Penerimaan Retur',
        '/trn-retur-pengantar/posting' => 'Membuat dan Update Penerimaan Retur',
        '/trn-retur-pengantar/delete' => 'Menghapus Penerimaan Retur',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('trn_retur_pengantar', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'no_urut' => $this->integer()->null(),
            'no' => $this->string()->null(),
            'bagian' => $this->smallInteger()->notNull()->comment('1=Processing, 2=QC, 3=Marketing, 4=Lain-lain'),
            'bagian_lain' => $this->string()->null()->comment('Nama bagian jika bagian = Lain-lain'),
            'penerima' => $this->string()->null(),
            'note' => $this->text()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1=Draft, 2=Posted'),
            'posted_at' => $this->integer()->null(),
            'posted_by' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createTable('trn_retur_pengantar_item', [
            'id' => $this->primaryKey(),
            'pengantar_id' => $this->integer()->notNull(),
            'gudang_jadi_id' => $this->integer()->notNull()->comment('Stok hasil retur (trn_gudang_jadi.id)'),
            'qty' => $this->decimal(12, 2)->notNull(),
            'stock_greige_id' => $this->integer()->null()->comment('Stok Gudang Mutasi yang dibuat saat posting ke Processing (trn_stock_greige.id)'),
        ]);
        $this->addForeignKey('fk_trn_retur_pengantar_item_pengantar', 'trn_retur_pengantar_item', 'pengantar_id', 'trn_retur_pengantar', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_trn_retur_pengantar_item_gudang_jadi', 'trn_retur_pengantar_item', 'gudang_jadi_id', 'trn_gudang_jadi', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_trn_retur_pengantar_item_stock_greige', 'trn_retur_pengantar_item', 'stock_greige_id', 'trn_stock_greige', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('idx_trn_retur_pengantar_item_pengantar_id', 'trn_retur_pengantar_item', 'pengantar_id');
        $this->createIndex('idx_trn_retur_pengantar_item_gudang_jadi_id', 'trn_retur_pengantar_item', 'gudang_jadi_id');

        $time = time();
        foreach ($this->routes as $route => $permission) {
            $exists = (new \yii\db\Query())->from('auth_item')->where(['name' => $route])->exists($this->db);
            if (!$exists) {
                $this->insert('auth_item', ['name' => $route, 'type' => 2, 'created_at' => $time, 'updated_at' => $time]);
            }
            $permissionExists = (new \yii\db\Query())->from('auth_item')->where(['name' => $permission])->exists($this->db);
            if ($permissionExists) {
                $this->insert('auth_item_child', ['parent' => $permission, 'child' => $route]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->routes as $route => $permission) {
            $this->delete('auth_item_child', ['parent' => $permission, 'child' => $route]);
            $this->delete('auth_item', ['name' => $route]);
        }

        $this->dropTable('trn_retur_pengantar_item');
        $this->dropTable('trn_retur_pengantar');
    }
}
