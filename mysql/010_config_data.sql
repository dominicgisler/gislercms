-- Einfügen der Konfigurationen

INSERT INTO `cms__config` (`section`, `name`, `type`, `value`)
VALUES ('global', 'maintenance_mode', 'boolean', '0'),
       ('page', 'meta_keywords', 'string', 'cms, gisler, software, gislercms'),
       ('page', 'meta_description', 'string', 'Meine eigene Webseite mit GislerCMS'),
       ('page', 'meta_author', 'string', 'Max Muster'),
       ('page', 'meta_copyright', 'string', 'Max Muster, domain.tld'),
       ('page', 'default_language', 'integer', '1'),
       ('global', 'default_page', 'integer', '1'),
       ('global', 'admin_route', 'string', '/admin'),
       ('global', 'max_failed_logins', 'integer', '5');