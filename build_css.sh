#!/usr/bin/env bash

cd public/css
sass style.scss style.css
uglifycss style.css --output style.min.css
sass content.scss content.css
uglifycss content.css --output content.min.css
sass maintenance.scss maintenance.css
uglifycss maintenance.css --output maintenance.min.css

cd admin
sass login.scss login.css
uglifycss login.css --output login.min.css
sass style.scss style.css
uglifycss style.css --output style.min.css
