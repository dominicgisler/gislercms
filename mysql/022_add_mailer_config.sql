-- Einfügen der Konfiguration für den Mailer

INSERT INTO `cms__config` (`section`, `name`, `type`, `value`)
VALUES ('mailer', 'smtp', 'boolean', '0'),
       ('mailer', 'host', 'string', 'mail.example.com'),
       ('mailer', 'smtpauth', 'boolean', '1'),
       ('mailer', 'username', 'string', 'info@example.com'),
       ('mailer', 'password', 'string', 'mypass'),
       ('mailer', 'smtpsecure', 'string', 'ssl'),
       ('mailer', 'port', 'integer', '465'),
       ('mailer', 'default_name', 'string', 'GislerCMS'),
       ('mailer', 'default_email', 'string', 'info@example.com');