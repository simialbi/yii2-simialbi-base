<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\sms;

use yii\base\Event;

/**
 * SMSEvent represents the event parameter used for events triggered by [[BaseProvider]].
 *
 * By setting the [[isValid]] property, one may control whether to continue running the action.
 *
 * @author Simon Karlen <karlen@tonic.ag>
 */
class SMSEvent extends Event
{
    /**
     * @var MessageInterface the sms message being send.
     */
    public $message;
    /**
     * @var boolean if message was sent successfully.
     */
    public $isSuccessful;
    /**
     * @var boolean whether to continue sending an sms. Event handlers of
     * [[\tonic\hq\re\sms\BaseProvider::EVENT_BEFORE_SEND]] may set this property to decide whether
     * to continue send or not.
     */
    public $isValid = true;
}