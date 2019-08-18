-- Einfügen der Standardseiten

INSERT INTO `cms__page` (`page_id`, `name`, `fk_language_id`)
VALUES (1, 'Startseite', 1),
       (2, 'Error 404', 1);

INSERT INTO `cms__page_translation` (`page_translation_id`, `fk_page_id`, `fk_language_id`, `name`, `title`, `content`, `enabled`)
VALUES (1, 1, 1, 'startseite', 'Startseite', '<h1>Willkommen auf deiner Webseite!</h1>', 1),
       (2, 1, 2, 'home', 'Home', '<h1>Welcome to your Website!</h1>', 1),
       (3, 2, 1, 'error-404', 'Error 404', '<p>Seite nicht gefunden!</p>', 1),
       (4, 2, 2, 'error-404', 'Error 404', '<p>Page not found!</p>', 1);