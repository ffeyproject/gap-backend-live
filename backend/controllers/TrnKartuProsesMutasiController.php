<?php
namespace backend\controllers;

use common\models\ar\TrnKartuProsesDyeing;
use common\models\ar\TrnKartuProsesDyeingItem;
use common\models\ar\TrnKartuProsesDyeingSearch;
use common\models\ar\TrnKartuProsesPrinting;
use common\models\ar\TrnKartuProsesPrintingItem;
use common\models\ar\TrnKartuProsesPrintingSearch;
use common\models\ar\TrnStockGreige;
use common\models\ar\TrnWo;
use common\models\ar\TrnWoColor;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrnKartuProsesMutasiController handles creation of Kartu Proses from Stock Gudang Mutasi.
 */
class TrnKartuProsesMutasiController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-dyeing' => ['POST'],
                    'delete-printing' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchDyeing = new TrnKartuProsesDyeingSearch(['asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI]);
        $paramsDyeing = Yii::$app->request->queryParams;
        $paramsDyeing['TrnKartuProsesDyeingSearch']['asal_greige'] = TrnStockGreige::ASAL_GREIGE_MUTASI;
        $dataProviderDyeing = $searchDyeing->search($paramsDyeing);

        $searchPrinting = new TrnKartuProsesPrintingSearch(['asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI]);
        $paramsPrinting = Yii::$app->request->queryParams;
        $paramsPrinting['TrnKartuProsesPrintingSearch']['asal_greige'] = TrnStockGreige::ASAL_GREIGE_MUTASI;
        $dataProviderPrinting = $searchPrinting->search($paramsPrinting);

        return $this->render('index', [
            'searchDyeing' => $searchDyeing,
            'dataProviderDyeing' => $dataProviderDyeing,
            'searchPrinting' => $searchPrinting,
            'dataProviderPrinting' => $dataProviderPrinting,
        ]);
    }

    public function actionCreateDyeing()
    {
        $model = new TrnKartuProsesDyeing([
            'date' => date('Y-m-d'),
            'asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI,
            'dikerjakan_oleh' => '-',
            'lebar' => '-',
            'lusi' => '-',
            'pakan' => '-',
            'k_density_lusi' => '-',
            'k_density_pakan' => '-',
        ]);

        $selectedStocks = [];
        $selectedTubes = [];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $stockIds = $post['stock_ids'] ?? [];
            $tubes = $post['tubes'] ?? [];
            $woId = $post['TrnKartuProsesDyeing']['wo_id'] ?? null;

            if (!empty($stockIds)) {
                $selectedStocks = TrnStockGreige::find()->with('greige')->where(['id' => $stockIds])->all();
                $selectedTubes = $tubes;
            }

            if (empty($stockIds) || empty($tubes) || empty($woId)) {
                Yii::$app->session->setFlash('error', 'Semua field wajib diisi.');
            } else {
                $hasMissingTube = false;
                $selectedTubesList = [];
                foreach ($stockIds as $sId) {
                    if (!isset($tubes[$sId]) || !in_array($tubes[$sId], [1, 2])) {
                        $hasMissingTube = true;
                        break;
                    }
                    $selectedTubesList[] = (int)$tubes[$sId];
                }

                if ($hasMissingTube) {
                    Yii::$app->session->setFlash('error', 'Semua stock item harus dipilih tube-nya.');
                } elseif (!in_array(1, $selectedTubesList) || !in_array(2, $selectedTubesList)) {
                    Yii::$app->session->setFlash('error', 'Satu form harus ada minimal satu Tube Kiri dan satu Tube Kanan.');
                } else {
                    $wo = TrnWo::findOne($woId);
                    if ($wo === null) {
                        Yii::$app->session->setFlash('error', 'Working Order tidak ditemukan.');
                    } else {
                        $stocks = TrnStockGreige::findAll(['id' => $stockIds]);
                        if (count($stocks) !== count($stockIds)) {
                            Yii::$app->session->setFlash('error', 'Ada stock Gudang Mutasi yang tidak ditemukan.');
                        } else {
                            $invalidStock = false;
                            foreach ($stocks as $st) {
                                if ($st->jenis_gudang !== TrnStockGreige::JG_MUTASI_GD_JADI) {
                                    $invalidStock = true;
                                    break;
                                }
                            }

                            if ($invalidStock) {
                                Yii::$app->session->setFlash('error', 'Salah satu stock bukan dari Gudang Mutasi.');
                            } else {
                                $woColor = TrnWoColor::find()->joinWith('moColor')
                                    ->where(['wo_id' => $wo->id, 'trn_mo_color.color' => $stocks[0]->color])
                                    ->one();
                                
                                if ($woColor === null) {
                                    $woColor = TrnWoColor::find()->where(['wo_id' => $wo->id])->one();
                                }

                                $model->load($post);
                                $model->wo_id = $wo->id;
                                $model->wo_color_id = $woColor ? $woColor->id : null;
                                $model->mo_id = $wo->mo_id;
                                $model->sc_greige_id = $wo->sc_greige_id;
                                $model->sc_id = $wo->sc_id;

                                if ($model->validate()) {
                                    $transaction = Yii::$app->db->beginTransaction();
                                    try {
                                        if ($model->save(false)) {
                                            $allSaved = true;
                                            $errors = [];
                                            foreach ($stocks as $stock) {
                                                $item = new TrnKartuProsesDyeingItem([
                                                    'kartu_process_id' => $model->id,
                                                    'stock_id' => $stock->id,
                                                    'panjang_m' => $stock->panjang_m,
                                                    'tube' => $tubes[$stock->id],
                                                    'date' => date('Y-m-d'),
                                                    'mesin' => '-',
                                                    'note' => 'Mutasi dari Gudang Jadi',
                                                    'wo_id' => $model->wo_id,
                                                    'mo_id' => $model->mo_id,
                                                    'sc_greige_id' => $model->sc_greige_id,
                                                    'sc_id' => $model->sc_id,
                                                ]);

                                                if (!$item->save()) {
                                                    $allSaved = false;
                                                    $errors[] = implode(', ', \yii\helpers\ArrayHelper::getColumn($item->getErrors(), 0));
                                                    break;
                                                }
                                            }

                                            if ($allSaved) {
                                                $transaction->commit();
                                                Yii::$app->session->setFlash('success', 'Kartu Proses Dyeing berhasil dibuat.');
                                                return $this->redirect(['/trn-kartu-proses-dyeing/view', 'id' => $model->id]);
                                            } else {
                                                $transaction->rollBack();
                                                Yii::$app->session->setFlash('error', 'Gagal menyimpan item kartu proses: ' . implode('; ', $errors));
                                            }
                                        } else {
                                            $transaction->rollBack();
                                            Yii::$app->session->setFlash('error', 'Gagal menyimpan kartu proses.');
                                        }
                                    } catch (\Throwable $t) {
                                        $transaction->rollBack();
                                        Yii::$app->session->setFlash('error', $t->getMessage());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->render('create-dyeing', [
            'model' => $model,
            'selectedStocks' => $selectedStocks,
            'selectedTubes' => $selectedTubes,
        ]);
    }

    public function actionCreatePrinting()
    {
        $model = new TrnKartuProsesPrinting([
            'date' => date('Y-m-d'),
            'asal_greige' => TrnStockGreige::ASAL_GREIGE_MUTASI,
            'dikerjakan_oleh' => '-',
            'jenis_printing' => TrnKartuProsesPrinting::JENIS_PRINTING_DIGITAL,
        ]);

        $selectedStocks = [];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $stockIds = $post['stock_ids'] ?? [];
            $jenisPrinting = $post['jenis_printing'] ?? null;
            $woId = $post['TrnKartuProsesPrinting']['wo_id'] ?? null;

            if (!empty($stockIds)) {
                $selectedStocks = TrnStockGreige::find()->with('greige')->where(['id' => $stockIds])->all();
            }

            if (empty($stockIds) || empty($jenisPrinting) || empty($woId)) {
                Yii::$app->session->setFlash('error', 'Semua field wajib diisi.');
            } else {
                $wo = TrnWo::findOne($woId);
                if ($wo === null) {
                    Yii::$app->session->setFlash('error', 'Working Order tidak ditemukan.');
                } else {
                    $stocks = TrnStockGreige::findAll(['id' => $stockIds]);
                    if (count($stocks) !== count($stockIds)) {
                        Yii::$app->session->setFlash('error', 'Ada stock Gudang Mutasi yang tidak ditemukan.');
                    } else {
                        $invalidStock = false;
                        foreach ($stocks as $st) {
                            if ($st->jenis_gudang !== TrnStockGreige::JG_MUTASI_GD_JADI) {
                                $invalidStock = true;
                                break;
                            }
                        }

                        if ($invalidStock) {
                            Yii::$app->session->setFlash('error', 'Salah satu stock bukan dari Gudang Mutasi.');
                        } else {
                            $woColor = TrnWoColor::find()->joinWith('moColor')
                                ->where(['wo_id' => $wo->id, 'trn_mo_color.color' => $stocks[0]->color])
                                ->one();
                            
                            if ($woColor === null) {
                                $woColor = TrnWoColor::find()->where(['wo_id' => $wo->id])->one();
                            }

                            $model->load($post);
                            $model->wo_id = $wo->id;
                            $model->wo_color_id = $woColor ? $woColor->id : null;
                            $model->mo_id = $wo->mo_id;
                            $model->sc_greige_id = $wo->sc_greige_id;
                            $model->sc_id = $wo->sc_id;
                            if ($jenisPrinting !== null) {
                                $model->jenis_printing = $jenisPrinting;
                            }

                            if ($model->validate()) {
                                $transaction = Yii::$app->db->beginTransaction();
                                try {
                                    if ($model->save(false)) {
                                        $allSaved = true;
                                        $errors = [];
                                        foreach ($stocks as $stock) {
                                            $item = new TrnKartuProsesPrintingItem([
                                                'kartu_process_id' => $model->id,
                                                'stock_id' => $stock->id,
                                                'panjang_m' => $stock->panjang_m,
                                                'date' => date('Y-m-d'),
                                                'mesin' => '-',
                                                'note' => 'Mutasi dari Gudang Jadi',
                                                'wo_id' => $model->wo_id,
                                                'mo_id' => $model->mo_id,
                                                'sc_greige_id' => $model->sc_greige_id,
                                                'sc_id' => $model->sc_id,
                                            ]);

                                            if (!$item->save()) {
                                                $allSaved = false;
                                                $errors[] = implode(', ', \yii\helpers\ArrayHelper::getColumn($item->getErrors(), 0));
                                                break;
                                            }
                                        }

                                        if ($allSaved) {
                                            $transaction->commit();
                                            Yii::$app->session->setFlash('success', 'Kartu Proses Printing berhasil dibuat.');
                                            return $this->redirect(['/trn-kartu-proses-printing/view', 'id' => $model->id]);
                                        } else {
                                            $transaction->rollBack();
                                            Yii::$app->session->setFlash('error', 'Gagal menyimpan item kartu proses: ' . implode('; ', $errors));
                                        }
                                    } else {
                                        $transaction->rollBack();
                                        Yii::$app->session->setFlash('error', 'Gagal menyimpan kartu proses.');
                                    }
                                } catch (\Throwable $t) {
                                    $transaction->rollBack();
                                    Yii::$app->session->setFlash('error', $t->getMessage());
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->render('create-printing', [
            'model' => $model,
            'selectedStocks' => $selectedStocks,
        ]);
    }
}
