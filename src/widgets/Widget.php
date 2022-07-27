<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @version 0.1
 */

namespace simialbi\yii2\widgets;

use simialbi\yii2\i18n\TranslationTrait;

/**
 * Widget base class skeleton
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.1
 */
class Widget extends \yii\base\Widget
{
    use TranslationTrait;
    use WidgetTrait;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        parent::init();
    }
}
