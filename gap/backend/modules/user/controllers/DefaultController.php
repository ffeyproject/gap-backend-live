<?php

namespace backend\modules\penyesuaian\user;

use yii\web\Controller;

/**
 * Default controller for the `penyesuaian` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
