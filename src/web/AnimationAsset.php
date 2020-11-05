<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\web;

/**
 * Animation asset bundle vor css animations
 * @package simialbi\yii2\web
 * @see https://github.com/daneden/animate.css
 */
class AnimationAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/animate.css';
    /**
     * {@inheritdoc}
     */
    public $css = [
        'animate.min.css'
    ];
}