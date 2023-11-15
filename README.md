
# Freelancer portal

Developed on Nette framework ~2014. In 2021 has been done code review where was:
 - use composer as dependencies manager
 - rewrote some model layer class where use [`nette database`](https://github.com/dg/nette-database) layer instead of [`dibi`](https://github.com/dg/dibi)
 - changed namespaces of some components like *grids*, *forms*, etc. because use nette skeleton instead
 - use DI instead of manually create instances
 - **application is not ready for production because**:
   - is still using old php version
   - running on unmaintained nette framework
   - main aim of publishing this source code was backup to GitHub and learn how to dockerized old project
 - Application has been refactored in 2023/11 to run under PHP 7.1 and Nette 2.3 

## Versions of components
| Nette  | PHP |
|--------|-----|
| 2.3.11 | 7.1 |

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

## Database
Application has been development with MySQL as a database, but for dockerized and use minimum dependencies of 
container I've decided to use SQLite. So I've rewritten some database call like change `CONCAT` method to `||` 
in SQLite dialect. **Not all database calls have been changed!**

### Import sample data
```shell
sqlite3 temp/database.db
sqlite3 temp/database.db < ../docker/sqlite/portal_sqlite.sql
sqlite3 temp/database.db < ../docker/sqlite/faktury_import.sql
```

## Docker

### Development
Docker command for developing inside docker container

```shell
docker-compose up
```

### Build 

```shell
docker build -t freelancer-portal-nette .
```