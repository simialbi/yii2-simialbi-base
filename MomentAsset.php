<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 0.1
 */

namespace simialbi\yii2;

/**
 * Class MomentAsset
 *
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 0.1
 */
class MomentAsset extends AssetBundle {
	/**
	 * @var string the directory that contains the source asset files for this asset bundle.
	 */
	public $sourcePath = '@npm/moment/min';

	/**
	 * @var array list of JavaScript files that this bundle contains.
	 */
	public $js = [
		'moment-with-locales.min.js'
	];

	/**
	 * @var array the options to be passed to [[AssetManager::publish()]] when the asset bundle
	 * is being published.
	 */
	public $publishOptions = [
		'forceCopy' => YII_DEBUG
	];
}