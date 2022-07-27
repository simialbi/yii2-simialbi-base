<?php
/**
 * @package yii2-simialbi-base
 * @author Simon Karlen <simi.albi@outlook.com>
 * @copyright Copyright © 2019 Simon Karlen
 */

namespace simialbi\yii2\models;

use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * UserInterface extents IdentityInterface and is the interface that should be implemented by
 * a class providing user information.
 *
 * This interface can typically be implemented by a user model class. For example, the following
 * code shows how to implement this interface by a User ActiveRecord class:
 *
 * ```php
 * class User extends ActiveRecord implements UserInterface
 * {
 *     public static function findIdentity($id)
 *     {
 *         return static::findOne($id);
 *     }
 *
 *     public static function findIdentityByAccessToken($token, $type = null)
 *     {
 *         return static::findOne(['access_token' => $token]);
 *     }
 *
 *     public function getImage()
 *     {
 *         return $this->image;
 *     }
 *
 *     public function getName()
 *     {
 *         return $this->name;
 *     }
 *
 *     public function getEmail()
 *     {
 *         return $this->email;
 *     }
 *
 *     public function getMobile()
 *     {
 *         return $this->mobile;
 *     }
 *
 *     public function getId()
 *     {
 *         return $this->id;
 *     }
 *
 *     public function getAuthKey()
 *     {
 *         return $this->authKey;
 *     }
 *
 *     public function validateAuthKey($authKey)
 *     {
 *         return $this->authKey === $authKey;
 *     }
 * }
 * ```
 *
 * @property-read string|null $name
 * @property-read string|null $image
 * @property-read string $email
 * @property-read string|null $mobile
 *
 * @author Simon Karlen <simi.albi@outlook.com>
 * @since 0.6.0
 */
interface UserInterface extends IdentityInterface
{
    /**
     * Returns a list of users that can be used in assignments
     *
     * @param array|null $filters Additional filters to apply to the query.
     *
     * @return static
     * @see ActiveQuery::where()
     */
    public static function findIdentities(?array $filters): UserInterface;

    /**
     * Returns a users profile image.
     * @return string|null A users profile image.
     */
    public function getImage(): ?string;

    /**
     * Returns a users name (first and last name or username).
     * @return string|null A users name.
     */
    public function getName(): ?string;

    /**
     * Returns a users email address
     * @retun string|null
     * @since 0.8.0
     */
    public function getEmail(): ?string;

    /**
     * Returns a users mobile phone number (international format e.g. +41791234567)
     * @return string|null
     * @since 0.10.1
     */
    public function getMobile(): ?string;
}
