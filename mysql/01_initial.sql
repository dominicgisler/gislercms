DROP TABLE IF EXISTS `cms__widget_translation_history`;
DROP TABLE IF EXISTS `cms__widget_translation`;
DROP TABLE IF EXISTS `cms__widget`;
DROP TABLE IF EXISTS `cms__page_translation_history`;
DROP TABLE IF EXISTS `cms__page_translation`;
DROP TABLE IF EXISTS `cms__page`;
DROP TABLE IF EXISTS `cms__user`;
DROP TABLE IF EXISTS `cms__language`;
DROP TABLE IF EXISTS `cms__config`;
DROP TABLE IF EXISTS `cms__migration`;

CREATE TABLE `cms__migration`
(
  `migration`  VARCHAR(128) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT `pk_migration` PRIMARY KEY (`migration`)
);

CREATE TABLE `cms__config`
(
  `config_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(128) NOT NULL,
  `value`      TEXT         NULL     DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_config` PRIMARY KEY (`config_id`)
);

CREATE TABLE `cms__language`
(
  `language_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `locale`      VARCHAR(10)  NOT NULL,
  `description` VARCHAR(128) NOT NULL,
  `enabled`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_language` PRIMARY KEY (`language_id`)
);

CREATE TABLE `cms__user`
(
  `user_id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(128) NOT NULL,
  `firstname`     VARCHAR(128) NULL     DEFAULT NULL,
  `lastname`      VARCHAR(128) NULL     DEFAULT NULL,
  `email`         VARCHAR(255) NOT NULL,
  `password`      VARCHAR(255) NOT NULL,
  `failed_logins` INT          NOT NULL DEFAULT 0,
  `locked`        TINYINT(1)   NOT NULL DEFAULT 0,
  `reset_key`     VARCHAR(128) NULL     DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_user` PRIMARY KEY (`user_id`)
);

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
  CONSTRAINT `uq_page_translation` UNIQUE (`fk_page_id`, `fk_language_id`),
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

CREATE TABLE `cms__widget`
(
  `widget_id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(128) NOT NULL,
  `enabled`        TINYINT(1)   NOT NULL DEFAULT 1,
  `trash`          TINYINT(1)   NOT NULL DEFAULT 0,
  `fk_language_id` INT UNSIGNED NOT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_widget` PRIMARY KEY (`widget_id`),
  CONSTRAINT `fk_widget_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE `cms__widget_translation`
(
  `widget_translation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_widget_id`          INT UNSIGNED NOT NULL,
  `fk_language_id`        INT UNSIGNED NOT NULL,
  `content`               LONGTEXT     NULL     DEFAULT NULL,
  `enabled`               TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_widget_translation` PRIMARY KEY (`widget_translation_id`),
  CONSTRAINT `uq_widget_translation` UNIQUE (`fk_widget_id`, `fk_language_id`),
  CONSTRAINT `fk_widget_translation_page` FOREIGN KEY (`fk_widget_id`) REFERENCES `cms__widget` (`widget_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_widget_translation_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE `cms__widget_translation_history`
(
  `widget_translation_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_widget_translation_id`      INT UNSIGNED NOT NULL,
  `content`                       LONGTEXT     NULL     DEFAULT NULL,
  `enabled`                       TINYINT(1)   NOT NULL DEFAULT 0,
  `fk_user_id`                    INT UNSIGNED NULL     DEFAULT NULL,
  `created_at`                    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_widget_translation_history` PRIMARY KEY (`widget_translation_history_id`),
  CONSTRAINT `fk_widget_translation_history_widget_translation` FOREIGN KEY (`fk_widget_translation_id`) REFERENCES `cms__widget_translation` (`widget_translation_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_widget_translation_history_user` FOREIGN KEY (`fk_user_id`) REFERENCES `cms__user` (`user_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);