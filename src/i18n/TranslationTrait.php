<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @version 0.1
 */

namespace simialbi\yii2\i18n;

use ReflectionClass;
use ReflectionException;
use Yii;
use yii\helpers\StringHelper;

/**
 * TranslationTrait provides methods for translation usage in all simialbi extensions
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.1
 */
trait TranslationTrait
{
    /**
     * Init translations
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $reflector = new ReflectionClass(static::class);
        $dir = rtrim(dirname($reflector->getFileName()), '\\/');
        $dir = rtrim(preg_replace('#widgets$#', '', $dir), '\\/') . DIRECTORY_SEPARATOR . 'messages';
        $category = str_replace(StringHelper::basename(static::class), '', static::class);
        $category = rtrim(str_replace(['\\', 'yii2/', 'widgets', 'models'], ['/', ''], $category), '/') . '*';

        if (!is_dir($dir) || isset(Yii::$app->i18n->tanslations[$category])) {
            return;
        }

        Yii::$app->i18n->translations[$category] = [
            'class' => 'yii\i18n\GettextMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => $dir,
            'forceTranslation' => true
        ];
    }
}
