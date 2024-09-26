#!/usr/bin/env bash

cd public/css
sass --silence-deprecation=mixed-decls,color-functions style.scss style.css
minify --type=css style.css -o style.min.css
sass --silence-deprecation=mixed-decls,color-functions content.scss content.css
minify --type=css content.css -o content.min.css
sass --silence-deprecation=mixed-decls,color-functions maintenance.scss maintenance.css
minify --type=css maintenance.css -o maintenance.min.css

cd admin
sass --silence-deprecation=mixed-decls,color-functions login.scss login.css
minify --type=css login.css -o login.min.css
sass --silence-deprecation=mixed-decls,color-functions style.scss style.css
minify --type=css style.css -o style.min.css
