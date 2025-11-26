<?php

use backend\modules\user\models\User;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $user User */

$controller = Yii::$app->controller;
$controllerId = $controller->id;
$actionId = $controller->action->id;
$moduleId = $controller->module->id;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $user->getAvatarUrl() ?>" class="img-circle" alt="User Image"
                    style="width: 80px; height: 50px !important;" />
            </div>
            <div class="pull-left info">
                <p><?=$user->full_name?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <!--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>-->
        <!-- /.search form -->

        <?php
        $menuItems = [
            [
                'label' => 'DIREKTUR',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Sales Contract', 'icon' => 'circle', 'url' => ['/direktur/trn-sc/index'],
                        'active' => $moduleId=='direktur' && $controllerId == 'trn-sc'
                    ],
                ]
            ],
            [
                'label' => 'REFERENSI DATA',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Banks', 'icon' => 'circle', 'url' => ['/mst-bank-account/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-bank-account'
                    ],
                ],
            ],

            [
                'label' => 'MASTER DATA',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Greige Group', 'icon' => 'circle', 'url' => ['/mst-greige-group/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-greige-group'
                    ],
                    [
                        'label' => 'Greige', 'icon' => 'circle', 'url' => ['/mst-greige/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-greige'
                    ],
                    [
                        'label' => 'Customer', 'icon' => 'circle', 'url' => ['/mst-customer/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-customer'
                    ],
                    [
                        'label' => 'Vendor', 'icon' => 'circle', 'url' => ['/mst-vendor/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-vendor'
                    ],
                    [
                        'label' => 'Handling', 'icon' => 'circle', 'url' => ['/mst-handling/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-handling'
                    ],
                    [
                        'label' => 'Defect',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label'=>'Kode Defect',
                                'icon' => 'circle-o',
                                'url' => ['/mst-kode-defect/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-kode-defect'
                            ],
                            [
                                'label'=>'Grafik Defect',
                                'icon' => 'circle-o',
                                'url' => ['/mst-kode-defect/grafik'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-kode-defect'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Processing',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label'=>'PFP',
                                'icon' => 'circle-o',
                                'url' => ['/mst-process-pfp/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-process-pfp'
                            ],
                            [
                                'label'=>'Dyeing',
                                'icon' => 'circle-o',
                                'url' => ['/mst-process-dyeing/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-process-dyeing'
                            ],
                            [
                                'label'=>'Printing',
                                'icon' => 'circle-o',
                                'url' => ['/mst-process-printing/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-process-printing'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Paper Tube', 'icon' => 'circle', 'url' => ['/mst-papper-tube/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-papper-tube'
                    ],
                    // bagussona
                    [
                        'label' => 'Stock Opname', 'icon' => 'circle', 'url' => ['/mst-stock-opname/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mst-stock-name'
                    ],
                    [
                        'label' => 'Warehouse',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label'=>'Location',
                                'icon' => 'circle-o',
                                'url' => ['/mst-location/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-location'
                            ],
                            [
                                'label'=>'Sub Location',
                                'icon' => 'circle-o',
                                'url' => ['/mst-sub-location/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'mst-sub-location'
                            ],
                        ]
                    ],
                ],
            ],

            [
                'label' => 'SALES CONTRACT',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label'=>'Sales Contract',
                        'icon' => 'circle-o',
                        'url' => ['/trn-sc/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-sc'
                    ],
                    [
                        'label'=>'SC Greige',
                        'icon' => 'circle-o',
                        'url' => ['/trn-sc-greige/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-sc-greige'
                    ],
                    [
                        'label'=>'SC Komisi',
                        'icon' => 'circle-o',
                        'url' => ['/trn-sc-komisi/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-sc-komisi'
                    ]
                ]
            ],

            [
                'label' => 'MARKETING ORDER',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label'=>'Marketing Order',
                        'icon' => 'circle-o',
                        'url' => ['/trn-mo/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-mo'
                    ],
                    [
                        'label'=>'Marketing Order Sisa',
                        'icon' => 'circle-o',
                        'url' => ['/trn-mo/index-sisa'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-mo'
                    ],
                ]
            ],

            [
                'label' => 'WORKING ORDER',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label'=>'Working Order',
                        'icon' => 'circle-o',
                        'url' => ['/trn-wo/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-wo'
                    ],
                    [
                        'label' => 'Wo Memo', 
                        'icon' => 'circle', 
                        'url' => ['/trn-wo-memo/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-wo-memo'
                    ],
                    [
                        'label' => 'REKAP',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label'=>'Total Working Order',
                                'icon' => 'circle-o',
                                'url' => ['/trn-wo/rekap-total-wo'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-wo'  && $actionId=='rekap-total-wo'
                            ],
                            [
                                'label'=>'Tanggal Siap Warna',
                                'icon' => 'circle-o',
                                'url' => ['/trn-wo/rekap-ready-colour'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-wo'  && $actionId=='rekap-ready-colour'
                            ],
                        ]
                    ],
                ]
            ],

            [
                'label' => 'APPROVAL',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Sales Contract', 'icon' => 'circle-o', 'url' => ['/approval-sc/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'approval-sc'
                    ],
                    [
                        'label' => 'Marketing Order', 'icon' => 'circle-o', 'url' => ['/approval-mo/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'approval-mo'
                    ],
                    [
                        'label' => 'Work Order', 'icon' => 'circle-o', 'url' => ['/approval-wo/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'approval-wo'
                    ],
                    [
                        'label' => 'Order PFP', 'icon' => 'circle-o', 'url' => ['/approval-order-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'approval-order-pfp'
                    ],
                ]
            ],

            [
                'label' => 'ORDER PFP', 'icon' => 'circle', 'url' => ['/trn-order-pfp/index'],
                'active' => $moduleId=='app-backend' && $controllerId == 'trn-order-pfp' && $actionId=='index'
            ],

            [
                'label' => 'ORDER CELUP', 'icon' => 'circle', 'url' => ['/trn-order-celup/index'],
                'active' => $moduleId=='app-backend' && $controllerId == 'trn-order-celup' && $actionId=='index'
            ],

            [
                'label' => 'GUDANG GREIGE',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Stock Fresh Greige', 'icon' => 'circle', 'url' => ['/trn-stock-greige/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-greige' && $actionId=='index'
                    ],
                    [
                        'label' => 'Proses', 'icon' => 'circle', 'url' => ['/trn-stock-greige/process'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-greige' && $actionId=='process'
                    ],
                    [
                        'label' => 'Beli Greige', 'icon' => 'circle', 'url' => ['/trn-buy-greige/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-buy-greige' && $actionId=='index'
                    ],
                    [
                        'label' => 'Potong Greige',
                        'icon' => 'circle',
                        'url' => ['/trn-potong-greige/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-potong-greige' && $actionId=='index'
                    ],
                    [
                        'label' => 'Greige Keluar',
                        'icon' => 'circle',
                        'url' => ['/trn-greige-keluar/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-greige-keluar'
                    ],
                    [
                        'label' => 'Stock Gudang Inspect', 'icon' => 'circle', 'url' => ['/trn-stock-greige/index-gudang-inspect'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-greige' && $actionId=='index-gudang-inspect'
                    ],
                    [
                        'label' => 'Riwayat Mix Quality', 'icon' => 'circle', 'url' => ['/trn-mixed-greige-item/riwayat'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-mixed-greige-item' && $actionId=='riwayat'
                    ],
                    [
                        'label' => 'REKAP',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Laporan Stock Greige', 'icon' => 'circle', 'url' => ['/trn-stock-greige/laporan-stock'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-greige' && $actionId=='laporan-stock'
                            ],
                            [
                                'label' => 'Kedatangan Greige', 'icon' => 'circle', 'url' => ['/trn-buy-greige/rekap'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-buy-greige' && $actionId=='rekap'
                            ],
                            [
                                'label' => 'Order Wo Actual Dyeing', 'icon' => 'circle', 'url' => ['/trn-wo/rekap-order-actual-dyeing'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-wo' && $actionId=='rekap-order-actual-dyeing'
                            ],
                            [
                                'label' => 'Order Wo Actual PFP', 'icon' => 'circle', 'url' => ['/trn-order-pfp/rekap-actual'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-order-pfp' && $actionId=='rekap-actual'
                            ],
                            [
                                'label' => 'Laporan Greige Keluar', 'icon' => 'circle', 'url' => ['/trn-greige-keluar/rekap'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-greige-keluar' && $actionId=='rekap'
                            ],
                        ]
                    ],
                ],
            ],
            [
                'label' => 'GUDANG STOCK OPNAME',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Record Stock Opname', 'icon' => 'circle', 'url' => ['/trn-gudang-stock-opname/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-gudang-stock-opname' && $actionId=='index'
                    ],
                    [
                        'label' => 'Stock Opname Keseluruhan', 'icon' => 'circle', 'url' => ['/trn-gudang-stock-opname/stock-keseluruhan'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-gudang-stock-opname' && $actionId=='get-stock-keseluruhan'
                    ],
                    [
                        'label' => 'Stock Opname Keluar', 'icon' => 'circle', 'url' => ['/trn-gudang-stock-opname/stock-keluar'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-gudang-stock-opname' && $actionId=='get-stock-keluar'
                    ],
                   [
                        'label' => 'Duplikat Stok ke Opname', 
                        'icon' => 'circle', 
                        'url' => ['/trn-gudang-stock-opname/index-duplicate'],
                        'active' => $moduleId == 'app-backend'
                            && $controllerId == 'trn-gudang-stock-opname'
                            && $actionId == 'index-duplicate'
                    ],
                    [
                        'label' => 'Laporan Stock Opname Harian', 
                        'icon' => 'circle', 
                        'url' => ['/trn-gudang-stock-opname/laporan-greige-opname'],
                        'active' => $moduleId == 'app-backend'
                            && $controllerId == 'trn-gudang-stock-opname'
                            && $actionId == 'laporan-greige-opname'
                    ],
                    [
                        'label' => 'Laporan per Motif',
                        'icon' => 'circle',
                        'url' => ['/trn-gudang-stock-opname/laporan-greige-opname-motif'],
                        'active' => $controllerId == 'trn-gudang-stock-opname' && $actionId == 'laporan-greige-opname-motif'
                    ],
                ],
            ],
            [
                'label' => 'GUDANG INSPECT',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Stock Gudang Inspect', 'icon' => 'circle', 'url' => ['/trn-gudang-inspect/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-gudang-inspect' && $actionId=='index'
                    ],
                ],
            ],

            [
                'label' => 'GUDANG PFP',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Masuk PFP', 'icon' => 'circle-o', 'url' => ['/trn-buy-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-buy-pfp'
                    ],
                    [
                        'label' => 'Stock PFP', 'icon' => 'circle-o', 'url' => ['/trn-stock-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-pfp' && $actionId=='index'
                    ],
                    [
                        'label' => 'Potong Greige',
                        'icon' => 'circle',
                        'url' => ['/trn-potong-greige-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-potong-greige-pfp' && $actionId=='index'
                    ],
                    [
                        'label' => 'PFP Keluar',
                        'icon' => 'circle',
                        'url' => ['/trn-pfp-keluar/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-pfp-keluar'
                    ],
                ]
            ],

            [
                'label' => 'GUDANG WIP',
                'icon' => 'circle',
                'url' => ['/trn-stock-wip/index'],
                'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-wip'
            ],

            [
                'label' => 'MEMO',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label'=>'Memo Repair', 'icon' => 'circle-o', 'url' => ['/trn-memo-repair/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-memo-repair'
                    ],
                    [
                        'label' => 'Memo Redyeing', 'icon' => 'circle-o', 'url' => ['/trn-memo-redyeing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-memo-redyeing' && $actionId='index'
                    ],
                    [
                        'label' => 'Memo Perubahan Data', 'icon' => 'circle-o', 'url' => ['/trn-memo-perubahan-data/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-memo-perubahan-data' && $actionId='index'
                    ],
                ]
            ],

            [
                'label' => 'KARTU PROSES',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'PFP', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-pfp'
                    ],
                    [
                        'label' => 'Celup', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-celup/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-celup'
                    ],
                    [
                        'label' => 'Dyeing', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-dyeing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-dyeing'
                    ],
                    [
                        'label' => 'Printing', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-printing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-printing'
                    ],
                    [
                        'label' => 'Maklon', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-maklon/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-maklon'
                    ],
                    [
                        'label' => 'Rekap Dyeing Siap Kirim', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-dyeing/siap-kirim'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-dyeing'
                    ],
                    [
                        'label' => 'Rekap Dyeing Masuk Packing', 'icon' => 'circle-o', 'url' => ['/trn-kartu-proses-dyeing/get-data-masuk-packing'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kartu-proses-dyeing'
                    ],
                ]
            ],

            [
                'label' => 'PENERIMAAN',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Kartu Proses PFP', 'icon' => 'circle-o', 'url' => ['/penerimaan-kartu-proses-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-kartu-proses-pfp'
                    ],
                    [
                        'label' => 'Kartu Proses Celup', 'icon' => 'circle-o', 'url' => ['/penerimaan-kartu-proses-celup/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-kartu-proses-celup'
                    ],
                    [
                        'label' => 'Kartu Proses Dyeing', 'icon' => 'circle-o', 'url' => ['/penerimaan-kartu-proses-dyeing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-kartu-proses-dyeing'
                    ],
                    [
                        'label' => 'Kartu Proses Printing', 'icon' => 'file-code-o', 'url' => ['/penerimaan-kartu-proses-printing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-kartu-proses-printing'
                    ],
                    [
                        'label' => 'LAPORAN',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Persiapan Dyeing', 'icon' => 'circle-o', 'url' => ['/laporan/persiapan-dyeing'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'laporan'
                            ],
                        ]
                    ],
                ],
            ],

            [
                'label' => 'PROCESSING',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'PFP', 'icon' => 'circle-o', 'url' => ['/processing-pfp/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'processing-pfp'
                    ],
                    [
                        'label' => 'Celup', 'icon' => 'circle-o', 'url' => ['/processing-celup/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'processing-celup'
                    ],
                    [
                        'label' => 'Dyeing', 'icon' => 'circle-o', 'url' => ['/processing-dyeing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'processing-dyeing' && $actionId=='index'
                    ],
                    [
                        'label' => 'Printing', 'icon' => 'circle-o', 'url' => ['/processing-printing/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'processing-printing' && $actionId=='index'
                    ],
                    [
                        'label' => 'REKAP',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label'=>'Laporan Proses Dyeing',
                                'icon' => 'circle-o',
                                'url' => ['/processing-dyeing/rekap'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'processing-dyeing' && $actionId=='rekap'
                            ],
                            [
                                'label'=>'Rekap Dyeing By Process',
                                'icon' => 'circle-o',
                                'url' => ['/processing-dyeing/rekap-by-process'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'processing-dyeing' && $actionId=='rekap-by-process'
                            ],
                            [
                                'label'=>'Laporan Proses PFP',
                                'icon' => 'circle-o',
                                'url' => ['processing-pfp/rekap'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'processing-pfp' && $actionId=='rekap'
                            ],
                            [
                                'label'=>'Laporan Proses Printing',
                                'icon' => 'circle-o',
                                'url' => ['processing-printing/rekap'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'processing-printing' && $actionId=='rekap'
                            ],
                        ]
                    ],
                ],
            ],

            [
                'label' => 'INSPECTING',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Kartu Proses Dyeing',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Data Kartu Proses Dyeing',
                                'icon'  => 'circle-o',
                                'url'   => ['/trn-inspecting/data-kartu-proses-dyeing'],
                                'active' => $moduleId=='app-backend' 
                                    && $controllerId == 'trn-inspecting' 
                                    && $actionId=='data-kartu-proses-dyeing'
                            ],
                            [
                                'label' => 'Inspecting', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/kartu-proses-dyeing'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && $actionId=='kartu-proses-dyeing'
                            ],
                            [
                                'label' => 'Penolakan', 'icon' => 'circle-o', 'url' => ['/inspecting-dyeing-reject/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'inspecting-dyeing-reject'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Kartu Proses Printing',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Inspecting', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/kartu-proses-printing'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && $actionId=='kartu-proses-printing'
                            ],
                            [
                                'label' => 'Penolakan', 'icon' => 'circle-o', 'url' => ['/inspecting-printing-reject/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'inspecting-printing-reject'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Memo Repair', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/memo-repair'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && $actionId=='memo-repair'
                    ],
                    [
                        'label' => 'Data', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && in_array($actionId, ['index', 'view', 'create-dyeing', 'update-dyeing', 'create-printing', 'update-printing', 'create-alt'])
                    ],
                    [
                        'label' => 'Makloon & Barang Jadi', 'icon' => 'circle-o', 'url' => ['/inspecting-mkl-bj/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'inspecting-mkl-bj' && in_array($actionId, ['index', 'view', 'update', 'create'])
                    ],
                    [
                        'label' => 'Analisa Pengiriman Produksi', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/analisa-pengiriman-produksi'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && $actionId=='analisa-pengiriman-produksi'
                    ],
                    [
                        'label' => 'Daftar Pengiriman Produksi', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/daftar-pengiriman-produksi'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && $actionId=='daftar-pengiriman-produksi'
                    ],
                    [
                        'label' => 'Rekap Pengiriman Harian', 'icon' => 'circle-o', 'url' => ['/trn-inspecting/rekap-pengiriman-harian'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-inspecting' && $actionId=='rekap-pengiriman-harian'
                    ],
                    [
                        'label' => 'Fixing Data', 'icon' => 'circle-o', 'url' => ['/trn-fixing-data/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-fixing-data' && $actionId=='index'
                    ],
                ],
            ],

            [
                'label' => 'VERPACKING',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'PFP Keluar', 'icon' => 'circle', 'url' => ['/pfp-keluar-verpacking/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'pfp-keluar-verpacking' && $actionId == 'index'
                    ],

                    [
                        'label' => 'Riwayat Penerimaan Makloon',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Proses', 'icon' => 'circle-o', 'url' => ['/riwayat-penerimaan-makloon-proses/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'riwayat-penerimaan-makloon-proses' && $actionId=='index'
                            ],
                            [
                                'label' => 'Finish', 'icon' => 'circle-o', 'url' => ['/riwayat-penerimaan-makloon-finish/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'riwayat-penerimaan-makloon-finish'
                            ],
                        ]
                    ],
                ]
            ],

            [
                'label' => 'GUDANG JADI',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Stock', 'icon' => 'circle', 'url' => ['/trn-gudang-jadi/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-gudang-jadi' && $actionId == 'index'
                    ],
                    [
                        'label' => 'Potong Stock', 'icon' => 'circle', 'url' => ['/trn-potong-stock/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-potong-stock' && $actionId == 'index'
                    ],
                    [
                        'label' => 'Laporan Stock', 'icon' => 'circle', 'url' => ['/trn-laporan-stock/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-laporan-stock' && $actionId == 'index'
                    ],
                    [
                        'label' => 'Print Stock', 'icon' => 'circle', 'url' => ['/trn-print-stock/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-print-stock' && $actionId == 'index'
                    ],
                    [
                        'label' => 'Integrasi Accurate',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Data Masuk', 'icon' => 'circle-o', 'url' => ['/trn-integrasi-accurate-data-masuk/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-integrasi-accurate-data-masuk'
                            ],
                            [
                                'label' => 'Data Keluar', 'icon' => 'circle-o', 'url' => ['/trn-integrasi-accurate-data-keluar/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-integrasi-accurate-data-keluar'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Penerimaan',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Beli Jadi', 'icon' => 'circle-o', 'url' => ['/trn-beli-kain-jadi/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-beli-kain-jadi'
                            ],
                            [
                                'label' => 'Packing',
                                'icon' => 'th',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Penerimaan', 'icon' => 'circle-o', 'url' => ['/penerimaan-inspecting/index'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-inspecting'
                                    ],
                                    [
                                        'label' => 'Riwayat Penerimaan', 'icon' => 'circle-o', 'url' => ['/gudang-jadi/index'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'gudang-jadi'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Packing MKL BJ',
                                'icon' => 'th',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Penerimaan', 'icon' => 'circle-o', 'url' => ['/penerimaan-inspecting-mkl-bj/index'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-inspecting-mkl-bj' && in_array($actionId, ['index', 'view'])
                                    ],
                                    [
                                        'label' => 'Riwayat Penerimaan', 'icon' => 'circle-o', 'url' => ['/penerimaan-inspecting-mkl-bj/history'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'penerimaan-inspecting-mkl-bj' && $actionId === 'history'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Makloon',
                                'icon' => 'th',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Process', 'icon' => 'circle-o', 'url' => ['/trn-terima-makloon-process/index'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-terima-makloon-process'
                                    ],
                                    [
                                        'label' => 'Finish', 'icon' => 'circle-o', 'url' => ['/trn-terima-makloon-finish/index'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-terima-makloon-finish'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Retur', 'icon' => 'circle-o', 'url' => ['/trn-retur-buyer/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-retur-buyer'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Pengiriman',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Ke Buyer',
                                'icon' => 'th-list',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Pengiriman', 'icon' => 'circle-o', 'url' => ['/trn-kirim-buyer-header/index'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kirim-buyer-header'
                                    ],
                                    [
                                        'label' => 'Rekap', 'icon' => 'circle-o', 'url' => ['/trn-kirim-buyer/rekap'],
                                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-kirim-buyer' && $actionId == 'rekap'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Makloon', 'icon' => 'circle-o', 'url' => ['/trn-kirim-makloon/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-kirim-makloon'
                            ],
                            [
                                'label' => 'Makloon V2', 'icon' => 'circle-o', 'url' => ['/trn-kirim-makloon-v2/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'trn-kirim-makloon-v2'
                            ],
                        ]
                    ],
                    [
                        'label' => 'Stock Mutasi', 'icon' => 'circle', 'url' => ['/gudang-jadi-mutasi/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'gudang-jadi-mutasi'
                    ],
                    [
                        'label' => 'Rekap', 'icon' => 'circle', 'url' => ['/trn-gudang-jadi/rekap'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-gudang-jadi' && $actionId == 'rekap'
                    ],
                ],
            ],

            [
                'label' => 'GUDANG EX FINISH',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Mutasi', 'icon' => 'circle-o', 'url' => ['/mutasi-ex-finish/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mutasi-ex-finish'
                    ],
                    [
                        'label' => 'Stock Ex Finish Retur', 'icon' => 'circle-o', 'url' => ['/trn-stock-ef/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'trn-stock-ef' && $actionId='index'
                    ],
                    [
                        'label' => 'Stock Ex Finish GD Jadi', 'icon' => 'circle-o', 'url' => ['/mutasi-ex-finish-alt-item/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'mutasi-ex-finish-alt-item' && $actionId='index'
                    ],
                    [
                        'label' => 'Penjualan', 'icon' => 'circle-o', 'url' => ['/jual-ex-finish/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'jual-ex-finish' && $actionId='index'
                    ],
                    [
                        'label' => 'Pengiriman', 'icon' => 'circle-o', 'url' => ['/surat-jalan-ex-finish/index'],
                        'active' => $moduleId=='app-backend' && $controllerId == 'surat-jalan-ex-finish' && $actionId='index'
                    ],
                    [
                        'label' => 'PFP KELUAR',
                        'icon' => 'th-list',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Stock', 'icon' => 'circle-o', 'url' => ['/pfp-keluar-verpacking-item/index'],
                                'active' => $moduleId=='app-backend' && $controllerId == 'pfp-keluar-verpacking-item' && $actionId == 'index'
                            ],
                        ]
                    ],
                ]
            ],

            [
                'label' => 'REKAP',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'SALES CONTRACT',
                        'icon' => 'circle',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'SC', 'icon' => 'circle-o', 'url' => ['/rekap/sc'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='sc'
                            ],
                        ]
                    ],
                    [
                        'label' => 'MARKETING ORDER',
                        'icon' => 'circle',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'MO', 'icon' => 'circle-o', 'url' => ['/rekap/mo'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='mo'
                            ],
                            [
                                'label' => 'MO Color', 'icon' => 'circle-o', 'url' => ['/rekap/mo-color'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='mo-color'
                            ],
                        ]
                    ],
                    [
                        'label' => 'WORKING ORDER',
                        'icon' => 'circle',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'WO', 'icon' => 'circle-o', 'url' => ['/rekap/wo'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='wo'
                            ],
                            [
                                'label' => 'WO Color', 'icon' => 'circle-o', 'url' => ['/rekap/wo-color'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='wo-color'
                            ],
                            [
                                'label' => 'Outstanding PMC', 'icon' => 'circle-o', 'url' => ['/rekap/outstanding-pmc'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='outstanding-pmc'
                            ],
                            [
                                'label' => 'Accounting', 'icon' => 'circle-o', 'url' => ['/rekap/accounting'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'rekap' && $actionId=='accounting'
                            ],
                        ]
                    ],
                    [
                        'label' => 'GD. GREIGE',
                        'icon' => 'circle',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'STOCK GREIGE', 'icon' => 'circle-o', 'url' => ['/trn-stock-greige/rekap'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'trn-stock-greige' && $actionId=='rekap'
                            ],
                            /*[
                                'label' => 'BELI GREIGE', 'icon' => 'circle-o', 'url' => ['/trn-buy-greige/rekap'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'trn-buy-greige' && $actionId=='rekap'
                            ],*/
                        ]
                    ],
                    [
                        'label' => 'REALISASI',
                        'icon' => 'circle',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'DYEING', 'icon' => 'circle-o', 'url' => ['/realisasi-dyeing/rekap'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'realisasi-dyeing' && $actionId=='rekap'
                            ],
                            [
                                'label' => 'DYEING FORMATED NK', 'icon' => 'circle-o', 'url' => ['/realisasi-dyeing/rekap-formated'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'realisasi-dyeing' && $actionId=='rekap-formated'
                            ],
                            [
                                'label' => 'DYEING FORMATED', 'icon' => 'circle-o', 'url' => ['/realisasi-dyeing/rekap-formated-no-nk'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'realisasi-dyeing' && $actionId=='rekap-formated-no-nk'
                            ],
                            [
                                'label' => 'PRINTING', 'icon' => 'circle-o', 'url' => ['/realisasi-printing/rekap'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'realisasi-printing' && $actionId=='rekap'
                            ],
                            [
                                'label' => 'Outstanding Bukaan Dyeing', 'icon' => 'circle-o', 'url' => ['/realisasi-dyeing/rekap-outstanding-bukaan-dyeing'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'realisasi-dyeing' && $actionId=='rekap-outstanding-bukaan-dyeing'
                            ],
                            [
                                'label' => 'Outstanding Bukaan PFP', 'icon' => 'circle-o', 'url' => ['/realisasi-pfp/rekap-outstanding-bukaan-pfp'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'realisasi-pfp' && $actionId=='rekap-outstanding-bukaan-pfp'
                            ],
                            /*[
                                'label' => 'BELI GREIGE', 'icon' => 'circle-o', 'url' => ['/trn-buy-greige/rekap'],
                                'active' => $moduleId == 'app-backend' && $controllerId == 'trn-buy-greige' && $actionId=='rekap'
                            ],*/
                        ]
                    ],
                ]
            ],

            [
                'label' => 'Admin',
                'icon' => 'user',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Users', 'icon' => 'circle', 'url' => ['/user/user/index'],
                        'active' => $moduleId=='user' && $controllerId == 'user'
                    ],
                    [
                        'label' => 'RBAC Control',
                        'icon' => 'circle',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'User', 'icon' => 'circle-o', 'url' => ['/admin/user'],
                                'active' => $moduleId == 'admin' && $controllerId == 'user'
                            ],
                            [
                                'label' => 'Assignment', 'icon' => 'circle-o', 'url' => ['/admin/assignment'],
                                'active' => $moduleId == 'admin' && $controllerId == 'assignment'
                            ],
                            [
                                'label' => 'Role', 'icon' => 'circle-o', 'url' => ['/admin/role'],
                                'active' => $moduleId == 'admin' && $controllerId == 'role'
                            ],
                            [
                                'label' => 'Permission', 'icon' => 'circle-o', 'url' => ['/admin/permission'],
                                'active' => $moduleId == 'admin' && $controllerId == 'permission'
                            ],
                            [
                                'label' => 'Route', 'icon' => 'circle-o', 'url' => ['/admin/route'],
                                'active' => $moduleId == 'admin' && $controllerId == 'route'
                            ],
                            [
                                'label' => 'Rule', 'icon' => 'circle-o', 'url' => ['/admin/rule'],
                                'active' => $moduleId == 'admin' && $controllerId == 'rule'
                            ],
                        ],
                    ],
                ],
            ],

            [
                'label' => 'Raw Data',
                'icon' => 'wrench',
                'url' => '#',
                'items' => [
                    /*[
                        'label' => 'rawdata', 'icon' => 'circle', 'url' => ['/rawdata/default'],
                        'active' => $moduleId=='rawdata' && $controllerId == 'rawdata'
                    ],*/
                    [
                        'label' => 'Referensi Data',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Banks', 'icon' => 'circle', 'url' => ['/rawdata/mst-bank-account/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'mst-bank-account'
                            ],
                            [
                                'label' => 'Currency', 'icon' => 'circle', 'url' => ['/rawdata/mst-currency/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'mst-currency'
                            ],
                        ],
                    ],
                    [
                        'label' => 'Master Data',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [

                            [
                                'label' => 'Greige Group', 'icon' => 'circle', 'url' => ['/rawdata/mst-greige-group/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'mst-greige-group'
                            ],
                            [
                                'label' => 'Greige', 'icon' => 'circle', 'url' => ['/rawdata/mst-greige/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'mst-greige'
                            ],
                            [
                                'label' => 'Customer', 'icon' => 'circle', 'url' => ['/rawdata/mst-customer/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'mst-customer'
                            ],
                            // bagussona
                            // [
                            //     'label' => 'Location', 'icon' => 'circle', 'url' => ['/rawdata/mst-location/index'],
                            //     'active' => $moduleId=='rawdata' && $controllerId == 'mst-location'
                            // ],
                            // [
                            //     'label' => 'Sub-Location', 'icon' => 'circle', 'url' => ['/rawdata/mst-location/index'],
                            //     'active' => $moduleId=='rawdata' && $controllerId == 'mst-location'
                            // ],
                        ],
                    ],
                    [
                        'label' => 'Transaksi',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Sales Contract',
                                'icon' => 'th',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Sales Contract', 'icon' => 'circle', 'url' => ['/rawdata/trn-sc/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-sc'
                                    ],
                                    [
                                        'label' => 'SC Greige Group', 'icon' => 'circle', 'url' => ['/rawdata/trn-sc-greige/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-sc-greige'
                                    ],
                                    [
                                        'label' => 'SC Agen', 'icon' => 'circle', 'url' => ['/rawdata/trn-sc-agen/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-sc-agen'
                                    ],
                                    [
                                        'label' => 'SC Komisi', 'icon' => 'circle', 'url' => ['/rawdata/trn-sc-komisi/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-sc-komisi'
                                    ],
                                    [
                                        'label' => 'SC Memo', 'icon' => 'circle', 'url' => ['/rawdata/trn-sc-memo/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-sc-memo'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Marketing Order',
                                'icon' => 'th',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Marketing Order', 'icon' => 'circle', 'url' => ['/rawdata/trn-mo/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-mo'
                                    ],
                                    [
                                        'label' => 'Mo Color', 'icon' => 'circle', 'url' => ['/rawdata/trn-mo-color/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-mo-color'
                                    ],
                                    [
                                        'label' => 'Mo Memo', 'icon' => 'circle', 'url' => ['/rawdata/trn-mo-memo/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-mo-memo'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Work Order',
                                'icon' => 'th',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Work Order', 'icon' => 'circle', 'url' => ['/rawdata/trn-wo/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-wo'
                                    ],
                                    [
                                        'label' => 'Wo Color', 'icon' => 'circle', 'url' => ['/rawdata/trn-wo-color/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-wo-color'
                                    ],
                                    [
                                        'label' => 'Wo Memo', 'icon' => 'circle', 'url' => ['/rawdata/trn-wo-memo/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-wo-memo'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Order PFP', 'icon' => 'circle', 'url' => ['/rawdata/trn-order-pfp/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-order-pfp'
                            ],
                            [
                                'label' => 'Stock Greige', 'icon' => 'circle', 'url' => ['/rawdata/trn-stock-greige/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-stock-greige'
                            ],
                            [
                                'label' => 'Kartu Proses',
                                'icon' => 'bars',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Dyeing',
                                        'icon' => 'th',
                                        'url' => '#',
                                        'items' => [
                                            [
                                                'label' => 'Kartu Proses', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-dyeing/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-dyeing'
                                            ],
                                            [
                                                'label' => 'Kartu Proses Item', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-dyeing-item/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-dyeing-item'
                                            ],
                                            [
                                                'label' => 'Proses', 'icon' => 'circle', 'url' => ['/rawdata/kartu-process-dyeing-process/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'kartu-process-dyeing-process'
                                            ],
                                        ]
                                    ],

                                    [
                                        'label' => 'Printing',
                                        'icon' => 'th',
                                        'url' => '#',
                                        'items' => [
                                            [
                                                'label' => 'Kartu Proses', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-printing/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-printing'
                                            ],
                                            [
                                                'label' => 'Kartu Proses Item', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-printing-item/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-printing-item'
                                            ],
                                            [
                                                'label' => 'Proses', 'icon' => 'circle', 'url' => ['/rawdata/kartu-process-printing-process/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'kartu-process-printing-process'
                                            ],
                                        ]
                                    ],

                                    [
                                        'label' => 'PFP',
                                        'icon' => 'th',
                                        'url' => '#',
                                        'items' => [
                                            [
                                                'label' => 'Kartu Proses', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-pfp/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-pfp'
                                            ],
                                            [
                                                'label' => 'Kartu Proses Item', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-pfp-item/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-pfp-item'
                                            ],
                                            [
                                                'label' => 'Proses', 'icon' => 'circle', 'url' => ['/rawdata/kartu-process-pfp-process/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'kartu-process-pfp-process'
                                            ],
                                        ]
                                    ],
                                    [
                                        'label' => 'Celup',
                                        'icon' => 'th',
                                        'url' => '#',
                                        'items' => [
                                            [
                                                'label' => 'Kartu Proses', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-celup/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-celup'
                                            ],
                                            [
                                                'label' => 'Kartu Proses Item', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-celup-item/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-celup-item'
                                            ],
                                            [
                                                'label' => 'Proses', 'icon' => 'circle', 'url' => ['/rawdata/kartu-process-celup-process/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'kartu-process-celup-process'
                                            ],
                                        ]
                                    ],
                                    [
                                        'label' => 'Maklon',
                                        'icon' => 'th',
                                        'url' => '#',
                                        'items' => [
                                            [
                                                'label' => 'Kartu Proses', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-maklon/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-maklon'
                                            ],
                                            [
                                                'label' => 'Kartu Proses Item', 'icon' => 'circle', 'url' => ['/rawdata/trn-kartu-proses-maklon-item/index'],
                                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kartu-proses-maklon-item'
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Inspecting',
                                'icon' => 'bars',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Inspecting', 'icon' => 'circle', 'url' => ['/rawdata/trn-inspecting/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-inspecting'
                                    ],
                                    [
                                        'label' => 'Inspecting Items', 'icon' => 'circle', 'url' => ['/rawdata/trn-inspecting-item/index'],
                                        'active' => $moduleId=='rawdata' && $controllerId == 'trn-inspecting-item'
                                    ],
                                ]
                            ],
                        ],
                    ],
                    [
                        'label' => 'GUDANG JADI',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Stock', 'icon' => 'circle', 'url' => ['/rawdata/trn-gudang-jadi/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-gudang-jadi' && $actionId == 'index'
                            ],
                            [
                                'label' => 'Kirim Buyer Bal', 'icon' => 'circle', 'url' => ['/rawdata/trn-kirim-buyer-bal/index'],
                                'active' => $moduleId == 'rawdata' && $controllerId == 'trn-kirim-buyer-bal' && $actionId == 'index'
                            ],
                            [
                                'label' => 'Kirim Item Bal', 'icon' => 'circle', 'url' => ['/rawdata/trn-kirim-buyer-item/index'],
                                'active' => $moduleId=='rawdata' && $controllerId == 'trn-kirim-buyer-item' && $actionId == 'index'
                            ],
                        ],
                    ],
                ],
            ],

            [
                'label' => 'Reset',
                'icon' => 'share',
                'url' => '#',
                'items' => [
                    /*[
                        'label' => 'Default', 'icon' => 'file-code-o', 'url' => ['/reset/default/index'],
                        'active' => $moduleId=='reset' && $controllerId == 'default'
                    ],
                    [
                        'label' => 'WO',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Un Approve', 'icon' => 'file-code-o', 'url' => ['/reset/wo/unapprove', 'id'=>''],
                                'active' => $moduleId=='reset' && $controllerId == 'wo'
                            ],
                        ],
                    ]*/
                ],
            ],

            [
                'label' => 'System',
                'icon' => 'share',
                'url' => '#',
                'items' => [
                    [
                        'label' => 'Backup Database', 'icon' => 'file-code-o', 'url' => ['/db-manager/default/index'],
                        'active' => $moduleId=='db-manager' && $controllerId == 'default'
                    ],
                ],
            ],

            /*[
                'label' => '-',
                'icon' => 'bars',
                'url' => '#',
                'items' => [
                    [
                        'label'=>'-',
                        'icon' => 'circle-o',
                        'url' => ['']
                    ]
                ]
            ],*/
        ];
        ?>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => \mdm\admin\components\Helper::filter($menuItems),
            ]
        ) ?>

    </section>

</aside>