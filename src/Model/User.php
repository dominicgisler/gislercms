<?php

namespace GislerCMS\Model;

/**
 * Class User
 * @package GislerCMS\Model
 */
class User extends DbModel
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $failedLogins;

    /**
     * @var bool
     */
    private $locked;

    /**
     * @var string
     */
    private $resetKey;

    /**
     * User constructor.
     * @param int $userId
     * @param string $username
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param int $failedLogins
     * @param bool $locked
     * @param string $resetKey
     */
    public function __construct(
        int $userId = 0,
        string $username = '',
        string $firstname = '',
        string $lastname = '',
        string $email = '',
        string $password = '',
        int $failedLogins = 0,
        bool $locked = false,
        string $resetKey = ''
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->failedLogins = $failedLogins;
        $this->locked = $locked;
        $this->resetKey = $resetKey;
    }

    /**
     * @param string $username
     * @return User
     * @throws \Exception
     */
    public static function getUser(string $username): User
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__user` WHERE `username` = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetchObject();
        if ($user) {
            return new User(
                $user->user_id,
                $user->username,
                $user->firstname ?: '',
                $user->lastname ?: '',
                $user->email,
                $user->password,
                $user->failed_logins,
                $user->locked,
                $user->reset_key ?: ''
            );
        }
        return new User();
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getFailedLogins(): int
    {
        return $this->failedLogins;
    }

    /**
     * @param int $failedLogins
     */
    public function setFailedLogins(int $failedLogins)
    {
        $this->failedLogins = $failedLogins;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return string
     */
    public function getResetKey(): string
    {
        return $this->resetKey;
    }

    /**
     * @param string $resetKey
     */
    public function setResetKey(string $resetKey)
    {
        $this->resetKey = $resetKey;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isEqual(User $user): bool
    {
        return
            $this->userId === $user->userId &&
            $this->username === $user->username &&
            $this->password === $user->password &&
            $this->locked === $user->locked;
    }
}