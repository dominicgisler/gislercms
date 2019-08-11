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
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

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
     * @param string $createdAt
     * @param string $updatedAt
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
        string $resetKey = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->failedLogins = $failedLogins;
        $this->locked = $locked;
        $this->resetKey = $resetKey;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $username
     * @return User
     * @throws \Exception
     */
    public static function getByUsername(string $username): User
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
                $user->reset_key ?: '',
                $user->created_at,
                $user->updated_at
            );
        }
        return new User();
    }

    /**
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public static function create(User $user): User
    {
        $sql = "INSERT INTO `cms__user` (`username`, `firstname`, `lastname`, `email`, `password`) VALUES (?, ?, ?, ?, ?)";
        $stmt = self::getPDO()->prepare($sql);
        $res = $stmt->execute([
            $user->getUsername(),
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getPassword()
        ]);
        if ($res === false) {
            $err = $stmt->errorInfo();
            if ($err[0] !== '00000' && $err[0] !== '01000') {
                return new User();
            }
        }
        return self::getByUsername($user->getUsername());
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
    public function setUserId(int $userId): void
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
    public function setUsername(string $username): void
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
    public function setFirstname(string $firstname): void
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
    public function setLastname(string $lastname): void
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
    public function setEmail(string $email): void
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
    public function setPassword(string $password): void
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
    public function setFailedLogins(int $failedLogins): void
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
    public function setLocked(bool $locked): void
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
    public function setResetKey(string $resetKey): void
    {
        $this->resetKey = $resetKey;
    }

    public function getDisplayName(): string
    {
        if (!empty($this->firstname) && !empty($this->lastname)) {
            return $this->firstname . ' ' . $this->lastname;
        } elseif (!empty($this->firstname)) {
            return $this->firstname;
        } elseif (!empty($this->lastname)) {
            return $this->lastname;
        } else {
            return $this->username;
        }
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

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
