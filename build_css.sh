#!/usr/bin/env bash

cd public/css
sass style.scss style.css
yuicompressor style.css -o style.min.css
sass content.scss content.css
yuicompressor content.css -o content.min.css
sass maintenance.scss maintenance.css
yuicompressor maintenance.css -o maintenance.min.css

cd admin
sass login.scss login.css
yuicompressor login.css -o login.min.css
sass style.scss style.css
yuicompressor login.css -o login.min.css
