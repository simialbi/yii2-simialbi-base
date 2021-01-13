<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @version 0.1
 */

namespace simialbi\yii2\widgets;

use simialbi\yii2\i18n\TranslationTrait;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * InputWidget base class skeleton
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.1
 */
class InputWidget extends \yii\widgets\InputWidget
{
    use TranslationTrait;
    use WidgetTrait;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        parent::init();
    }
}