<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2019 Simon Karlen
 */

namespace simialbi\yii2\widgets;

use simialbi\yii2\web\AssetBundle;

/**
 * Class CommentInputAsset
 * @package simialbi\yii2\widgets
 */
class CommentInputAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/autosize/dist';

    /**
     * {@inheritdoc}
     */
    public $js = [
        'autosize.js'
    ];
}