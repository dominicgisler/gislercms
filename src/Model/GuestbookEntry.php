<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class GuestbookEntry
 * @package GislerCMS\Model
 */
class GuestbookEntry extends DbModel
{
    /**
     * @var int
     */
    private $guestbookEntryId;

    /**
     * @var string
     */
    private $guestbookIdentifier;

    /**
     * @var string
     */
    private $input;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * @var string
     */
    protected static $table = 'cms__guestbook_entry';

    /**
     * Client constructor.
     * @param int $guestbookEntryId
     * @param string $guestbookIdentifer
     * @param string $input
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int    $guestbookEntryId = 0,
        string $guestbookIdentifer = '',
        string $input = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->guestbookEntryId = $guestbookEntryId;
        $this->input = $input;
        $this->guestbookIdentifier = $guestbookIdentifer;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return GuestbookEntry[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__guestbook_entry` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $entries = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($entries) > 0) {
                foreach ($entries as $entry) {
                    $arr[] = new GuestbookEntry(
                        $entry->guestbook_entry_id,
                        $entry->guestbook_identifier,
                        $entry->input,
                        $entry->created_at,
                        $entry->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return GuestbookEntry
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): GuestbookEntry
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new GuestbookEntry();
    }

    /**
     * @param int $id
     * @return GuestbookEntry
     * @throws Exception
     */
    public static function get(int $id): GuestbookEntry
    {
        return self::getObjectWhere('`guestbook_entry_id` = ?', [$id]);
    }

    /**
     * @param string $identifier
     * @return array
     * @throws Exception
     */
    public static function getGuestbookEntries(string $identifier): array
    {
        return self::getWhere('`guestbook_identifier` = ?', [$identifier]);
    }

    /**
     * @return GuestbookEntry[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return GuestbookEntry|null
     * @throws Exception
     */
    public function save(): ?GuestbookEntry
    {
        $pdo = self::getPDO();
        if ($this->getGuestbookEntryId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__guestbook_entry`
                SET `guestbook_identifier` = ?, `input` = ?, `updated_at` = CURRENT_TIMESTAMP()
                WHERE `guestbook_entry_id` = ?
            ");
            $res = $stmt->execute([
                $this->getGuestbookIdentifier(),
                $this->getInput(),
                $this->getGuestbookEntryId()
            ]);
            return $res ? self::get($this->getGuestbookEntryId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__guestbook_entry` (`guestbook_identifier`, `input`)
                VALUES (?, ?)
            ");
            $res = $stmt->execute([
                $this->getGuestbookIdentifier(),
                $this->getInput()
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
        if ($this->getGuestbookEntryId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__guestbook_entry`
                WHERE `guestbook_entry_id` = ?
            ");
            return $stmt->execute([$this->getGuestbookEntryId()]);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getGuestbookEntryId(): int
    {
        return $this->guestbookEntryId;
    }

    /**
     * @param int $guestbookEntryId
     */
    public function setGuestbookEntryId(int $guestbookEntryId): void
    {
        $this->guestbookEntryId = $guestbookEntryId;
    }

    /**
     * @return string
     */
    public function getGuestbookIdentifier(): string
    {
        return $this->guestbookIdentifier;
    }

    /**
     * @param string $guestbookIdentifier
     */
    public function setGuestbookIdentifier(string $guestbookIdentifier): void
    {
        $this->guestbookIdentifier = $guestbookIdentifier;
    }

    /**
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * @param string $input
     */
    public function setInput(string $input): void
    {
        $this->input = $input;
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
