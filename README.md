# gislercms

A simple CMS to manage your website contents

## Global requirements

- PHP 7.3 (could also work with 7.1 or 7.2, but it's not tested)
- PHP-PDO
- MySQL
- Some kind of webserver
- Setup vHost to access the application

Developed and tested with:

- Ubuntu 18.04
- Apache 2.4.29
- PHP 7.3.3
- MySQL 5.7.25

## How to install

### Use Release

- Download latest release and extract it.
- See `Setup` below

### Use Source

#### Requirements

- See `Global requirements`
- npm sass (`npm install -g sass`)
- npm yuicompressor (`npm install -g yuicompressor`)
- [composer](https://getcomposer.org/download/)

#### Install

- Download or clone the project
- Run composer (`composer install`)

### Setup

- Edit configuration in `config/[name].php`
- Use `{URL}/admin/setup` to configure interactive (DB access and login data)
- Login on `{URL}/admin/login`
