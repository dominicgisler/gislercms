-- Einfügen der Standardwidgets

INSERT INTO `cms__widget` (`widget_id`, `name`, `fk_language_id`)
VALUES (1, 'footer', 1),
       (2, 'navigation', 1);

INSERT INTO `cms__widget_translation` (`widget_translation_id`, `fk_widget_id`, `fk_language_id`, `content`, `enabled`)
VALUES (1, 1, 1, '<p>Copyright &copy; 2019 by Gisler Software</p>', 1),
       (2, 1, 2, '<p>Copyright &copy; 2019 by Gisler Software</p>', 1),
       (3, 2, 1, '<ul><li><a href="/de/startseite">Startseite</a></li></ul>', 1),
       (4, 2, 2, '<ul><li><a href="/en/home">Home</a></li></ul>', 1);