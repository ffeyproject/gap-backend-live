<?php
namespace backend\modules\user\models\form;

use backend\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * This is the model class for table "article".
 *
 * @property User $user
 * @property UploadedFile $signatureFile
 */
class SignatureForm extends Model
{
    public $signatureFile;
    public $user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                'signatureFile',
                'file',
                'skipOnEmpty' => false,
                'extensions' => 'png, jpg, jpeg',
                'maxSize'=> (0.5 * (1024 * 1024)), //0.5MB
            ],
        ];
    }

    /**
     * @return bool
     */
    public function upload(){
        if($this->validate()){
            if($this->user->signature !== null){
                $path = Yii::getAlias('@webroot/uploads/signature/'.$this->user->signature);
                if(file_exists($path)){unlink($path);}
            }

            $this->user->signature = Yii::$app->security->generateRandomString().'.'.$this->signatureFile->extension;
            $this->signatureFile->saveAs('@webroot/uploads/signature/'.$this->user->signature);
            $this->user->save(false, ['signature']);
        }

        return false;
    }
}