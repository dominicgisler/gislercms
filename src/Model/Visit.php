<?php

namespace GislerCMS\Model;

/**
 * Class Visit
 * @package GislerCMS\Model
 */
class Visit extends DbModel
{
    /**
     * @var int
     */
    private $visitId;

    /**
     * @var PageTranslation
     */
    private $pageTranslation;

    /**
     * @var string
     */
    private $arguments;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Visit constructor.
     * @param int $visitId
     * @param PageTranslation|null $pageTranslation
     * @param string $arguments
     * @param Session|null $session
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $visitId = 0,
        PageTranslation $pageTranslation = null,
        string $arguments = '',
        Session $session = null,
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->visitId = $visitId;
        $this->pageTranslation = $pageTranslation;
        $this->arguments = $arguments;
        $this->session = $session;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Visit[]
     * @throws \Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__visit` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof \PDOStatement) {
            $stmt->execute($args);
            $visits = $stmt->fetchAll(\PDO::FETCH_OBJ);
            if (sizeof($visits) > 0) {
                foreach ($visits as $visit) {
                    $arr[] = new Visit(
                        $visit->visit_id,
                        PageTranslation::get($visit->fk_page_translation_id),
                        $visit->arguments,
                        Session::get($visit->fk_session_id),
                        $visit->created_at,
                        $visit->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Visit
     * @throws \Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Visit
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__visit` " . (!empty($where) ? 'WHERE ' . $where : ''));
        $stmt->execute($args);
        $visit = $stmt->fetchObject();
        if ($visit) {
            return new Visit(
                $visit->visit_id,
                PageTranslation::get($visit->fk_page_translation_id),
                $visit->arguments,
                Session::get($visit->fk_session_id),
                $visit->created_at,
                $visit->updated_at
            );
        }
        return new Visit();
    }

    /**
     * @return Visit|null
     * @throws \Exception
     */
    public function save(): ?Visit
    {
        $pdo = self::getPDO();
        if ($this->getVisitId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__visit`
                SET `fk_page_translation_id` = ?, `arguments` = ?, `fk_session_id` = ?
                WHERE `visit_id` = ?
            ");
            $res = $stmt->execute([
                $this->getPageTranslation()->getPageTranslationId(),
                $this->getArguments(),
                $this->getSession()->getSessionId(),
                $this->getVisitId()
            ]);
            return $res ? self::get($this->getVisitId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__visit` (`fk_page_translation_id`, `arguments`, `fk_session_id`)
                VALUES (?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getPageTranslation()->getPageTranslationId(),
                $this->getArguments(),
                $this->getSession()->getSessionId()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @param int $id
     * @return Visit
     * @throws \Exception
     */
    public static function get(int $id): Visit
    {
        return self::getObjectWhere('`visit_id` = ?', [$id]);
    }

    /**
     * @return Visit[]
     * @throws \Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return int
     */
    public function getVisitId(): int
    {
        return $this->visitId;
    }

    /**
     * @param int $visitId
     */
    public function setVisitId(int $visitId): void
    {
        $this->visitId = $visitId;
    }

    /**
     * @return PageTranslation
     */
    public function getPageTranslation(): PageTranslation
    {
        return $this->pageTranslation;
    }

    /**
     * @param PageTranslation $pageTranslation
     */
    public function setPageTranslation(PageTranslation $pageTranslation): void
    {
        $this->pageTranslation = $pageTranslation;
    }

    /**
     * @return string
     */
    public function getArguments(): string
    {
        return $this->arguments;
    }

    /**
     * @param string $arguments
     */
    public function setArguments(string $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
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
