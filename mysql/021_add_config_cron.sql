-- Einfügen der Konfiguration für Cronjobs

INSERT INTO `cms__config` (`section`, `name`, `type`, `value`)
VALUES ('global', 'interval_stats_refresh', 'integer', '24'),
       ('global', 'interval_backup', 'integer', '168');