<?php
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $user User */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <?php
                if(!Yii::$app->user->isGuest){
                    echo $this->render('_notification', ['directoryAsset'=>$directoryAsset]);
                }
                ?>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $user->getAvatarUrl() ?>" class="user-image" alt="User Image" />
                        <span class="hidden-xs"><?=$user->username?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $user->getAvatarUrl() ?>" class="img-circle" alt="User Image" />

                            <p><?=$user->username?>
                                <small>Member Since <?=Yii::$app->formatter->asDatetime($user->created_at)?></small>
                                <?php
                                $roles = [];
                                foreach ($user->roles as $userRole) {
                                    $roles[] =  $userRole->name;
                                }
                                //echo \yii\helpers\Json::encode($roles);
                                echo implode(',', $roles);
                                ?>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <!--<li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </li>-->
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(
                                    'Profile',
                                    ['/site/profile'],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>

                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <!--<li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>-->
            </ul>
        </div>
    </nav>
</header>