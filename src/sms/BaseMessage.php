<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\sms;

use Yii;
use yii\base\ErrorHandler;

/**
 * BaseMessage serves as a base class that implements the [[send()]] method required by [[MessageInterface]].
 *
 * By default, [[send()]] will use the "smsProvider" application component to send the current message.
 * The "smsProvider" application component should be a provider instance implementing [[ProviderInterface]].
 *
 * @see BaseProvider
 *
 * @author Simon Karlen <karlen@tonic.ag>
 */
abstract class BaseMessage implements MessageInterface
{
    /**
     * @var ProviderInterface the provider instance that created this message.
     * For independently created messages this is `null`.
     */
    public ProviderInterface $provider;

    /**
     * {@inheritDoc}
     */
    public function getRegion(): string
    {
        $language = explode('-', Yii::$app->language);
        return (count($language) > 1) ? $language[1] : strtoupper($language[0]);
    }

    /**
     * {@inheritDoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function send(ProviderInterface $provider = null): bool
    {
        if ($provider === null && $this->provider === null) {
            $provider = Yii::$app->get('smsProvider');
        } elseif ($provider === null) {
            $provider = $this->provider;
        }

        return $provider->send($this);
    }

    /**
     * PHP magic method that returns the string representation of this object.
     * @return string the string representation of this object.
     */
    public function __toString(): string
    {
        // __toString cannot throw exception
        // use trigger_error to bypass this limitation
        try {
            return $this->toString();
        } catch (\Exception $e) {
            ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }
}
