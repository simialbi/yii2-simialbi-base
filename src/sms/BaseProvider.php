<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\sms;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\ViewContextInterface;
use yii\web\View;

/**
 * BaseProvider serves as a base class that implements the basic functions required by [[ProviderInterface]].
 *
 * Concrete child classes should may focus on implementing the [[sendMessage()]] method.
 *
 * @see BaseMessage
 *
 * @property \yii\web\View $view View instance. Note that the type of this property differs in getter and setter. See
 * [[getView()]] and [[setView()]] for details.
 * @property string $viewPath The directory that contains the view files for composing sms messages. Defaults
 * to '@app/sms'.
 *
 * @author Simon Karlen <karlen@tonic.ag>
 */
abstract class BaseProvider extends Component implements ProviderInterface, ViewContextInterface
{
    /**
     * @event SMSEvent an event raised right before send.
     * You may set [[SMSEvent::isValid]] to be false to cancel the send.
     */
    const EVENT_BEFORE_SEND = 'beforeSend';
    /**
     * @event SMSEvent an event raised right after send.
     */
    const EVENT_AFTER_SEND = 'afterSend';

    /**
     * @var string|boolean layout view name. This is the layout used to render sms body.
     * The property can take the following values:
     *
     * - a relative view name: a view file relative to [[viewPath]], e.g., 'layouts/sms'.
     * - a [path alias](guide:concept-aliases): an absolute view file path specified as a path alias, e.g., '@app/sms/layout'.
     * - a boolean false: the layout is disabled.
     */
    public $layout = false;
    /**
     * @var array the configuration that should be applied to any newly created
     * sms message instance by [[createMessage()]] or [[compose()]]. Any valid property defined
     * by [[MessageInterface]] can be configured, such as `from`, `to`, `subject`, `body`, etc.
     *
     * For example:
     *
     * ```php
     * [
     *     'charset' => 'UTF-8',
     *     'from' => '+41 79 123 45 67'
     * ]
     * ```
     */
    public $messageConfig = [];
    /**
     * @var string the default class name of the new message instances created by [[createMessage()]]
     */
    public $messageClass = 'tonic\hq\re\sms\BaseMessage';
    /**
     * @var bool whether to save sms messages as files under [[fileTransportPath]] instead of sending them
     * to the actual recipients. This is usually used during development for debugging purpose.
     * @see fileTransportPath
     */
    public $useFileTransport = false;
    /**
     * @var string the directory where the sms messages are saved when [[useFileTransport]] is true.
     */
    public $fileTransportPath = '@runtime/sms';
    /**
     * @var callable a PHP callback that will be called by [[send()]] when [[useFileTransport]] is true.
     * The callback should return a file name which will be used to save the sms message.
     * If not set, the file name will be generated based on the current timestamp.
     *
     * The signature of the callback is:
     *
     * ```php
     * function ($provider, $message)
     * ```
     */
    public $fileTransportCallback;

    /**
     * @var \yii\base\View|array view instance or its array configuration.
     */
    private $_view = [];
    /**
     * @var string the directory containing view files for composing mail messages.
     */
    private $_viewPath;
    private $_message;

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public function compose($view = null, array $params = [])
    {
        $message = $this->createMessage();
        if ($view === null) {
            return $message;
        }

        if (!array_key_exists('message', $params)) {
            $params['message'] = $message;
        }

        $this->_message = $message;

        $body = $this->render($view, $params, $this->layout);

        $this->_message = null;

        if (isset($body)) {
            $message->setBody($body);
        }

        return $message;
    }

    /**
     * Creates a new message instance.
     * The newly created instance will be initialized with the configuration specified by [[messageConfig]].
     * If the configuration does not specify a 'class', the [[messageClass]] will be used as the class
     * of the new message instance.
     * @return MessageInterface message instance.
     * @throws InvalidConfigException
     */
    protected function createMessage()
    {
        $config = $this->messageConfig;
        if (!array_key_exists('class', $config)) {
            $config['class'] = $this->messageClass;
        }
        $config['provider'] = $this;

        return Yii::createObject($config);
    }

    /**
     * Renders the specified view with optional parameters and layout.
     * The view will be rendered using the [[view]] component.
     * @param string $view the view name or the [path alias](guide:concept-aliases) of the view file.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param string|boolean $layout layout view name or [path alias](guide:concept-aliases). If false, no layout will be applied.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function render($view, $params = [], $layout = false)
    {
        $output = $this->getView()->render($view, $params, $this);
        if ($layout !== false) {
            return $this->getView()->render($layout, ['content' => $output, 'message' => $this->_message], $this);
        }

        return $output;
    }

    /**
     * @return View view instance.
     * @throws InvalidConfigException
     */
    public function getView()
    {
        if (!is_object($this->_view)) {
            $this->_view = $this->createView($this->_view);
        }

        return $this->_view;
    }

