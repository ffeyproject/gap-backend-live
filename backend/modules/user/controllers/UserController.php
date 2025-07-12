<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\form\AddUserForm;
use backend\modules\user\models\form\SignatureForm;
use Yii;
use backend\modules\user\models\User;
use backend\modules\user\models\UserSearch;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'change-status' => ['POST'],
                    'change-status-aktif' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //jangan tampilkan super user pada user lain
        if(Yii::$app->user->id != '1'){
            $dataProvider->query->andWhere(['<>', 'id', '1']);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        /*$roles = $model->getRbacItems()['assigned'];
        $roles = array_keys($roles);
        BaseVarDumper::dump($roles, 10, true);Yii::$app->end();*/

        $modelSignature = new SignatureForm(['user'=>$model]);
        if($modelSignature->load(Yii::$app->request->post())){
            $modelSignature->signatureFile = UploadedFile::getInstance($modelSignature, 'signatureFile');
            if($modelSignature->upload()){
                Yii::$app->session->setFlash('success', 'Tanda tangan baru berhasil diunggah.');
                return $this->redirect(['view', 'id'=>$model->id]);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'modelSignature' => $modelSignature
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new AddUserForm();

        if ($model->load(Yii::$app->request->post()) && $model->addUser()) {
            Yii::$app->session->setFlash('success', 'User berhasil ditambahkan.');
            return $this->redirect(['view', 'id' => $model->user->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionChangeStatusAktif($id, $newStatus)
    {
        $model = $this->findModel($id);

        if (!in_array($newStatus, [User::STATUS_ACTIVE, User::STATUS_INACTIVE])) {
            throw new \yii\web\BadRequestHttpException('Status tidak valid.');
        }

        $model->status = $newStatus;

        if ($model->save(false, ['status'])) {
            Yii::$app->session->setFlash('success', 'Status pengguna berhasil diperbarui.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memperbarui status pengguna.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }



    public function actionChangeStatus($id)
    {
        $model = $this->findModel($id);

        // Toggle status notifikasi email
        $model->status_notif_email = $model->status_notif_email ? 0 : 1;

        if ($model->save(false, ['status_notif_email'])) {
            Yii::$app->session->setFlash('success', 'Status email notifikasi berhasil diperbarui.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal memperbarui status email notifikasi.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }


    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    /*public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $manager = Yii::$app->authManager;
        $manager->revokeAll($model->id);

        $model->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Assign items
     * @param string $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAssign($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $model = $this->findModel($id);
        $success = $model->assign($items);
        Yii::$app->getResponse()->format = 'json';
        return array_merge($model->getRbacItems(), ['success' => $success]);
    }

    /**
     * Assign items
     * @param string $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionRevoke($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $model = $this->findModel($id);
        $success = $model->revoke($items);
        Yii::$app->getResponse()->format = 'json';
        return array_merge($model->getRbacItems(), ['success' => $success]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdatePhoneNumber()
    {
        $id = Yii::$app->request->post('id');
        $phone = Yii::$app->request->post('phone_number');

        if (($model = User::findOne($id)) !== null) {
            $model->phone_number = $phone;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Phone number updated.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'User not found.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }
}