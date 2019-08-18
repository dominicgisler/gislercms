-- Struktur der User-Tabelle

CREATE TABLE `cms__user`
(
  `user_id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(128) NOT NULL,
  `firstname`     VARCHAR(128) NULL     DEFAULT NULL,
  `lastname`      VARCHAR(128) NULL     DEFAULT NULL,
  `email`         VARCHAR(255) NOT NULL,
  `password`      VARCHAR(255) NOT NULL,
  `locale`        VARCHAR(2)   NOT NULL DEFAULT "de",
  `failed_logins` INT          NOT NULL DEFAULT 0,
  `locked`        TINYINT(1)   NOT NULL DEFAULT 0,
  `reset_key`     VARCHAR(128) NULL     DEFAULT NULL,
  `last_login`    TIMESTAMP    NULL     DEFAULT NULL,
  `last_activity` TIMESTAMP    NULL     DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_user` PRIMARY KEY (`user_id`)
);