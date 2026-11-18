<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m261118_085000_add_rbac_trn_input_pfp
 */
class m261118_085000_add_rbac_trn_input_pfp extends Migration
{
    private $routes = [
        '/trn-input-pfp/*',
        '/trn-input-pfp/index',
        '/trn-input-pfp/view',
        '/trn-input-pfp/create',
        '/trn-input-pfp/update',
        '/trn-input-pfp/delete',
        '/trn-input-pfp/posting',
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

        $viewParents = ['Melihat Processing Pfp', 'Viewer', 'Melihat Beli Pfp'];
        $viewRoutes = ['/trn-input-pfp/index', '/trn-input-pfp/view'];

        foreach ($viewParents as $parent) {
            $parentExists = (new Query())->from('auth_item')->where(['name' => $parent])->exists($this->db);
            if ($parentExists) {
                foreach ($viewRoutes as $route) {
                    $linkExists = (new Query())->from('auth_item_child')->where(['parent' => $parent, 'child' => $route])->exists($this->db);
                    if (!$linkExists) {
                        $this->insert('auth_item_child', ['parent' => $parent, 'child' => $route]);
                    }
                }
            }
        }

        $manageParents = ['Membuat dan Update Processing Pfp', 'Membuat dan Update Beli Pfp'];
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
