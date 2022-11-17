<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

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
     * @var Redirect
     */
    private $redirect;

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
     * @var string
     */
    protected static $table = 'cms__visit';

    /**
     * Visit constructor.
     * @param int $visitId
     * @param PageTranslation|null $pageTranslation
     * @param string $arguments
     * @param Redirect|null $redirect
     * @param Session|null $session
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int             $visitId = 0,
        PageTranslation $pageTranslation = null,
        string          $arguments = '',
        Redirect        $redirect = null,
        Session         $session = null,
        string          $createdAt = '',
        string          $updatedAt = ''
    )
    {
        $this->visitId = $visitId;
        $this->pageTranslation = $pageTranslation;
        $this->arguments = $arguments;
        $this->redirect = $redirect;
        $this->session = $session;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Visit[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
            
                   `v`.*,
                   `p`.`page_id` AS 'p_page_id',
                   `p`.`name` AS 'p_name',
                   `r`.`redirect_id` AS 'r_redirect_id',
                   `r`.`name` AS 'r_name',
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
            
            INNER JOIN `cms__session` `s`
            ON `v`.`fk_session_id` = `s`.`session_id`
                
            LEFT JOIN `cms__page_translation` `pt`
            ON `v`.`fk_page_translation_id` = `pt`.`page_translation_id`
            
            LEFT JOIN `cms__page` `p`
            ON `pt`.`fk_page_id` = `p`.`page_id`
            
            LEFT JOIN `cms__language` `l`
            ON `pt`.`fk_language_id` = `l`.`language_id`
                
            LEFT JOIN `cms__redirect` `r`
            ON `v`.`fk_redirect_id` = `r`.`redirect_id`
                
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $visits = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($visits) > 0) {
                foreach ($visits as $visit) {
                    $arr[] = new Visit(
                        $visit->visit_id,
                        new PageTranslation(
                            $visit->fk_page_translation_id ?: 0,
                            new Page(
                                $visit->p_page_id ?: 0,
                                $visit->p_name ?: ''
                            ),
                            new Language(
                                $visit->l_language_id ?: 0,
                                $visit->l_locale ?: '',
                                $visit->l_description ?: ''
                            )
                        ),
                        $visit->arguments,
                        new Redirect(
                            $visit->r_redirect_id ?: 0,
                            $visit->r_name ?: ''
                        ),
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
     * @throws Exception
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
     * @throws Exception
     */
    public static function get(int $id): Visit
    {
        return self::getObjectWhere('`visit_id` = ?', [$id]);
    }

    /**
     * @return Visit[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Visit|null
     * @throws Exception
     */
    public function save(): ?Visit
    {
        $pdo = self::getPDO();
        if ($this->getVisitId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__visit`
                SET `fk_page_translation_id` = ?, `arguments` = ?, `fk_redirect_id` = ?, `fk_session_id` = ?
                WHERE `visit_id` = ?
            ");
            $res = $stmt->execute([
                $this->getPageTranslation()->getPageTranslationId() ?: null,
                $this->getArguments(),
                $this->getRedirect()->getRedirectId() ?: null,
                $this->getSession()->getSessionId(),
                $this->getVisitId()
            ]);
            return $res ? self::get($this->getVisitId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__visit` (`fk_page_translation_id`, `arguments`, `fk_redirect_id`, `fk_session_id`)
                VALUES (?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getPageTranslation()->getPageTranslationId() ?: null,
                $this->getArguments(),
                $this->getRedirect()->getRedirectId() ?: null,
                $this->getSession()->getSessionId()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @param string $format
     * @return array
     * @throws Exception
     */
    public static function countByTimestamp(string $format): array
    {
        $stmt = self::getPDO()->prepare("SELECT DATE_FORMAT(created_at, ?), COUNT(*) FROM cms__visit GROUP BY DATE_FORMAT(created_at, ?) ORDER BY created_at DESC LIMIT 31");
        $stmt->execute([$format, $format]);
        $arr = [];
        foreach ($stmt->fetchAll() as $row) {
            $arr[$row[0]] = $row[1];
        }
        return $arr;
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getPageVisits(): array
    {
        $stmt = self::getPDO()->query("
            SELECT
                
                p.page_id AS page_id,
                p.name AS page_name,
                l.description AS language,
                v.arguments AS arguments,
                COUNT(*) AS visits
            
            FROM cms__visit v
            
            INNER JOIN cms__page_translation pt
            ON pt.page_translation_id = v.fk_page_translation_id
            
            INNER JOIN cms__page p
            ON p.page_id = pt.fk_page_id
            
            INNER JOIN cms__language l
            ON l.language_id = pt.fk_language_id
            
            GROUP BY CONCAT(fk_page_translation_id, arguments);
        ");
        $arr = [];
        foreach ($stmt->fetchAll() as $row) {
            $arr[] = [
                'page_id' => $row['page_id'],
                'page_name' => $row['page_name'],
                'language' => $row['language'],
                'arguments' => $row['arguments'],
                'visits' => $row['visits']
            ];
        }
        return $arr;
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
     * @return Redirect
     */
    public function getRedirect(): Redirect
    {
        return $this->redirect;
    }

    /**
     * @param Redirect $redirect
     */
    public function setRedirect(Redirect $redirect): void
    {
        $this->redirect = $redirect;
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
