-- Struktur der Gästebuch-Tabellen

CREATE TABLE `cms__guestbook_entry`
(
  `guestbook_entry_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guestbook_identifier` VARCHAR(128) NOT NULL,
  `input`                TEXT         NOT NULL,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_guestbook_entry` PRIMARY KEY (`guestbook_entry_id`)
);