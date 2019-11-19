-- Struktur der Beitragstabellen

CREATE TABLE `cms__post`
(
  `post_id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(128)  NOT NULL,
  `enabled`        TINYINT(1)    NOT NULL DEFAULT 1,
  `trash`          TINYINT(1)    NOT NULL DEFAULT 0,
  `fk_language_id` INT UNSIGNED  NOT NULL,
  `categories`     VARCHAR(1024) NOT NULL,
  `publish_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_post` PRIMARY KEY (`post_id`),
  CONSTRAINT `fk_post_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE `cms__post_translation`
(
  `post_translation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_post_id`          INT UNSIGNED NOT NULL,
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

  CONSTRAINT `pk_post_translation` PRIMARY KEY (`post_translation_id`),
  CONSTRAINT `uq1_post_translation` UNIQUE (`fk_post_id`, `fk_language_id`),
  CONSTRAINT `uq2_post_translation` UNIQUE (`name`, `fk_language_id`),
  CONSTRAINT `fk_post_translation_post` FOREIGN KEY (`fk_post_id`) REFERENCES `cms__post` (`post_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_post_translation_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE `cms__post_attribute` (
  `post_attribute_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_post_id`        INT UNSIGNED NOT NULL,
  `name`              VARCHAR(128) NOT NULL,
  `value`             TEXT         NOT NULL,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_post_attribute` PRIMARY KEY (`post_attribute_id`),
  CONSTRAINT `fk_post_attribute_post` FOREIGN KEY (`fk_post_id`) REFERENCES `cms__post` (`post_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `uq_post_attribute` UNIQUE (`fk_post_id`, `name`)
);