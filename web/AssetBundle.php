<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @version 0.1
 */

namespace simialbi\yii2\web;

/**
 * AssetBundle skeleton
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.1
 */
class AssetBundle extends \yii\web\AssetBundle {
	/**
	 * {@inheritdoc}
	 */
	public $sourcePath = '__AUTO_SET__';
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		if ($this->sourcePath === '__AUTO_SET__') {
			$reflector        = new \ReflectionClass(static::className());
			$dir              = rtrim(dirname($reflector->getFileName()), '\\/').DIRECTORY_SEPARATOR.'assets';
			$this->sourcePath = $dir;
		}

		parent::init();
	}
}
