-- Struktur der Modul-Tabelle

DROP TABLE IF EXISTS `cms__module`;

CREATE TABLE `cms__module`
(
  `module_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(128) NOT NULL,
  `enabled`    TINYINT(1)   NOT NULL DEFAULT 1,
  `trash`      TINYINT(1)   NOT NULL DEFAULT 0,
  `controller` VARCHAR(128) NOT NULL,
  `config`     TEXT         NULL     DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_module` PRIMARY KEY (`module_id`),
  CONSTRAINT `uq_module` UNIQUE (`name`)
);