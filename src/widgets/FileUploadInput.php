<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Class FileUploadInput
 * @package simialbi\yii2\widgets
 */
class FileUploadInput extends InputWidget
{
    /**
     * @var boolean|string The jQuery selector of the textarea to combine FileUpload with
     */
    public $combineWithTextarea = false;

    /**
     * @var string|null Tooltip to show on button
     */
    public $tooltip;

    /**
     * @var array the HTML attributes for the widget container tag. The following special tokens are recognized
     * and will be specially treated:
     *  - `content`: _string_, The button content
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var array|string the url to be used to upload files
     * @see https://github.com/23/resumable.js#full-documentation
     */
    public $uploadUrl;

    /**
     * @var array|string the url to check if a file exists
     * @see https://github.com/23/resumable.js#full-documentation
     */
    public $fileExistsUrl;

    /**
     * @var array|string the url to delete an existing file
     * @see https://github.com/23/resumable.js#full-documentation
     */
    public $deleteUrl;

    /**
     * @var string|null The template for the files items being uploaded. Will be set if not set. The following special
     * tokens are recognized and will be replaced:
     *  - `{identifier}`: _string_, Unique file identifier
     *  - `{name}`: _string_, The file name
     */
    public $itemTemplate;

    /**
     * @var string|null The template for the files items already uploaded (typical used in update function).
     * Will be set if not set. The following special tokens are recognized and will be replaced:
     *  - `{identifier}`: _string_, Unique file identifier
     *  - `{name}`: _string_, The file name
     *  - `{link}`: _string_, The file url
     */
    public $initialItemTemplate;

    /**
     * @var array Extra parameters to include in the multipart request with data.
     */
    public $params = [];

    /**
     * @var boolean Whether to start upload automatically after file add or not.
     */
    public $autoUpload = true;
    /**
     * @var boolean
     */
    public $showProgressBar = true;

    /**
     * @var string The jQuery selector of the placeholder element
     */
    public $filePlaceholder;

    /**
     * @var array An array of initial files to render. The unique ids of the files are the key and the urls the values.
     */
    public $files = [];

    /**
     * @var boolean Render the placeholder or not
     */
    private $_renderPlaceholder = false;

