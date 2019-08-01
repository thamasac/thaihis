<?php

namespace dee\angular;

/**
 * AngularValidationAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AngularValidationAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dee/angular/assets/angular-validation/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        'angular-validation.js',
        'angular-validation-rule.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'dee\angular\AngularAsset'
    ];
}