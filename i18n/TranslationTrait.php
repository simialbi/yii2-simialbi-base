<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 0.1
 */

namespace simialbi\yii2\i18n;

use yii\helpers\StringHelper;
use Yii;

/**
 * TranslationTrait provides methods for translation usage in all simialbi extensions
 *
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 0.1
 */
trait TranslationTrait {
	/**
	 * Init translations
	 */
	public function registerTranslations() {
		$reflector = new \ReflectionClass(parent::className());
		$dir       = rtrim(dirname($reflector->getFileName()), '\\/').DIRECTORY_SEPARATOR.'messages';
		$category  = str_replace(StringHelper::basename(parent::className()), '', parent::className());
		$category  = rtrim(str_replace(['\\', 'yii2/'], ['/', ''], $category), '/').'*';

		if (!is_dir($dir)) {
			return;
		}

		Yii::$app->i18n->translations[$category] = [
			'class'            => 'yii\i18n\GettextMessageSource',
			'sourceLanguage'   => 'en-US',
			'basePath'         => $dir,
			'forceTranslation' => true
		];
	}
}