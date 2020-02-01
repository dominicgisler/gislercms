-- Erstellt die standardmässige Navigation

INSERT INTO `cms__module` (`name`, `enabled`, `trash`, `controller`, `config`)
VALUES ('navigation', 1, 0, 'ConfigurationModuleController', '{
    "label": "Navigation",
    "items": [
        {
            "label": "Startseite",
            "href": "/startseite"
        },
        {
            "label": "Impressum",
            "href": "/impressum"
        },
        {
            "label": "Datenschutzerklärung",
            "href": "/datenschutz"
        }
    ]
}');