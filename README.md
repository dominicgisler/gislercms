# gislercms

[![GitHub](https://img.shields.io/github/license/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/issues)
[![GitHub last commit](https://img.shields.io/github/last-commit/dominicgisler/gislercms/dev)](https://github.com/dominicgisler/gislercms/commits/dev)
[![GitHub release](https://img.shields.io/github/release/dominicgisler/gislercms)](https://github.com/dominicgisler/gislercms/releases/latest)
[![GitHub All Releases](https://img.shields.io/github/downloads/dominicgisler/gislercms/total)](https://github.com/dominicgisler/gislercms/releases)

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

Developed and tested with:

- Ubuntu 20.04
- Apache 2.4.41
- PHP 7.3.27
- MariaDB 10.3.25

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

## Custom adjustments

To use this CMS in your projects with custom adjustments (mostly custom templates) you can use the [gislercms-custom](https://github.com/dominicgisler/gislercms-custom) repo, which is prepared to download the latest release and use it with your custom changes.
