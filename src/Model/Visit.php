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
        $stmt = self::getPDO()->prepare("
            SELECT
            
                   `v`.*,
                   `p`.`page_id` AS 'p_page_id',
                   `p`.`name` AS 'p_name',
                   `l`.`language_id` AS 'l_language_id',
                   `l`.`locale` AS 'l_locale',
                   `l`.`description` AS 'l_description',
                   `s`.`session_id` AS 's_session_id',
                   `s`.`fk_client_id` AS 's_fk_client_id',
                   `s`.`uuid` AS 's_uuid',
                   `s`.`ip` AS 's_ip',
                   `s`.`platform` AS 's_platform',
                   `s`.`browser` AS 's_browser',
                   `s`.`user_agent` AS 's_user_agent',
                   `s`.`created_at` AS 's_created_at',
                   `s`.`updated_at` AS 's_updated_at'
            
            FROM `cms__visit` `v`
            
            INNER JOIN `cms__page_translation` `pt`
            ON `v`.`fk_page_translation_id` = `pt`.`page_translation_id`
            
            INNER JOIN `cms__page` `p`
            ON `pt`.`fk_page_id` = `p`.`page_id`
            
            INNER JOIN `cms__language` `l`
            ON `pt`.`fk_language_id` = `l`.`language_id`
            
            INNER JOIN `cms__session` `s`
            ON `v`.`fk_session_id` = `s`.`session_id`
                
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        if ($stmt instanceof \PDOStatement) {
            $stmt->execute($args);
            $visits = $stmt->fetchAll(\PDO::FETCH_OBJ);
            if (sizeof($visits) > 0) {
                foreach ($visits as $visit) {
                    $arr[] = new Visit(
                        $visit->visit_id,
                        new PageTranslation(
                            $visit->fk_page_translation_id,
                            new Page(
                                $visit->p_page_id,
                                $visit->p_name
                            ),
                            new Language(
                                $visit->l_language_id,
                                $visit->l_locale,
                                $visit->l_description
                            )
                        ),
                        $visit->arguments,
                        new Session(
                            $visit->s_session_id,
                            new Client($visit->s_fk_client_id),
                            $visit->s_uuid,
                            $visit->s_ip,
                            $visit->s_platform,
                            $visit->s_browser,
                            $visit->s_user_agent,
                            $visit->s_created_at,
                            $visit->s_updated_at,
                        ),
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
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Visit();
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
