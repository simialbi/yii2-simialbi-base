<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\sms;

/**
 * ProviderInterface is the interface that should be implemented by Provider classes.
 *
 * A provider should mainly support creating and sending [[MessageInterface|sms messages]]. It should
 * also support composition of the message body through the view rendering mechanism. For example,
 *
 * ```php
 * Yii::$app->smsProvider->compose('my-message', ['contactForm' => $form])
 *     ->setFrom('+41 79 123 45 67')
 *     ->setTo($form->mobile)
 *     ->setSubject($form->subject)
 *     ->send();
 * ```
 *
 * @see MessageInterface
 *
 * @author Simon Karlen <karlen@tonic.ag>
 */
interface ProviderInterface
{
    /**
     * Creates a new message instance and optionally composes its body content via view rendering.
     *
     * @param string|null $view the view to be used for rendering the message body. This can be:
     *
     * - a string, which represents the view name or [path alias](guide:concept-aliases) for rendering the body of the sms.
     * - null, meaning the message instance will be returned without body content.
     *
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return MessageInterface message instance.
     */
    public function compose(?string $view = null, array $params = []): MessageInterface;

    /**
     * Sends the given sms message.
     * @param MessageInterface $message sms message instance to be sent
     * @return boolean whether the message has been sent successfully
     */
    public function send(MessageInterface $message): bool;

    /**
     * Sends multiple messages at once.
     *
     * This method may be implemented by some providers which support more efficient way of sending multiple messages in the same batch.
     *
     * @param array $messages list of sms messages, which should be sent.
     * @return integer number of messages that are successfully sent.
     */
    public function sendMultiple(array $messages): int;
}
