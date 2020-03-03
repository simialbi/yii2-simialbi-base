<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2020 Simon Karlen
 */

namespace simialbi\yii2;

use simialbi\yii2\web\AssetBundle;

/**
 * AssetBundle providing base scripts and styles
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.9
 */
class BaseAsset extends AssetBundle {
	/**
	 * {@inheritDoc}
	 */
 	public $js = [
 		'js/sa.js'
	];

	/**
	 * {@inheritDoc}
	 */
 	public $depends = [
 		'yii\web\JqueryAsset'
	];
}