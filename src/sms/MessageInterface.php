<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\sms;

/**
 * MessageInterface is the interface that should be implemented by sms message classes.
 *
 * A message represents the settings and content of an sms, such as the sender, recipient,
 * subject, body, etc.
 *
 * Messages are sent by a [[\tonic\hq\re\sms\ProviderInterface|mailer]], like the following,
 *
 * ```php
 * Yii::$app->smsProvider->compose()
 *     ->setFrom('+41 79 123 45 67')
 *     ->setTo($form->mobile)
 *     ->setSubject($form->subject)
 *     ->setBody('Plain text content')
 *     ->send();
 * ```
 *
 * @see ProviderInterface
 *
 * @author Simon Karlen <karlen@tonic.ag>
 */
interface MessageInterface
{
    /**
     * Returns the region of this message.
     * @return string the region of this message.
     */
    public function getRegion(): string;

    /**
     * Sets the region of this message (e.g. 'CH' for Switzerland). This is important for number parsing.
     * @param string $region region name.
     * @return $this self reference.
     */
    public function setRegion(string $region): MessageInterface;

    /**
     * Returns the message sender.
     * @return string|array the sender
     */
    public function getFrom();

    /**
     * Sets the message sender.
     * @param string|null $from sender phone number.
     * @return $this self reference.
     */
    public function setFrom(string $from = null): MessageInterface;

    /**
     * Returns the message recipient(s).
     * @return string|array the message recipients
     */
    public function getTo();

    /**
     * Sets the message recipient(s).
     * @param string|array $to receiver mobile number.
     * You may pass an array of numbers if multiple recipients should receive this message.
     * @return $this self reference.
     */
    public function setTo($to): MessageInterface;

    /**
     * Returns the message subject.
     * @return string the message subject
     */
    public function getSubject(): string;

    /**
     * Sets the message subject.
     * @param string $subject message subject
     * @return $this self reference.
     */
    public function setSubject(string $subject): MessageInterface;

    /**
     * Sets message plain text content.
     * @param string $text message plain text content.
     * @return $this self reference.
     */
    public function setBody(string $text): MessageInterface;

    /**
     * Sends this sms message.
     * @param ProviderInterface|null $provider the provider that should be used to send this message.
     * If null, the "smsProvider" application component will be used instead.
     * @return boolean whether this message is sent successfully.
     */
    public function send(ProviderInterface $provider = null): bool;

    /**
     * Returns string representation of this message.
     * @return string the string representation of this message.
     */
    public function toString(): string;
}
