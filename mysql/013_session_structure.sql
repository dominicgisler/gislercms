-- Struktur der session-Tabelle

CREATE TABLE `cms__session`
(
  `session_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_client_id` INT UNSIGNED NOT NULL,
  `uuid`         VARCHAR(36)  NOT NULL,
  `ip`           VARCHAR(50)  NOT NULL,
  `platform`     VARCHAR(50)  NOT NULL,
  `browser`      VARCHAR(50)  NOT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_session` PRIMARY KEY (`session_id`),
  CONSTRAINT `uq_session` UNIQUE (`uuid`),
  CONSTRAINT `fk_session_client` FOREIGN KEY (`fk_client_id`) REFERENCES `cms__client` (`client_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);