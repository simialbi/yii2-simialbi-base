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
	 * @var string optional template to render the input group content
	 */
	public $template = '{beginWrapper}{image}{input}{submit}{endWrapper}';
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
	 * @var array the HTML attributes for the image wrapper tag.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $imageWrapperOptions = [
		'class' => ['input-group-prepend']
	];
	/**
	 * @var array the HTML attributes for the button tag. You can override `icon` property to set button content.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $buttonOptions = [
		'class' => ['btn', 'btn-primary']
	];
	/**
	 * @var array the HTML attributes for the button wrapper tag.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $buttonWrapperOptions = [
		'class' => ['input-group-append']
	];

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		$template      = $this->template;
		$options       = $this->options;
		$buttonOptions = $this->buttonOptions;
		ArrayHelper::setValue($options, 'style.height', '0');
		ArrayHelper::setValue($options, 'style.min-height', '2.5rem');

		$icon   = ArrayHelper::remove($buttonOptions, 'icon', '🖅');
		$image  = '';

		if ($this->image) {
			$image = Html::beginTag('div', $this->imageWrapperOptions);
			$image .= Html::img($this->image, $this->imageOptions);
			$image .= Html::endTag('div');
		}
		if ($this->hasModel()) {
			$input = Html::activeTextarea($this->model, $this->attribute, $options);
		} else {
			$input = Html::textarea($this->name, $this->value, $options);
		}
		$button = Html::beginTag('div', $this->buttonWrapperOptions);
		$button .= Html::submitButton($icon, $buttonOptions);
		$button .= Html::endTag('div');
		$this->registerPlugin();

		return strtr($template, [
			'{beginWrapper}' => Html::beginTag('div', ['class' => 'input-group']),
			'{image}'        => $image,
			'{input}'        => $input,
			'{submit}'       => $button,
			'{endWrapper}'   => Html::endTag('div')
		]);
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