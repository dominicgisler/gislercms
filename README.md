# gislercms

[![GitHub](https://img.shields.io/github/license/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/issues)
[![GitHub last commit](https://img.shields.io/github/last-commit/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/commits/dev)
[![GitHub release](https://img.shields.io/github/release/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/releases)
[![GitHub All Releases](https://img.shields.io/github/downloads/dominicgisler/gislercms/total)](https://github.com/dominicgisler/gislercms/releases)

A simple CMS to manage your website contents

## Global requirements

- PHP 7.3 (could also work with 7.1 or 7.2, but it's not tested)
- PDO
- MySQL or MariaDB
- Some kind of webserver
- Setup vHost to access the application

Developed and tested with:

- Ubuntu 18.04
- Apache 2.4.29
- PHP 7.3.8
- MariaDB 10.1.40

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

- Use `{URL}/admin/setup` to configure interactive (DB access and login data)
- Login on `{URL}/admin/login`
- Edit configuration in `config/{file}.php` if adjustments needed
