-- Struktur der client-Tabelle

CREATE TABLE `cms__client`
(
  `client_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       VARCHAR(36)  NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `pk_client` PRIMARY KEY (`client_id`),
  CONSTRAINT `uq_client` UNIQUE (`uuid`)
);