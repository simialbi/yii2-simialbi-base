<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2019 Simon Karlen
 */

namespace simialbi\yii2\widgets;

use marqu3s\summernote\Summernote;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class CommentInput extends InputWidget
{
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
            'height' => '50px',
            'object-fit' => 'cover',
            'object-position' => 'center',
            'width' => '50px'
        ]
    ];
    /**
     * @var boolean Use rich text field (summernote) instead of default textarea. The package
     * "simialbi/yii2-summernote" is needed to use this feature.
     */
    public $richTextField = false;
    /**
     * @var array Summernote plugin options
     */
    public $summernoteClientOptions = [];
    /**
     * @var array|boolean the HTML attributes for the image wrapper tag. Set to false to disable wrapping
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
     * @var array|boolean the HTML attributes for the button wrapper tag. Set to false to disable wrapping
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $buttonWrapperOptions = [
        'class' => ['input-group-append']
    ];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if ($this->richTextField && !class_exists('marqu3s\summernote\Summernote')) {
            throw new InvalidConfigException("The package 'simialbi/yii2-summernote' is needed to use this feature.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $template = $this->template;
        $options = $this->options;
        $buttonOptions = $this->buttonOptions;
        Html::addCssStyle($options, ['height' => 0, 'min-height' => '2.5rem']);

        $icon = ArrayHelper::remove($buttonOptions, 'icon', '🖅');
        $image = '';

        if ($this->image) {
            $image .= $this->imageWrapperOptions ? Html::beginTag('div', $this->imageWrapperOptions) : '';
            $image .= Html::img($this->image, $this->imageOptions);
            $image .= $this->imageWrapperOptions ? Html::endTag('div') : '';
        }
        if ($this->richTextField) {
            $css = <<<CSS
.input-group > .note-editor {
    flex: 1 1 auto;
    margin-bottom: 0;
    min-width: 0;
    width: 1%;
}
CSS;
            $this->view->registerCss($css, [], 'input-group-summernote');

            if ($this->hasModel()) {
                $input = Summernote::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'options' => $options,
                    'clientOptions' => $this->summernoteClientOptions
                ]);
            } else {
                $input = Summernote::widget([
                    'name' => $this->name,
                    'value' => $this->value,
                    'options' => $this->options,
                    'clientOptions' => $this->summernoteClientOptions
                ]);
            }
        } else {
            if ($this->hasModel()) {
                $input = Html::activeTextarea($this->model, $this->attribute, $options);
            } else {
                $input = Html::textarea($this->name, $this->value, $options);
            }
            $this->registerPlugin();
        }

        $button = $this->buttonWrapperOptions ? Html::beginTag('div', $this->buttonWrapperOptions) : '';
        $button .= Html::submitButton($icon, $buttonOptions);
        $button .= $this->buttonWrapperOptions ? Html::endTag('div') : '';

        return strtr($template, [
            '{beginWrapper}' => Html::beginTag('div', ['class' => 'input-group']),
            '{image}' => $image,
            '{input}' => $input,
            '{submit}' => $button,
            '{endWrapper}' => Html::endTag('div')
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function registerPlugin($pluginName = 'autosize', $selector = null)
    {
        $view = $this->view;
        $id = $this->options['id'];
        CommentInputAsset::register($view);
        if (empty($selector)) {
            $selector = "#$id";
        }
        if ($this->richTextField) {
            $selector .= ' + .note-editor > .note-editing-area > .note-editable';
        }
        $view->registerJs("$pluginName(jQuery('$selector'));");
    }
}
