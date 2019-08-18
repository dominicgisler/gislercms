-- Struktur der Widget-Tabellen

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
  CONSTRAINT `uq_widget` UNIQUE (`name`),
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