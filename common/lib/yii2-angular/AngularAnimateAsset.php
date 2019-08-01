<?php

namespace dee\angular;

/**
 * AngularAnimateAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.1
 */
class AngularAnimateAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dee/angular/assets/angular-animate';

    /**
     * @inheritdoc
     */
    public $js = [
        'angular-animate.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'dee\angular\AngularAsset'
    ];
}