<?php
/**
 * @package yii2-simialbi-base
 * @author Sandro Venetz <sandro.venetz@raiffeisen.ch
 */

namespace simialbi\yii2\widgets;

use simialbi\yii2\web\AssetBundle;

class ConditionBuilderAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@simialbi/yii2/assets';

    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/conditionBuilderDefault.css',
        //'css/conditionBuilderDark.css',
    ];

    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/conditionBuilder/query-builder.standalone.js',
        'js/conditionBuilder/i18n/query-builder.de.js',
        'js/conditionBuilder/i18n/query-builder.en.js',
        'js/conditionBuilder/i18n/query-builder.fr.js',
    ];

    /**
     * {@inheritdoc}
     */
    public $publishOptions = [
        'forceCopy' => YII_DEBUG
    ];
}
