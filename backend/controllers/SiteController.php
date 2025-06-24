<?php
namespace backend\controllers;


use backend\models\form\PasswordResetRequestForm;
use backend\models\form\ResetPasswordForm;
use backend\models\form\VerifyEmailForm;
use InvalidArgumentException;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use common\models\ar\TrnWo;
use common\models\ar\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use yii\web\NotAcceptableHttpException;
use kartik\mpdf\Pdf;
use common\jobs\EmailWoJob;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends Controller
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
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    

// public function actionKirimEmail()
// {
//     $id = Yii::$app->request->post('id');
//     $emails = Yii::$app->request->post('selectedEmails', []);

//     if (!$id) {
//         throw new \yii\web\BadRequestHttpException('Parameter "id" tidak ditemukan.');
//     }

//     if (empty($emails)) {
//         Yii::$app->session->setFlash('error', 'Silakan pilih setidaknya satu penerima email.');
//         return $this->redirect(['trn-wo/view', 'id' => $id]);
//     }

//     $model = \common\models\ar\TrnWo::findOne($id);

//     if (!$model) {
//         throw new \yii\web\NotFoundHttpException('Data WO tidak ditemukan.');
//         }

//     // Ambil data lain seperti mo, sc, greige
//     $mo = $model->mo;
//     $scGreige = $model->scGreige;

//     // Generate PDF
//     $content = $this->renderPartial('@backend/views/trn-wo/print/print', [
//         'model' => $model,
//         'mo' => $mo,
//         'scGreige' => $scGreige
//     ]);


    
//     $pdfPath = Yii::getAlias('@runtime/wo_' . $model->id . '.pdf');
//     $pdf = new \kartik\mpdf\Pdf([
//         'mode' => \kartik\mpdf\Pdf::MODE_BLANK,
//         'format' => \kartik\mpdf\Pdf::FORMAT_FOLIO,
//         'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
//         'destination' => \kartik\mpdf\Pdf::DEST_FILE,
//         'content' => $content,
//         'cssInline' => 'body { font-size: 10px; }',
//         'filename' => $pdfPath,
//     ]);
//     $pdf->render();

//     // Kirim email ke semua penerima
//     Yii::$app->mailer->compose()
//         ->setFrom('infogajahapp@examplegmail.com')
//         ->setTo($emails) // ✅ langsung array
//         ->setSubject('Working Order No. ' . $model->no)
//         ->setTextBody('Silakan lihat lampiran Working Order dalam bentuk PDF.')
//         ->attach($pdfPath)
//         ->send();

//     Yii::$app->session->setFlash('success', 'Email berhasil dikirim ke: ' . implode(', ', $emails));
//     return $this->redirect(['trn-wo/view', 'id' => $model->id]);
// }



// public function actionKirimEmail()
// {
//     $id = Yii::$app->request->post('id');
//     $emails = Yii::$app->request->post('selectedEmails', []);

//     if (!$id) {
//         throw new \yii\web\BadRequestHttpException('Parameter "id" tidak ditemukan.');
//     }

//     if (empty($emails)) {
//         Yii::$app->session->setFlash('error', 'Silakan pilih setidaknya satu penerima email.');
//         return $this->redirect(['trn-wo/view', 'id' => $id]);
//     }

//     // ✅ Gunakan eager loading untuk menghindari lazy loading di view
//     $model = \common\models\ar\TrnWo::find()
//         ->where(['id' => $id])
//         ->with(['mo', 'scGreige'])
//         ->one();

//     if (!$model) {
//         throw new \yii\web\NotFoundHttpException('Data WO tidak ditemukan.');
//     }

//     // ✅ Generate PDF sebagai string (lebih efisien)
//     $pdfContent = $this->generatePdfContent($model);

//     // ✅ Simpan ke file sementara
//     $pdfPath = Yii::getAlias('@runtime/wo_' . $model->id . '_' . time() . '.pdf');
//     file_put_contents($pdfPath, $pdfContent);

//     // ✅ Kirim email
//     Yii::$app->mailer->compose()
//         ->setFrom('infogajahapp@gmail.com') // Ganti dengan email Anda yang valid
//         ->setTo($emails)
//         ->setSubject('Working Order No. ' . $model->no)
//         ->setTextBody('Silakan lihat lampiran Working Order dalam bentuk PDF.')
//         ->attach($pdfPath)
//         ->send();

//     // ✅ Hapus file PDF setelah dikirim
//     @unlink($pdfPath);

//     Yii::$app->session->setFlash('success', 'Email berhasil dikirim ke: ' . implode(', ', $emails));
//     return $this->redirect(['trn-wo/view', 'id' => $model->id]);
// }

// protected function generatePdfContent($model)
// {
//     $mo = $model->mo;
//     $scGreige = $model->scGreige;

