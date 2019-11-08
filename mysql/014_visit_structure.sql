-- Struktur der visit-Tabelle

CREATE TABLE `cms__visit`
(
  `visit_id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_page_translation_id` INT UNSIGNED NOT NULL,
  `fk_session_id`          INT UNSIGNED NOT NULL,
  `created_at`             TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_visit` PRIMARY KEY (`visit_id`),
  CONSTRAINT `fk_visit_page_translation` FOREIGN KEY (`fk_page_translation_id`) REFERENCES `cms__page_translation` (`page_translation_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_visit_session` FOREIGN KEY (`fk_session_id`) REFERENCES `cms__session` (`session_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);