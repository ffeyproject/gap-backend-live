<?php

namespace backend\modules\reset\controllers;

use common\models\ar\TrnWo;
use yii\helpers\BaseVarDumper;
use yii\web\Controller;

/**
 * Default controller for the `reset` module
 */
class WoController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionUnapprove($id)
    {
        $model = TrnWo::findOne(['id'=>$id, 'apv_by_mengetahui'=>true, 'apv_by_marketing'=>true]);

        $data = [];
        if($model !== null){
            $model->no_urut = null;
            $model->no = null;
            $model->apv_by_mengetahui = false;
            $model->apv_mengetahui_time = null;
            $model->apv_note_mengetahui = null;
            $model->apv_by_marketing = false;
            $model->apv_marketing_time = null;
            $model->apv_note_marketing = null;
            $model->save(false, [
                'no_urut', 'no', 'apv_by_mengetahui', 'apv_mengetahui_time', 'apv_note_mengetahui', 'apv_by_marketing', 'apv_marketing_time',
                'apv_note_marketing'
            ]);

            $data = $model->toArray();
        }

        BaseVarDumper::dump($data, 10, true);
    }

    public function actionUnpost($id)
    {
        $model = TrnWo::findOne(['id'=>$id, 'posted'=>true]);

        $data = [];
        if($model !== null){
            //approval
            $model->no_urut = null;
            $model->no = null;
            $model->apv_by_mengetahui = false;
            $model->apv_mengetahui_time = null;
            $model->apv_note_mengetahui = null;
            $model->apv_by_marketing = false;
            $model->apv_marketing_time = null;
            $model->apv_note_marketing = null;
            //approval
            $model->posted = false;
            $model->save(false, [
                'no_urut', 'no', 'apv_by_mengetahui', 'apv_mengetahui_time', 'apv_note_mengetahui', 'apv_by_marketing', 'apv_marketing_time',
                'apv_note_marketing', 'posted'
            ]);

            $data = $model->toArray();
        }

        BaseVarDumper::dump($data, 10, true);
    }
}
