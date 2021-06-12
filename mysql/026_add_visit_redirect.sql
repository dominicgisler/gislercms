-- Ergänzt visit-Tabelle mit Weiterleitung

ALTER TABLE `cms__visit`
    MODIFY `fk_page_translation_id` INT UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `cms__visit`
    ADD COLUMN `fk_redirect_id` INT UNSIGNED NULL DEFAULT NULL AFTER `arguments`;

ALTER TABLE `cms__visit`
    ADD CONSTRAINT `fk_visit_redirect` FOREIGN KEY (`fk_redirect_id`) REFERENCES `cms__redirect` (`redirect_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;