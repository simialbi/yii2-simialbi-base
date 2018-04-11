<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 0.1
 */

namespace simialbi\yii2\widgets;

use simialbi\yii2\i18n\TranslationTrait;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;

/**
 * InputWidget base class skeleton
 *
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 0.1
 */
class InputWidget extends \yii\widgets\InputWidget {
	use TranslationTrait;

	/**
	 * @var array the options for the underlying JS plugin.
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
	 * {@inheritdoc}
	 * @throws \yii\base\InvalidConfigException
	 */
	public function init() {
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
		}
		parent::init();
	}

	/**
	 * Registers a specific plugin and the related events
	 *
	 * @param string $pluginName optional plugin name
	 */
	protected function registerPlugin($pluginName = null) {
		$view = $this->view;

		$className      = static::className();
		$assetClassName = $className."Asset";
		if (empty($pluginName)) {
			$pluginName = strtolower(StringHelper::basename($className));
		}
		if (class_exists($assetClassName)) {
			/**
			 * @var \simialbi\yii2\web\AssetBundle $assetClassName
			 */
			$assetClassName::register($view);
		}

		$id = $this->options['id'];

		if ($this->clientOptions !== false) {
			$options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
			$js      = "jQuery('#$id').$pluginName($options);";
			$view->registerJs($js);
		}

		$this->registerClientEvents();
	}

	/**
	 * Registers JS event handlers that are listed in [[clientEvents]].
	 */
	protected function registerClientEvents() {
		if (!empty($this->clientEvents)) {
			$id = $this->options['id'];
			$js = [];
			foreach ($this->clientEvents as $event => $handler) {
				$js[] = "jQuery('#$id').on('$event', $handler);";
			}
			$this->view->registerJs(implode("\n", $js));
		}
	}
}