-- Struktur der Config-Tabelle

CREATE TABLE `cms__config`
(
  `config_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `section`    VARCHAR(128) NOT NULL,
  `name`       VARCHAR(128) NOT NULL,
  `type`       VARCHAR(128) NOT NULL,
  `value`      TEXT         NULL     DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_config` PRIMARY KEY (`config_id`),
  CONSTRAINT `uq_config` UNIQUE (`section`, `name`)
);