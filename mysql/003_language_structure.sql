-- Struktur der Sprachen-Tabelle

CREATE TABLE `cms__language`
(
  `language_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `locale`      VARCHAR(2)  NOT NULL,
  `description` VARCHAR(128) NOT NULL,
  `enabled`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_language` PRIMARY KEY (`language_id`),
  CONSTRAINT `uq_language` UNIQUE (`locale`),
);