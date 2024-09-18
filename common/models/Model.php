<?php
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class Model extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @param string|int|null $key
     * @return array
     */
    public static function createMultiple($modelClass, $multipleModels = [], $key=null)
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];
        $keyName = $key === null ? 'id' : $key;

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, $keyName, $keyName));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item[$keyName]) && !empty($item[$keyName]) && isset($multipleModels[$item[$keyName]])) {
                    $models[] = $multipleModels[$item[$keyName]];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
}
