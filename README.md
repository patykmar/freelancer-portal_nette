
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

## Versions of components
|Nette|PHP|
|-----|---|
|2.1.x|5.6|

## Create missing folders

Please make sure that you have created folders, before start.
```shell
mkdir app/log
mkdir app/temp
mkdir app/temp/cache
mkdir www/webtemp
mkdir app/templates/ChangeStav
mkdir app/templates/FormatDatum
mkdir app/templates/Firma
mkdir app/templates/Fronta
mkdir app/templates/FrontaOsoba
mkdir app/templates/IncidentStav
mkdir app/templates/Osoba
mkdir app/templates/Ovlivneni
mkdir app/templates/Priorita
mkdir app/templates/StavCi
mkdir app/templates/Tarif
mkdir app/templates/TypChange
mkdir app/templates/TypIncident
mkdir app/templates/TypOsoby
mkdir app/templates/TimeZone
mkdir app/templates/Ukon
mkdir app/templates/WebAlertsCi
mkdir app/templates/Zeme
mkdir app/templates/ZpusobUzavreni
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