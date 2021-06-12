-- Korrekte Verknüpfung der Post-Attribute

ALTER TABLE `cms__post_attribute`
    DROP FOREIGN KEY `fk_post_attribute_post`;

ALTER TABLE `cms__post_attribute`
    ADD CONSTRAINT `fk_post_attribute_post` FOREIGN KEY (`fk_post_id`) REFERENCES `cms__post` (`post_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;