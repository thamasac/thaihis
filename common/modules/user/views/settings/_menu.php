<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\widgets\Menu;

/** @var dektrium\user\models\User $user */
$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

?>
<?= Menu::widget([
            'encodeLabels'=>false,
            'options' => [
                'class' => 'nav nav-pills nav-stacked'
            ],
            'items' => [
                ['label' => "<i class='fa fa-history'></i> ".Yii::t('chanpan', 'User Profile'),  'url' => ['/user/settings/profile']],
                ['label' => "<i class='fa fa-address-card'></i> ".Yii::t('user', 'Account'),  'url' => ['/user/settings/account']],
//                ['label' => Yii::t('user', 'Networks'), 'url' => ['/user/settings/networks'], 'visible' => $networksVisible],
            ]
        ]) ?>

