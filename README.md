# GislerCMS

[![License](https://img.shields.io/github/license/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/blob/master/LICENSE)
[![Issues](https://img.shields.io/github/issues/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/issues)
[![Last commit](https://img.shields.io/github/last-commit/dominicgisler/gislercms/dev)](https://github.com/dominicgisler/gislercms/commits/dev)
[![Latest release](https://img.shields.io/github/release/dominicgisler/gislercms?label=latest+release)](https://github.com/dominicgisler/gislercms/releases/latest)

[![total downloads](https://img.shields.io/github/downloads/dominicgisler/gislercms/gislercms.zip?label=total+downloads)](https://github.com/dominicgisler/gislercms/releases)
[![downloads latest release](https://img.shields.io/github/downloads/dominicgisler/gislercms/latest/gislercms.zip?label=downloads+latest+release)](https://github.com/dominicgisler/gislercms/releases/latest)

A simple CMS to manage your website contents

## Demo

The current release is available as a demo on [demo.cms.gisler.software](https://demo.cms.gisler.software)

Login on [/admin](https://demo.cms.gisler.software/admin) with user `demo` and password `123456` to try it out.

The demo will be reset to the latest release every hour.

## Global requirements

- PHP 7.3
- PDO
- MySQL or MariaDB
- Some kind of webserver
- Setup vHost to access the application

## How to install

### Use Release

- Download latest release and extract it.
- See `Setup` below

### Use Source

#### Requirements

- See `Global requirements`
- npm sass (`npm install -g sass`)
- npm uglifycss (`npm install -g uglifycss`)
- [composer](https://getcomposer.org/download/)

#### Install

- Download or clone the project
- Run composer (`composer install`)

### Setup

- Use `{URL}/admin/setup` to configure interactive (DB access and login data)
- Login on `{URL}/admin/login`

## Custom theme

To create custom themes for this CMS you can use the [gislercms-theme](https://github.com/dominicgisler/gislercms-theme) repo to have an example.
