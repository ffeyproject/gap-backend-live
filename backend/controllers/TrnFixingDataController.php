<?php

namespace backend\controllers;

use common\models\ar\{ TrnInspecting, InspectingItem, InspectingMklBj, InspectingMklBjItems, TrnGudangJadi };
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\helpers\Url;
use yii\db\Expression;
use yii\data\ArrayDataProvider;
use yii\base\DynamicModel;

/**
 * TrnFixingDataController implements the CRUD actions for TrnGudangJadi, InspectingItem, InspectingMklbjItems model.
 */
class TrnFixingDataController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Indexing all TrnGudangJadi models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $searchModel = Yii::$app->request->post();

            $model = DynamicModel::validateData($searchModel, [
                [['startDate', 'endDate', 'tableName'], 'required'],
            ]);

            if($model->validate()){
                $startDate = strtotime($model['startDate'].' 00:00:00');
                $endDate = strtotime($model['endDate'].' 23:59:59');
                $tableName = $model['tableName'];
                $tableItem = $tableName == TrnInspecting::tableName() ? InspectingItem::tableName() : InspectingMklBjItems::tableName();
                $tableCode = $tableName == TrnInspecting::tableName() ? 'INS' : 'MKL';

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $query1 = new Query();
                    $query1->select([
                        $tableName.'.id '.$tableName.'_id',
                        $tableName.'.no '.$tableName.'_no',
                        $tableName.'.created_at '.$tableName.'_created_at',
                        'x.is_head '.$tableItem.'_is_head',
                        'x.id '.$tableItem.'_id',
                    ])
                    ->leftJoin($tableItem.' x', $tableName.'.id = x.inspecting_id')
                    ->where(['>=', $tableName.'.created_at', $startDate])
                    ->andWhere(['<=', $tableName.'.created_at', $endDate])
                    ->orderBy('x.id ASC')
                    ->from($tableName);
    
                    $result1 = $query1->all();
                    for ($i=0; $i < count($result1); $i++) { 
                        $query2 = new Query();
                        $command2 = $query2->createCommand()->update($tableItem, ['is_head' => 0],  ['id' => $result1[$i][$tableItem.'_id']]);
                        $command2->execute();
                    }

                    $transaction->commit();

                    echo '<script>alert("Success:\nBerhasil submit permintaan kamu!");</script>';

                    $params = ['start_date' => $startDate, 'end_date' => $endDate];

                    $url = Url::to(['trn-fixing-data/update', 'table_name' => $tableName] + $params);

                    return $this->redirect($url);
                } catch (\Throwable $th) {
                    $transaction->rollBack();
                    throw $th;
                }
            } else {
                $errorMessage = '';
                foreach ($model->getErrors() as $attribute => $errors) {
                    $errorMessage .= $errors[0].'\n';
                }
                echo '<script>alert("Error:\n' . $errorMessage . '");</script>';
            }
        }

        return $this->render('index');
    }

    public function actionUpdate()
    {
        $limit = 50;
        $query = new Query();
        $searchModel = Yii::$app->request->getQueryParams();

        $model = DynamicModel::validateData($searchModel, [
            [['start_date', 'end_date', 'table_name'], 'required'],
        ]);

        $startDate = $model['start_date'];
        $endDate = $model['end_date'];
        $tableName = $model['table_name'];
        $tableItem = $tableName == TrnInspecting::tableName() ? InspectingItem::tableName() : InspectingMklBjItems::tableName();
        $tableCode = $tableName == TrnInspecting::tableName() ? 'INS' : 'MKL';

        $query->select([
                $tableName.'.*',
                'trn_wo.no',
                // 'MIN(x.is_head) AS min_is_head'
                // 'MIN(COALESCE(x.is_head, 0)) AS min_is_head',
                'CASE WHEN MAX(x.is_head) = 1 THEN 1 ELSE 0 END AS have_checked'
            ])
            ->leftJoin('trn_wo', $tableName.'.wo_id = trn_wo.id')
            ->leftJoin($tableItem.' x', $tableName.'.id = x.inspecting_id')
            ->where(['>=', $tableName.'.created_at', $startDate])
            ->andWhere(['<=', $tableName.'.created_at', $endDate])
            ->groupBy(['x.inspecting_id', $tableName.'.id', 'trn_wo.no'])
            // ->having(['MIN(COALESCE(x.is_head, 0))' => 0])
            // ->having(['have_checked' => 0])
            ->from($tableName);

        $result = $query->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => $limit, // Number of items per page
            ]
        ]);

        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->getQueryParams();

            $query1 = new Query();
            $query1->select(['*'])->where(['=', 'inspecting_id', $request['id']])->orderBy($tableItem.'.id ASC')->from($tableItem);

            $result1 = $query1->all();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($result1 as $gIBOII) {
                    if ($tableName == TrnInspecting::tableName()) {
                        $modeltem = InspectingItem::findOne($gIBOII['id']);
                    } else {
                        $modeltem = InspectingMklBjItems::findOne($gIBOII['id']);
                    }

                    $qty_count = $query1->where(['=', 'join_piece', $modeltem['join_piece']])->andWhere(['=', 'inspecting_id', $modeltem['inspecting_id']])->from($tableItem)->count();
                    $is_head = $query1->orderBy('is_head DESC')->orderBy('id ASC')
                                ->where(['=', 'join_piece', $modeltem['join_piece']])
                                ->andWhere(['=', 'inspecting_id',  $modeltem['inspecting_id']])
                                ->andWhere(['<>', 'join_piece', ""])->from($tableItem)->one();
                    $qty_sum = $query1->where(['=', 'join_piece', $modeltem['join_piece']])->andWhere(['=', 'inspecting_id', $modeltem['inspecting_id']])->from($tableItem)->sum('qty');

                    $modeltem['qty_sum'] = ($is_head && ($is_head['id'] <> $modeltem['id'])) ? NULL : ($modeltem['join_piece'] == NULL || $modeltem['join_piece'] == "" ? $modeltem['qty'] : $qty_sum);
                    $modeltem['is_head'] = ($is_head && ($is_head['id'] <> $modeltem['id'])) ? 0 : 1;
                    $modeltem['qr_code'] = ($modeltem['qr_code'] <> NULL || $modeltem['qr_code']) ? $modeltem['qr_code'] : $tableCode.'-'.$modeltem['inspecting_id'].'-'.$modeltem['id'];
                    $modeltem['qty_count'] = ($is_head && ($is_head['id'] <> $modeltem['id'])) ? 0 : ($modeltem['join_piece'] == NULL || $modeltem['join_piece'] == "" ? 1 : $qty_count);

                    $modeltem->save();

                    $query2 = new Query();
                    $stock = $query2->select(['*'])->where(['=', 'id_from', $gIBOII['id']])->andWhere(['=', 'trans_from',  $tableCode])->from('trn_gudang_jadi')->one();
                    if ($stock) {
                        $modeStock = TrnGudangJadi::findOne($stock['id']);
                        $modeStock['qty'] = $qty_sum;
                        $modeStock->save();
                    }
                }

                $transaction->commit();

                // After the transaction is committed, refresh the page
                Yii::$app->response->refresh();
            } catch (\Throwable $th) {
                $transaction->rollBack();
                throw $th;
            }
        }

        return $this->render('update', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
