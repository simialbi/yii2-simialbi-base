<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\widgets;


use simialbi\yii2\web\AssetBundle;
use yii\helpers\Json;
use yii\helpers\StringHelper;

trait WidgetTrait
{
    /**
     * @var array|false the options for the underlying JS plugin.
     */
    public $clientOptions = [];
    /**
     * @var array the event handlers for the underlying JS plugin.
     */
    public $clientEvents = [];
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * Registers a specific plugin and the related events
     *
     * @param string|null $pluginName optional plugin name
     * @param string|null $selector optional javascript selector for the plugin initialization. Defaults to widget id.
     */
    protected function registerPlugin(?string $pluginName = null, ?string $selector = null)
    {
        $view = $this->view;
        $id = $this->options['id'];

        $className = static::class;
        $assetClassName = str_replace('widgets\\', '', $className . "Asset");
        if (empty($pluginName)) {
            $pluginName = strtolower(StringHelper::basename($className));
        }
        if (empty($selector)) {
            $selector = "#$id";
        }
        if (class_exists($assetClassName)) {
            /**
             * @var AssetBundle $assetClassName
             */
            $assetClassName::register($view);
        }

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('$selector').$pluginName($options);";
            $view->registerJs($js);
        }

        $this->registerClientEvents($selector);
    }

    /**
     * Registers JS event handlers that are listed in [[clientEvents]].
     *
     * @param string|null $selector optional javascript selector for the plugin initialization. Defaults to widget id.
     */
    protected function registerClientEvents(?string $selector = null)
    {
        if (!empty($this->clientEvents)) {
            $id = $this->options['id'];

            if (empty($selector)) {
                $selector = "#$id";
            }

            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('$selector').on('$event', $handler);";
            }
            $this->view->registerJs(implode("\n", $js));
        }
    }
}
