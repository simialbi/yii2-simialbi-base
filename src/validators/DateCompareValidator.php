<?php

namespace simialbi\yii2\validators;

use simialbi\yii2\helpers\FormatConverter;
use simialbi\yii2\web\MomentAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\Validator;

/**
 * DateCompareValidator compares two dates against each other. This validator is useful, if one does not want to
 * use the timestampAttribute of the DateValidator
 *
 * @author Sandro Venetz <sandro.venetz@raiffeisen.ch>
 * @date 19.01.2022
 */
class DateCompareValidator extends Validator
{
    /** @var string The second attribute to compare against */
    public $compareAttribute = '';

    /** @var string How to compare the 2 attributes, allowed operators are: ===, !==, <, <=, >, >= */
    public $operator = '';

    /** @var string Needed for client-side validation. ICU or php formats allowed. Prefix php formats with php: */
    public $format = '';

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if ($this->compareAttribute === '') {
            throw new InvalidConfigException('Property \'compareAttribute\' not set');
        }
        if ($this->operator === '') {
            throw new InvalidConfigException('Property \'operator\' not set');
        }
        if ($this->enableClientValidation && $this->format === '') {
            throw new InvalidConfigException('Property \'format\' must be set if client-side validation is enabled');
        }
        if ($this->message === null) {
            switch ($this->operator) {
                case '===':
                    $this->message = Yii::t(
                        'yii',
                        '{attribute} must be equal to "{compareValueOrAttribute}".'
                    );
                    break;
                case '!==':
                    $this->message = Yii::t(
                        'yii',
                        '{attribute} must not be equal to "{compareValueOrAttribute}".'
                    );
                    break;
                case '>':
                    $this->message = Yii::t(
                        'yii',
                        '{attribute} must be greater than "{compareValueOrAttribute}".'
                    );
                    break;
                case '>=':
                    $this->message = Yii::t(
                        'yii',
                        '{attribute} must be greater than or equal to "{compareValueOrAttribute}".'
                    );
                    break;
                case '<':
                    $this->message = Yii::t(
                        'yii',
                        '{attribute} must be less than "{compareValueOrAttribute}".'
                    );
                    break;
                case '<=':
                    $this->message = Yii::t(
                        'yii',
                        '{attribute} must be less than or equal to "{compareValueOrAttribute}".'
                    );
                    break;
                default:
                    throw new InvalidConfigException("Unknown operator: {$this->operator}");
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $t1 = Yii::$app->formatter->asTimestamp($model->{$attribute});
        $t2 = Yii::$app->formatter->asTimestamp($model->{$this->compareAttribute});
        if (!$this->compareValues($this->operator, $t1, $t2)) {
            $this->addError($model, $attribute, $this->message, [
                'compareValueOrAttribute' => $model->getAttributeLabel($this->compareAttribute)
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clientValidateAttribute($model, $attribute, $view): ?string
    {
        MomentAsset::register($view);

        $options = $this->getClientOptions($model, $attribute);
        $jsonOptions = Json::encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // convert from icu/php to moment format
        if (strncmp($this->format, 'php:', 4) === 0) {
            $this->format = FormatConverter::convertDatePhpToIcu(substr($this->format, 4));
        }
        $this->format = FormatConverter::convertDateIcuToMoment($this->format);


        return <<<JS
var options = {$jsonOptions};

if (options.skipOnEmpty && yii.validation.isEmpty(value)) {
    return;
}

var compareValue, valid = true;

var target = \$('#' + options.compareAttribute);
if (!target.length) {
    target = \$form.find('[name="' + options.compareAttributeName + '"]');
}
if (!target.length) {
    target = \$('#' + attribute.id.replace(/{$attribute}$/, '{$this->compareAttribute}'));
}
compareValue = target.val();

var t1 = moment(value, '{$this->format}').unix();
var t2 = moment(compareValue, '{$this->format}').unix();

switch (options.operator) {
    case '===':
        valid = t1 === t2;
        break;
    case '!==':
        valid = t1 !== t2;
        break;
    case '>':
        valid = t1 > t2;
        break;
    case '>=':
        valid = t1 >= t2;
        break;
    case '<':
        valid = t1 < t2;
        break;
    case '<=':
        valid = t1 <= t2;
        break;
    default:
        valid = false;
        break;
}

if (!valid) {
    yii.validation.addMessage(messages, options.message, value);
}
JS;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientOptions($model, $attribute): array
    {
        $options = [
            'operator' => $this->operator
        ];

        $compareAttribute = $this->compareAttribute;
        $compareValue = $model->getAttributeLabel($compareAttribute);
        $options['compareAttribute'] = Html::getInputId($model, $compareAttribute);
        $options['compareAttributeName'] = Html::getInputName($model, $compareAttribute);
        $compareLabel = $compareValueOrAttribute = $model->getAttributeLabel($compareAttribute);

        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        $options['message'] = $this->formatMessage($this->message, [
            'attribute' => $model->getAttributeLabel($attribute),
            'compareAttribute' => $compareLabel,
            'compareValue' => $compareValue,
            'compareValueOrAttribute' => $compareValueOrAttribute,
        ]);

        return $options;
    }

    /**
     * Compares two timestamps with the specified operator.
     * @param string $operator the comparison operator
     * @param mixed $value the value being compared
     * @param mixed $compareValue another value being compared
     * @return bool whether the comparison using the specified operator is true.
     */
    protected function compareValues(string $operator, $value, $compareValue): bool
    {
        switch ($operator) {
            case '===':
                return $value === $compareValue;
            case '!==':
                return $value !== $compareValue;
            case '>':
                return $value > $compareValue;
            case '>=':
                return $value >= $compareValue;
            case '<':
                return $value < $compareValue;
            case '<=':
                return $value <= $compareValue;
            default:
                return false;
        }
    }
}
