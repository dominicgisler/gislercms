-- Ergänzt visit-Tabelle mit Parametern

ALTER TABLE `cms__visit`
    ADD COLUMN `arguments` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `fk_page_translation_id`;