    /**
     * @param array|View $view view instance or its array configuration that will be used to
     * render message bodies.
     * @throws InvalidConfigException on invalid argument.
     */
    public function setView($view)
    {
        if (!is_array($view) && !is_object($view)) {
            throw new InvalidConfigException('"' . get_class($this) . '::view" should be either object or configuration array, "' . gettype($view) . '" given.');
        }
        $this->_view = $view;
    }

    /**
     * Creates view instance from given configuration.
     * @param array $config view configuration.
     * @return View view instance.
     * @throws InvalidConfigException
     */
    protected function createView(array $config)
    {
        if (!array_key_exists('class', $config)) {
            $config['class'] = View::class;
        }

        return Yii::createObject($config);
    }

    /**
     * Sends multiple messages at once.
     *
     * The default implementation simply calls [[send()]] multiple times.
     * Child classes may override this method to implement more efficient way of
     * sending multiple messages.
     *
     * @param array $messages list of email messages, which should be sent.
     * @return integer number of messages that are successfully sent.
     */
    public function sendMultiple(array $messages)
    {
        $successCount = 0;
        foreach ($messages as $message) {
            if ($this->send($message)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * {@inheritDoc}
     */
    public function send(MessageInterface $message)
    {
        if (!$this->beforeSend($message)) {
            return false;
        }

        $address = $message->getTo();
        if (is_array($address)) {
            $address = implode(', ', array_keys($address));
        }
        Yii::info('Sending email "' . $message->getSubject() . '" to "' . $address . '"', __METHOD__);

        if ($this->useFileTransport) {
            $isSuccessful = $this->saveMessage($message);
        } else {
            $isSuccessful = $this->sendMessage($message);
        }
        $this->afterSend($message, $isSuccessful);

        return $isSuccessful;
    }

    /**
     * This method is invoked right before mail send.
     * You may override this method to do last-minute preparation for the message.
     * If you override this method, please make sure you call the parent implementation first.
     * @param MessageInterface $message
     * @return boolean whether to continue sending an email.
     */
    public function beforeSend(MessageInterface $message)
    {
        $event = new SMSEvent(['message' => $message]);
        $this->trigger(self::EVENT_BEFORE_SEND, $event);

        return $event->isValid;
    }

    /**
     * Saves the message as a file under [[fileTransportPath]].
     * @param MessageInterface $message
     * @return boolean whether the message is saved successfully
     */
    protected function saveMessage(MessageInterface $message)
    {
        $path = Yii::getAlias($this->fileTransportPath);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if ($this->fileTransportCallback !== null) {
            $file = $path . '/' . call_user_func($this->fileTransportCallback, $this, $message);
        } else {
            $file = $path . '/' . $this->generateMessageFileName();
        }
        file_put_contents($file, $message->toString());

        return true;
    }

    /**
     * @return string the file name for saving the message when [[useFileTransport]] is true.
     */
    public function generateMessageFileName()
    {
        $time = microtime(true);

        return date('Ymd-His-', $time) . sprintf('%04d', (int)(($time - (int)$time) * 10000)) . '-' . sprintf('%04d', mt_rand(0, 10000)) . '.eml';
    }

    /**
     * Sends the specified message.
     * This method should be implemented by child classes with the actual sms sending logic.
     * @param MessageInterface $message the message to be sent
     * @return boolean whether the message is sent successfully
     */
    abstract protected function sendMessage(MessageInterface $message);

    /**
     * This method is invoked right after mail was send.
     * You may override this method to do some postprocessing or logging based on mail send status.
     * If you override this method, please make sure you call the parent implementation first.
     * @param MessageInterface $message
     * @param boolean $isSuccessful
     */
    public function afterSend(MessageInterface $message, bool $isSuccessful)
    {
        $event = new SMSEvent(['message' => $message, 'isSuccessful' => $isSuccessful]);
        $this->trigger(self::EVENT_AFTER_SEND, $event);
    }

    /**
     * @return string the directory that contains the view files for composing mail messages
     * Defaults to '@app/sms'.
     */
    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->setViewPath('@app/sms');
        }

        return $this->_viewPath;
    }

    /**
     * @param string $path the directory that contains the view files for composing mail messages
     * This can be specified as an absolute path or a [path alias](guide:concept-aliases).
     */
    public function setViewPath(string $path)
    {
        $this->_viewPath = Yii::getAlias($path);
    }
}