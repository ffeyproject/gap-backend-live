<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m261118_095500_add_rbac_pindah_jenis_printing
 */
class m261118_095500_add_rbac_pindah_jenis_printing extends Migration
{
    private $routes = [
        '/processing-pfp/pindah-jenis-printing',
        '/processing-pfp/riwayat-pindah-printing',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time = time();

        foreach ($this->routes as $route) {
            $exists = (new Query())->from('auth_item')->where(['name' => $route])->exists($this->db);
            if (!$exists) {
                $this->insert('auth_item', [
                    'name' => $route,
                    'type' => 2,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }

        $viewParents = ['Melihat Processing Pfp', 'Viewer'];
        $viewRoute = '/processing-pfp/riwayat-pindah-printing';
        foreach ($viewParents as $parent) {
            $parentExists = (new Query())->from('auth_item')->where(['name' => $parent])->exists($this->db);
            if ($parentExists) {
                $linkExists = (new Query())->from('auth_item_child')->where(['parent' => $parent, 'child' => $viewRoute])->exists($this->db);
                if (!$linkExists) {
                    $this->insert('auth_item_child', ['parent' => $parent, 'child' => $viewRoute]);
                }
            }
        }

        $manageParents = ['Membuat dan Update Processing Pfp'];
        foreach ($manageParents as $parent) {
            $parentExists = (new Query())->from('auth_item')->where(['name' => $parent])->exists($this->db);
            if ($parentExists) {
                foreach ($this->routes as $route) {
                    $linkExists = (new Query())->from('auth_item_child')->where(['parent' => $parent, 'child' => $route])->exists($this->db);
                    if (!$linkExists) {
                        $this->insert('auth_item_child', ['parent' => $parent, 'child' => $route]);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->routes as $route) {
            $this->delete('auth_item_child', ['child' => $route]);
            $this->delete('auth_item', ['name' => $route]);
        }
    }
}
