-- Erstellt die standardmässige Navigation

INSERT INTO `cms__module` (`name`, `enabled`, `trash`, `controller`, `config`)
VALUES
('navigation-de', 1, 0, 'ConfigurationModuleController', '{
    "label": "Navigation",
    "items": [
        {
            "label": "Startseite",
            "href": "/startseite"
        },
        {
            "label": "Galerie",
            "href": "/galerie"
        },
        {
            "label": "Gästebuch",
            "href": "/gaestebuch"
        }
    ]
}'),
('navigation-en', 1, 0, 'ConfigurationModuleController', '{
    "label": "Navigation",
    "items": [
        {
            "label": "Home",
            "href": "/home"
        },
        {
            "label": "Gallery",
            "href": "/gallery"
        },
        {
            "label": "Guestbook",
            "href": "/guestbook"
        }
    ]
}'),
('gallery-de', 1, 0, 'GalleryModuleController', '{
    "title": "Galerie",
    "galleries": {
        "demo1": {
            "title": "Demo 1",
            "description": "Demo Galerie 1",
            "path": "gallery/demo1",
            "cover": "gallery/demo1/pexels-holiho-1112186.jpg"
        },
        "demo2": {
            "title": "Demo 2",
            "description": "Demo Galerie 2",
            "path": "gallery/demo2",
            "cover": "gallery/demo2/pexels-pixabay-235621.jpg"
        }
    }
}'),
('gallery-en', 1, 0, 'GalleryModuleController', '{
    "title": "Gallery",
    "galleries": {
        "demo1": {
            "title": "Demo 1",
            "description": "Demo Gallery 1",
            "path": "gallery/demo1",
            "cover": "gallery/demo1/pexels-holiho-1112186.jpg"
        },
        "demo2": {
            "title": "Demo 2",
            "description": "Demo Gallery 2",
            "path": "gallery/demo2",
            "cover": "gallery/demo2/pexels-pixabay-235621.jpg"
        }
    }
}'),
('guestbook-de', 1, 0, 'GuestbookModuleController', '{
    "identifier": "guestbook",
    "notification": {
        "enable": false,
        "mailer": {
            "smtp": false,
            "host": "mail.example.com",
            "smtpauth": true,
            "username": "max.muster@example.com",
            "password": "mypass",
            "smtpsecure": "ssl",
            "port": 465
        },
        "from": {
            "email": "max.muster@example.com",
            "name": "Max Muster"
        },
        "to": {
            "email": "max.muster@example.com",
            "name": "Max Muster"
        }
    },
    "elements": {
        "name": {
            "label": "Name",
            "placeholder": "Name",
            "type": "text",
            "required": true,
            "validators": {
                "string_length": {
                    "min": 3,
                    "max": 20
                }
            }
        },
        "message": {
            "type": "textarea",
            "label": "Nachricht",
            "placeholder": "Nachricht",
            "hint": "Ihre Nachricht",
            "required": true,
            "validators": {
                "string_length": {
                    "min": 10,
                    "max": 500
                }
            }
        },
        "send": {
            "type": "submit",
            "label": "Senden",
            "class": "btn-primary"
        },
        "reset": {
            "type": "reset",
            "label": "Abbrechen",
            "class": "btn-secondary"
        }
    },
    "messages": {
        "error": "Bitte überprüfe deine Eingaben",
        "success": "Eintrag wurde gespeichert",
        "failed": "Es ist ein Fehler aufgetreten, bitte versuche es später erneut",
        "empty": "Bisher keine Einträge",
        "timestamp": "am %s um %s"
    }
}'),
('guestbook-en', 1, 0, 'GuestbookModuleController', '{
    "identifier": "guestbook",
    "notification": {
        "enable": false,
        "mailer": {
            "smtp": false,
            "host": "mail.example.com",
            "smtpauth": true,
            "username": "max.muster@example.com",
            "password": "mypass",
            "smtpsecure": "ssl",
            "port": 465
        },
        "from": {
            "email": "max.muster@example.com",
            "name": "Max Muster"
        },
        "to": {
            "email": "max.muster@example.com",
            "name": "Max Muster"
        }
    },
    "elements": {
        "name": {
            "label": "Name",
            "placeholder": "Name",
            "type": "text",
            "required": true,
            "validators": {
                "string_length": {
                    "min": 3,
                    "max": 20
                }
            }
        },
        "message": {
            "type": "textarea",
            "label": "Message",
            "placeholder": "Message",
            "hint": "Your message",
            "required": true,
            "validators": {
                "string_length": {
                    "min": 10,
                    "max": 500
                }
            }
        },
        "send": {
            "type": "submit",
            "label": "Send",
            "class": "btn-primary"
        },
        "reset": {
            "type": "reset",
            "label": "Cancel",
            "class": "btn-secondary"
        }
    },
    "messages": {
        "error": "Please check your entries",
        "success": "Your entry has been saved",
        "failed": "An error occurred, please try again later",
        "empty": "No entries yet",
        "timestamp": "at %s on %s"
    }
}');