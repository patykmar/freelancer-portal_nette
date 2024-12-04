# Freelancer portal

Developed on Nette framework ~2014.

## Release notes

- 2021
    - use composer as dependencies manager
    - rewrote some model layer class where use [`nette database`](https://github.com/dg/nette-database) layer instead
      of [`dibi`](https://github.com/dg/dibi)
    - changed namespaces of some components like *grids*, *forms*, etc. because use nette skeleton instead
    - use DI instead of manually create instances
    - **application is not ready for production because**:
        - is still using old php version
        - running on unmaintained nette framework
        - main aim of publishing this source code was backup to GitHub and learn how to dockerized old project
- 2023/11
    - Application has been refactored to run under PHP 7.1 and upgrade Nette framework to 2.3 version
    - 11/23 remove using database layer `dibi` and use only `Nette database`
- 2023/12
    - Upgraded to PHP 7.4 and Nette framework 2.4
- 2024/1
    - Refactor defining classes of forms to use factory class for building forms
    - Update `mPdf` and `ondrejbrejla/eciovni` to `^8.0` respective `^2.3`
- 2024/12
    - Migrate `datagrid` from `miloslavkostir/datagrid` to `ublaboo/datagrid`
    - Upgraded to Nette framework 3.1 and PHP 8.0
    - Upgraded to Nette framework 3.2 and PHP 8.3
    - Added Static Analysis by PHPStan level 1 into GitHub action

## Versions of components

| Nette | PHP |
|-------|-----|
| 3.2   | 8.3 |

## Create missing folders

Please make sure that you have created folders, before start.

```shell
mkdir app/log
mkdir app/temp
mkdir app/temp/cache
mkdir www/webtemp
mkdir app/AdminModule/templates/ChangeStav
mkdir app/AdminModule/templates/FormatDatum
mkdir app/AdminModule/templates/Firma
mkdir app/AdminModule/templates/Fronta
mkdir app/AdminModule/templates/FrontaOsoba
mkdir app/AdminModule/templates/IncidentStav
mkdir app/AdminModule/templates/Osoba
mkdir app/AdminModule/templates/Ovlivneni
mkdir app/AdminModule/templates/Priorita
mkdir app/AdminModule/templates/StavCi
mkdir app/AdminModule/templates/Tarif
mkdir app/AdminModule/templates/TypChange
mkdir app/AdminModule/templates/TypIncident
mkdir app/AdminModule/templates/TypOsoby
mkdir app/AdminModule/templates/TimeZone
mkdir app/AdminModule/templates/Ukon
mkdir app/AdminModule/templates/WebAlertsCi
mkdir app/AdminModule/templates/Zeme
mkdir app/AdminModule/templates/ZpusobUzavreni
```

## Docker

### Development

Docker command for developing inside docker container

```shell
docker-compose up
# or
docker-compose up -d
```

### Links

| App     | Link                   |
|---------|------------------------|
| Portal  | http://127.0.0.1:8080/ |
| Adminer | http://127.0.0.1:8088/ |

### Build

```shell
docker build -t freelancer-portal-nette .
```

### PHPStan

Run static analys by PHPStan

```shell
./vendor/bin/phpstan analyse app --memory-limit 256m --level 1
```

## TODO

- for section Create missing folders add .gitignore file 
