-- Struktur der Weiterleitungs-Tabelle

CREATE TABLE `cms__redirect`
(
    `redirect_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(128) NOT NULL,
    `enabled`     TINYINT(1)   NOT NULL DEFAULT 1,
    `route`       VARCHAR(128) NOT NULL,
    `location`    VARCHAR(512) NOT NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `pk_redirect` PRIMARY KEY (`redirect_id`),
    CONSTRAINT `uq_redirect_name` UNIQUE (`name`),
    CONSTRAINT `uq_redirect_route` UNIQUE (`route`)
);