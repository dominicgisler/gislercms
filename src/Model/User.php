<?php

namespace GislerCMS\Model;

use Exception;
use PDO;

/**
 * Class User
 * @package GislerCMS\Model
 */
class User extends DbModel
{
    /**
     * @var int
     */
    private int $userId;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $firstname;

    /**
     * @var string
     */
    private string $lastname;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var string
     */
    private string $locale;

    /**
     * @var int
     */
    private int $failedLogins;

    /**
     * @var bool
     */
    private bool $locked;

    /**
     * @var string
     */
    private string $resetKey;

    /**
     * @var string
     */
    private string $lastLogin;

    /**
     * @var string
     */
    private string $lastActivity;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     * @var string
     */
    private string $updatedAt;

    /**
     * @var string
     */
    protected static string $table = 'cms__user';

    /**
     * User constructor.
     * @param int $userId
     * @param string $username
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param string $locale
     * @param int $failedLogins
     * @param bool $locked
     * @param string $resetKey
     * @param string $lastLogin
     * @param string $lastActivity
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int    $userId = 0,
        string $username = '',
        string $firstname = '',
        string $lastname = '',
        string $email = '',
        string $password = '',
        string $locale = '',
        int    $failedLogins = 0,
        bool   $locked = false,
        string $resetKey = '',
        string $lastLogin = '',
        string $lastActivity = '',
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
        $this->locale = $locale;
        $this->failedLogins = $failedLogins;
        $this->locked = $locked;
        $this->resetKey = $resetKey;
        $this->lastLogin = $lastLogin;
        $this->lastActivity = $lastActivity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return User[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT * FROM `cms__user`
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        $stmt->execute($args);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (sizeof($users) > 0) {
            foreach ($users as $user) {
                $arr[] = new User(
                    $user->user_id,
                    $user->username,
                    $user->firstname ?: '',
                    $user->lastname ?: '',
                    $user->email,
                    $user->password,
                    $user->locale,
                    $user->failed_logins,
                    $user->locked,
                    $user->reset_key ?: '',
                    $user->last_login ?: '',
                    $user->last_activity ?: '',
                    $user->created_at,
                    $user->updated_at
                );
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return User
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): User
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new User();
    }

    /**
     * @param int $id
     * @return User
     * @throws Exception
     */
    public static function get(int $id): User
    {
        return self::getObjectWhere('`user_id` = ? ', [$id]);
    }

    /**
     * @param string $username
     * @param string $where
     * @param array $args
     * @return User
     * @throws Exception
     */
    public static function getByUsername(string $username, string $where = '', array $args = []): User
    {
        return self::getObjectWhere(
            '`username` = ? ' . (!empty($where) ? 'AND ' . $where : ''),
            array_merge([$username], $args)
        );
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return User|null
     * @throws Exception
     */
    public function save(): ?User
    {
        $pdo = self::getPDO();
        if ($this->getUserId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__user`
                SET `username` = ?, `firstname` = ?, `lastname` = ?, `email` = ?, `password` = ?, `locale` = ?,
                `failed_logins` = ?, `locked` = ?, `reset_key` = ?, `last_login` = ?, `last_activity` = ?
                WHERE `user_id` = ?
            ");
            $res = $stmt->execute([
                $this->getUsername(),
                $this->getFirstname(),
                $this->getLastname(),
                $this->getEmail(),
                $this->getPassword(),
                $this->getLocale(),
                $this->getFailedLogins(),
                $this->isLocked() ? 1 : 0,
                $this->getResetKey() ?: null,
                $this->getLastLogin() ?: null,
                $this->getLastActivity() ?: null,
                $this->getUserId()
            ]);
            return $res ? self::get($this->getUserId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__user` (
                    `username`, `firstname`, `lastname`, `email`, `password`, `locale`,
                    `failed_logins`, `locked`, `reset_key`, `last_login`, `last_activity`
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getUsername(),
                $this->getFirstname(),
                $this->getLastname(),
                $this->getEmail(),
                $this->getPassword(),
                $this->getLocale(),
                $this->getFailedLogins(),
                $this->isLocked() ? 1 : 0,
                $this->getResetKey() ?: null,
                $this->getLastLogin() ?: null,
                $this->getLastActivity() ?: null
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        $pdo = self::getPDO();
        if ($this->getUserId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__user`
                WHERE `user_id` = ?
            ");
            return $stmt->execute([$this->getUserId()]);
        }
        return false;
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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLastLogin(): string
    {
        return $this->lastLogin;
    }

    /**
     * @param string $lastLogin
     */
    public function setLastLogin(string $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return string
     */
    public function getLastActivity(): string
    {
        return $this->lastActivity;
    }

    /**
     * @param string $lastActivity
     */
    public function setLastActivity(string $lastActivity): void
    {
        $this->lastActivity = $lastActivity;
    }
}