//     // ✅ Gunakan switch tanpa PROCESS_OPTIONS agar lebih aman
//     switch ($scGreige->process) {
//         case $scGreige::PROCESS_DYEING:
//             $content = $this->renderPartial('@backend/views/trn-wo/print/print', [
//                 'model' => $model,
//                 'mo' => $mo,
//                 'scGreige' => $scGreige
//             ]);
//             $processName = 'Dyeing';
//             break;
//         case $scGreige::PROCESS_PRINTING:
//             $content = $this->renderPartial('@backend/views/trn-wo/print/print', [
//                 'model' => $model,
//                 'mo' => $mo,
//                 'scGreige' => $scGreige
//             ]);
//             $processName = 'Printing';
//             break;
//         default:
//             $procName = $scGreige::processOptions()[$scGreige->process] ?? 'Unknown';
//             throw new NotAcceptableHttpException("Mohon maaf, untuk sementara proses \"{$procName}\" belum didukung.");
//     }

//     // ✅ Generate PDF sebagai string (DEST_STRING lebih cepat)
//     $pdf = new Pdf([
//         'mode' => Pdf::MODE_BLANK,
//         'format' => Pdf::FORMAT_FOLIO,
//         'orientation' => Pdf::ORIENT_PORTRAIT,
//         'destination' => Pdf::DEST_STRING,
//         'content' => $content,
//         'cssInline' => '
//             .row { margin: 0; padding: 0; }
//             [class^="col-"] { border: 0; padding: 5px 0; }
//             body { font-size: 10px; }
//         ',
//         'options' => ['title' => 'Working Order - ' . $model->id],
//         'methods' => [
//             'SetTitle' => 'WORKING ORDER - ' . $model->id,
//             'SetHeader' => ['WORKING ORDER ' . $processName . '||NO: ' . $model->no],
//             'SetFooter' => ['Page {PAGENO}'],
//         ],
//     ]);

//     return $pdf->render();
// }

public function actionKirimEmail()
{
    $id = Yii::$app->request->post('id');
    $emails = Yii::$app->request->post('selectedEmails', []);

    if (!$id) {
        throw new \yii\web\BadRequestHttpException('Parameter "id" tidak ditemukan.');
    }

    if (empty($emails)) {
        Yii::$app->session->setFlash('error', 'Silakan pilih setidaknya satu penerima email.');
        return $this->redirect(['trn-wo/view', 'id' => $id]);
    }

    // Kirim ke queue
    Yii::$app->queue->push(new \common\jobs\KirimEmailJob([
        'modelId' => $id,
        'emails' => $emails,
    ]));

    Yii::$app->session->setFlash('success', 'Permintaan pengiriman email telah dijadwalkan.');
    return $this->redirect(['trn-wo/view', 'id' => $id]);
}

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'main-login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionKirimEmailMemo()
{
    $id = Yii::$app->request->post('id');
    $emails = Yii::$app->request->post('selectedEmails', []);

    if (!$id) {
        throw new \yii\web\BadRequestHttpException('Parameter "id" tidak ditemukan.');
    }

    if (empty($emails)) {
        Yii::$app->session->setFlash('error', 'Silakan pilih setidaknya satu penerima email.');
        return $this->redirect(['trn-wo-memo/view', 'id' => $id]);
    }

    // Kirim ke queue
    Yii::$app->queue->push(new \common\jobs\KirimEmailMemo([
        'modelId' => $id,
        'emails' => $emails,
    ]));

    Yii::$app->session->setFlash('success', 'Permintaan pengiriman email telah dijadwalkan.');
    return $this->redirect(['trn-wo-memo/view', 'id' => $id]);
}

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return string
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionVerifyEmail($token)
    {
        $this->layout = 'main-login';

        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if($model->load(Yii::$app->request->post())){
            if ($user = $model->verifyEmail()) {
                if (Yii::$app->user->login($user)) {
                    Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                    return $this->goHome();
                }
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        }

        return $this->render('verify-email', ['model'=>$model]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->render('requestPasswordResetToken', [
                    'model' => new PasswordResetRequestForm(),
                ]);
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'main-login';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {
        // ambil user yang sedang login
        $model = User::findOne(Yii::$app->user->id);

        if (!$model) {
            throw new NotFoundHttpException('User tidak ditemukan.');
        }
        

        // handle POST
        if ($model->load(Yii::$app->request->post())) {

            // handle upload foto
            $file = UploadedFile::getInstance($model, 'foto');
            if ($file) {
                // path folder upload
                $uploadPath = Yii::getAlias('@webroot/uploads/avatar/');
                
                // path file lama
                $oldFilePath = $uploadPath . $model->foto;

                // kalau file lama ada, hapus dulu
                if ($model->foto && file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }

                // buat nama file baru
                $fileName = 'avatar_' . Yii::$app->user->id . '.' . $file->extension;
                $filePath = $uploadPath . $fileName;

                if ($file->saveAs($filePath)) {
                    $model->foto = $fileName;
                }
            }



            // kalau password diisi, hash password baru
            if (!empty($model->password)) {
                $model->setPassword($model->password);
            }


            // simpan model
            if ($model->save()) { // false untuk skip validation tertentu
                Yii::$app->session->setFlash('success', 'Profile berhasil diperbarui.');
                return $this->redirect(['profile']);
            }
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

}