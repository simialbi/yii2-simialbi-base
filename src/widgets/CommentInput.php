<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2019 Simon Karlen
 */

namespace simialbi\yii2\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class CommentInput extends InputWidget {
	/**
	 * @var string User image (optional)
	 */
	public $image;
	/**
	 * @var array the HTML attributes for the image tag.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $imageOptions = [
		'class' => ['rounded-circle'],
		'style' => [
			'height'          => '50px',
			'object-fit'      => 'cover',
			'object-position' => 'center',
			'width'           => '50px'
		]
	];
	/**
	 * @var array the HTML attributes for the button tag. You can override `icon` property to set button content.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $buttonOptions = [
		'class' => ['btn', 'btn-primary']
	];

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		$options       = $this->options;
		$buttonOptions = $this->buttonOptions;
		ArrayHelper::setValue($options, 'style.height', '0');
		ArrayHelper::setValue($options, 'style.min-height', '2.5rem');

		$icon = ArrayHelper::remove($buttonOptions, 'icon', '🖅');

		$html = Html::beginTag('div', ['class' => 'input-group']);
		if ($this->image) {
			$html .= Html::beginTag('div', ['class' => 'input-group-prepend']);
			$html .= Html::img($this->image, $this->imageOptions);
			$html .= Html::endTag('div');
		}
		if ($this->hasModel()) {
			$html .= Html::activeTextarea($this->model, $this->attribute, $options);
		} else {
			$html .= Html::textarea($this->name, $this->value, $options);
		}
		$html .= Html::beginTag('div', ['class' => 'input-group-append']);
		$html .= Html::submitButton($icon, $buttonOptions);
		$html .= Html::endTag('div');
		$html .= Html::endTag('div');
		$this->registerPlugin();

		return $html;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function registerPlugin($pluginName = 'autosize') {
		$view = $this->view;
		$id   = $this->options['id'];
		CommentInputAsset::register($view);
		$view->registerJs("$pluginName(jQuery('#$id'));");
	}
}