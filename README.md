
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
- app/log
- app/temp
- app/temp/cache
- www/webtemp
- app/templates/ChangeStav
- app/templates/FormatDatum
- app/templates/Firma
- app/templates/Fronta
- app/templates/FrontaOsoba
- app/templates/IncidentStav
- app/templates/Osoba
- app/templates/Ovlivneni
- app/templates/Priorita
- app/templates/StavCi
- app/templates/Tarif
- app/templates/TypChange
- app/templates/TypIncident
- app/templates/TypOsoby
- app/templates/TimeZone
- app/templates/Ukon
- app/templates/WebAlertsCi
- app/templates/Zeme
- app/templates/ZpusobUzavreni

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