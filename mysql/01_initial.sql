CREATE TABLE `cms__config` (
  `config_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(128) NOT NULL,
  `value`      TEXT         NULL     DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_config` PRIMARY KEY (`config_id`)
);

CREATE TABLE `cms__language` (
  `language_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `locale`      VARCHAR(10)  NOT NULL,
  `description` VARCHAR(128) NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_language` PRIMARY KEY (`language_id`)
);

CREATE TABLE `cms__page` (
  `page_id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(128) NOT NULL,
  `enabled`        TINYINT(1)   NOT NULL,
  `fk_language_id` INT UNSIGNED NOT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_page` PRIMARY KEY (`page_id`),
  CONSTRAINT `fk_page_language` FOREIGN KEY (`fk_language_id`) REFERENCES `cms__language` (`language_id`)
);

CREATE TABLE `cms__user` (
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