#!/usr/bin/env bash

cd public/css
sass style.scss style.css
minify --type=css style.css -o style.min.css
sass content.scss content.css
minify --type=css content.css -o content.min.css
sass maintenance.scss maintenance.css
minify --type=css maintenance.css -o maintenance.min.css

cd admin
sass login.scss login.css
minify --type=css login.css -o login.min.css
sass style.scss style.css
minify --type=css style.css -o style.min.css
