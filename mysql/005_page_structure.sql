-- Struktur der Seiten-Tabellen

CREATE TABLE `cms__page`
(
  `page_id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(128) NOT NULL,
  `enabled`        TINYINT(1)   NOT NULL DEFAULT 1,
  `trash`          TINYINT(1)   NOT NULL DEFAULT 0,
  `fk_language_id` INT UNSIGNED NOT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_page` PRIMARY KEY (`page_id`),
  CONSTRAINT `fk_page_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE `cms__page_translation`
(
  `page_translation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_page_id`          INT UNSIGNED NOT NULL,
  `fk_language_id`      INT UNSIGNED NOT NULL,
  `name`                VARCHAR(128) NOT NULL,
  `title`               VARCHAR(128) NULL     DEFAULT NULL,
  `content`             LONGTEXT     NULL     DEFAULT NULL,
  `meta_keywords`       VARCHAR(512) NULL     DEFAULT NULL,
  `meta_description`    VARCHAR(512) NULL     DEFAULT NULL,
  `meta_author`         VARCHAR(255) NULL     DEFAULT NULL,
  `meta_copyright`      VARCHAR(255) NULL     DEFAULT NULL,
  `meta_image`          VARCHAR(255) NULL     DEFAULT NULL,
  `enabled`             TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_page_translation` PRIMARY KEY (`page_translation_id`),
  CONSTRAINT `uq1_page_translation` UNIQUE (`fk_page_id`, `fk_language_id`),
  CONSTRAINT `uq2_page_translation` UNIQUE (`name`, `fk_language_id`),
  CONSTRAINT `fk_page_translation_page` FOREIGN KEY (`fk_page_id`) REFERENCES `cms__page` (`page_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_page_translation_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE `cms__page_translation_history`
(
  `page_translation_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_page_translation_id`      INT UNSIGNED NOT NULL,
  `name`                        VARCHAR(128) NOT NULL,
  `title`                       VARCHAR(128) NULL     DEFAULT NULL,
  `content`                     LONGTEXT     NULL     DEFAULT NULL,
  `meta_keywords`               VARCHAR(512) NULL     DEFAULT NULL,
  `meta_description`            VARCHAR(512) NULL     DEFAULT NULL,
  `meta_author`                 VARCHAR(255) NULL     DEFAULT NULL,
  `meta_copyright`              VARCHAR(255) NULL     DEFAULT NULL,
  `meta_image`                  VARCHAR(255) NULL     DEFAULT NULL,
  `enabled`                     TINYINT(1)   NOT NULL DEFAULT 0,
  `fk_user_id`                  INT UNSIGNED NULL     DEFAULT NULL,
  `created_at`                  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_page_translation_history` PRIMARY KEY (`page_translation_history_id`),
  CONSTRAINT `fk_page_translation_history_page_translation` FOREIGN KEY (`fk_page_translation_id`) REFERENCES `cms__page_translation` (`page_translation_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_page_translation_history_user` FOREIGN KEY (`fk_user_id`) REFERENCES `cms__user` (`user_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);