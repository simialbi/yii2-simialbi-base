<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 0.1
 */

namespace simialbi\yii2\web;

/**
 * AssetBundle skeleton
 *
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 0.1
 */
class AssetBundle extends \yii\web\AssetBundle {
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		if (!isset($this->sourcePath)) {
			$reflector        = new \ReflectionClass(static::className());
			$dir              = rtrim(dirname($reflector->getFileName()), '\\/').DIRECTORY_SEPARATOR.'assets';
			$this->sourcePath = $dir;
		}

		parent::init();
	}
}