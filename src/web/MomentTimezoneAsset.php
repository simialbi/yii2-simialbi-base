<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\web;

/**
 * MomentAsset
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.3.2
 */
class MomentTimezoneAsset extends AssetBundle
{
    /**
     * @var string the directory that contains the source asset files for this asset bundle.
     */
    public $sourcePath = '@bower/moment-timezone/builds';

    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $js = [
        'moment-timezone-with-data.min.js'
    ];

    /**
     * @var array the options to be passed to [[AssetManager::publish()]] when the asset bundle
     * is being published.
     */
    public $publishOptions = [
        'forceCopy' => YII_DEBUG
    ];

    /**
     * @var array list of bundle class names that this bundle depends on.
     */
    public $depends = [
        'simialbi\yii2\web\MomentAsset'
    ];
}