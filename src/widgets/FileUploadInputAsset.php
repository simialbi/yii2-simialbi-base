<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\widgets;

use simialbi\yii2\web\AssetBundle;

/**
 * Class FileUploadInputAsset
 * @package simialbi\yii2\widgets
 */
class FileUploadInputAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/resumablejs';

    /**
     * {@inheritdoc}
     */
    public $js = [
        'resumable.js'
    ];
}