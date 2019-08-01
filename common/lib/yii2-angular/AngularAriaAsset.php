<?php

namespace dee\angular;

/**
 * AngularAriaAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.1
 */
class AngularAriaAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dee/angular/assets/angular-aria';

    /**
     * @inheritdoc
     */
    public $js = [
        'angular-aria.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'dee\angular\AngularAsset'
    ];
}