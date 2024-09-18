<?php

use common\models\User;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */
/* @var $user User */

\backend\assets\AppAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic');


$user = Yii::$app->user->identity;
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset, 'user'=>$user]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset, 'user'=>$user]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset, 'user'=>$user]
        ) ?>

    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>