    /**
     * {@inheritDoc}
     */
    public function init()
    {

        parent::init();

        if (empty($this->filePlaceholder)) {
            $this->_renderPlaceholder = true;
            $this->filePlaceholder = '#' . $this->options['id'] . '-file-placeholder';
        }
        if (empty($this->itemTemplate)) {
            $this->itemTemplate = Html::beginTag('div', [
                'class' => ['d-flex', 'align-items-center', 'justify-content-stretch', 'mb-2', 'bg-light', 'px-3', 'py-2'],
                'id' => 'file-{identifier}'
            ]);
            $this->itemTemplate .= Html::tag('span', '{name}', [
                'class' => ['file-name', 'flex-grow-0']
            ]);
            $progressOptions = ['class' => ['flex-grow-1', 'mx-2']];
            if ($this->showProgressBar) {
                Html::addCssClass($progressOptions, 'progress');
            }
            $this->itemTemplate .= Html::beginTag('div', $progressOptions);
            if ($this->showProgressBar) {
                $this->itemTemplate .= Html::tag('div', '', [
                    'class' => ['progress-bar'],
                    'role' => 'progressbar',
                    'aria-valuenow' => '0',
                    'aria-valuemin' => '0',
                    'aria-valuemax' => '100'
                ]);
            }
            $this->itemTemplate .= Html::endTag('div');
            $this->itemTemplate .= Html::a('&times;', 'javascript:;', [
                'class' => ['delete-link', 'flex-grow-0', 'text-dark', 'd-none']
            ]);
            $this->itemTemplate .= Html::endTag('div');
        }
        if (empty($this->initialItemTemplate)) {
            $this->initialItemTemplate = Html::beginTag('div', [
                'class' => ['d-flex', 'align-items-center', 'justify-content-between', 'mb-2', 'bg-light', 'px-3', 'py-2'],
                'id' => 'file-{identifier}'
            ]);
            $this->initialItemTemplate .= $this->hasModel()
                ? Html::activeHiddenInput($this->model, $this->attribute . '[]', [
                    'value' => '{identifier}',
                    'id' => $this->options['id'] . '-{identifier}'
                ])
                : Html::hiddenInput($this->name . '[]', '{identifier}');
            $this->initialItemTemplate .= Html::tag('a', '{name}', [
                'class' => ['file-name', 'flex-grow-0'],
                'href' => '{link}',
                'target' => '_blank',
                'data' => ['pjax' => '0']
            ]);
            $this->initialItemTemplate .= Html::a('&times;', 'javascript:;', [
                'class' => ['delete-link', 'flex-grow-0', 'text-dark'],
                'data' => ['unique-id' => '{identifier}']
            ]);
            $this->initialItemTemplate .= Html::endTag('div');
        }
        Html::removeCssClass($this->options, 'form-control');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $options = $this->options;
        $content = ArrayHelper::remove($options, 'content', '');
        $html = '';
        if ($this->_renderPlaceholder) {
            $placeholderContent = '';
            if (!empty($this->files)) {
                foreach ($this->files as $identifier => $file) {
                    $placeholderContent .= strtr($this->initialItemTemplate, [
                        '{identifier}' => $identifier,
                        '{link}' => $file,
                        '{name}' => basename($file)
                    ]);
                }
            }

            $html .= Html::tag('div', $placeholderContent, [
                'id' => $this->options['id'] . '-file-placeholder'
            ]);
        }
        if ($this->tooltip) {
            $options['data']['toggle'] = 'tooltip';
            $options['title'] = Html::encode($this->tooltip);
        }
        $html .= Html::button($content, $options);

        $this->registerPlugin('Resumable');

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    protected function registerPlugin($pluginName = null, $selector = null)
    {
        $id = $this->options['id'];
        $var = Inflector::variablize($id);
        $options = Json::encode($this->getClientOptions());

        FileUploadInputAsset::register($this->view);

        $js = '';

        if (false !== $this->combineWithTextarea) {
            $js .= <<<JS
var textarea$var = jQuery('{$this->combineWithTextarea}');
var formGroup$var = textarea$var.closest('.form-group');
var button$var = jQuery('#$id');

formGroup$var.css({position: 'relative'});
button$var.appendTo(formGroup$var).css({
    position: 'absolute',
    left: '1.5rem',
    top: textarea$var.height() - button$var.height()
});

JS;
        }

        if ($this->tooltip) {
            $js .= "jQuery('#$id').tooltip();\n";
        }

        $js .= <<<JS
var resumable$var = new $pluginName($options);
resumable$var.assignBrowse(document.getElementById('$id'));

JS;
        $fileAdded = "function fileAdded(file) {\n";
        if ($this->autoUpload) {
            $fileAdded .= "resumable$var.upload();\n";
        }
        $fileAdded .= <<<JS
var container = jQuery('{$this->filePlaceholder}');
var el = '{$this->itemTemplate}';
el = el.replace('{identifier}', file.uniqueIdentifier);
el = el.replace('{name}', file.fileName);
container.append(el);
JS;

        $fileAdded .= '}';
        $this->clientEvents['fileAdded'] = new JsExpression($fileAdded);

        if ($this->showProgressBar) {
            $fileProgress = <<<JS
function fileProgress(file) {
    var el = jQuery('#file-' + file.uniqueIdentifier),
        progress = file.progress() * 100;
    el.find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%');
}
JS;
            $this->clientEvents['fileProgress'] = new JsExpression($fileProgress);
        }

        $inputName = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        $fileSuccess = <<<JS
function fileSuccess(file, msg) {
    var el = jQuery('#file-' + file.uniqueIdentifier),
        uFile = JSON.parse(msg),
        deleteUrl = '{$this->deleteUrl}';
    el.find('.file-name').replaceWith('<a class="file-name flex-grow-0" href="' + uFile.path + '" target="_blank" data-pjax="0">' + file.fileName + '</a>');
    el.prepend('<input type="hidden" name="{$inputName}[]" value="' + file.uniqueIdentifier + '">');
    if (deleteUrl) {
        el.find('.delete-link').removeClass('d-none').show().on('click', function () {
            jQuery.ajax({
                url: deleteUrl + '?identifier=' + file.uniqueIdentifier,
                method: 'DELETE'
            }).done(function () {
                jQuery('#file-' + file.uniqueIdentifier).remove();
            });
        });
    }
}
JS;
        if (!empty($this->files) && $this->deleteUrl) {
            $ids = implode(',#file-', array_keys($this->files));
            $js .= <<<JS
jQuery('#file-$ids').find('.delete-link').on('click', function () {
    debugger;
    var el = jQuery(this);
    jQuery.ajax({
        url: '{$this->deleteUrl}?identifier=' + el.data('uniqueId'),
        method: 'DELETE'
    }).done(function () {
        jQuery('#file-' + el.data('uniqueId')).remove();
    });
});
JS;

        }

        $this->clientEvents['fileSuccess'] = new JsExpression($fileSuccess);

        $this->view->registerJs($js);

        $this->registerClientEvents($selector);
    }

    /**
     * {@inheritDoc}
     */
    protected function registerClientEvents($selector = null)
    {
        if (!empty($this->clientEvents)) {
            $id = $this->options['id'];
            $var = Inflector::variablize($id);

            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "resumable$var.on('$event', $handler);";
            }
            $this->view->registerJs(implode("\n", $js));
        }
    }

    /**
     * @return array
     */
    protected function getClientOptions()
    {
        $clientOptions = $this->clientOptions;
        $params = $this->params;
        if (Yii::$app->request->enableCsrfValidation) {
            $params[Yii::$app->request->csrfParam] = Yii::$app->request->getCsrfToken();
        }

        return ArrayHelper::merge([
            'target' => Url::to($this->uploadUrl),
            'testTarget' => Url::to($this->fileExistsUrl),
            'query' => $params,
            'chunkNumberParameterName' => ArrayHelper::remove($clientOptions, 'chunkNumberParameterName', false),
            'totalChunksParameterName' => ArrayHelper::remove($clientOptions, 'totalChunksParameterName', false),
            'chunkSizeParameterName' => ArrayHelper::remove($clientOptions, 'chunkSizeParameterName', false),
            'relativePathParameterName' => ArrayHelper::remove($clientOptions, 'relativePathParameterName', false),
            'currentChunkSizeParameterName' => ArrayHelper::remove($clientOptions, 'currentChunkSizeParameterName', false)
        ], $this->clientOptions);
    }

}
