-- Datenbank aufräumen

DROP TABLE IF EXISTS `cms__guestbook_entry`;
DROP TABLE IF EXISTS `cms__post_attribute`;
DROP TABLE IF EXISTS `cms__post_translation`;
DROP TABLE IF EXISTS `cms__post`;
DROP TABLE IF EXISTS `cms__visit`;
DROP TABLE IF EXISTS `cms__session`;
DROP TABLE IF EXISTS `cms__client`;
DROP TABLE IF EXISTS `cms__module`;
DROP TABLE IF EXISTS `cms__widget_translation_history`;
DROP TABLE IF EXISTS `cms__widget_translation`;
DROP TABLE IF EXISTS `cms__widget`;
DROP TABLE IF EXISTS `cms__page_translation_history`;
DROP TABLE IF EXISTS `cms__page_translation`;
DROP TABLE IF EXISTS `cms__page`;
DROP TABLE IF EXISTS `cms__redirect`;
DROP TABLE IF EXISTS `cms__user`;
DROP TABLE IF EXISTS `cms__language`;
DROP TABLE IF EXISTS `cms__config`;
DROP TABLE IF EXISTS `cms__migration`;

CREATE TABLE `cms__migration`
(
  `migration_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(128) NOT NULL,
  `description`  VARCHAR(255) NULL     DEFAULT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_migration` PRIMARY KEY (`migration_id`),
  CONSTRAINT `uq_migration` UNIQUE (`name`)
);
