
# Freelancer portal

Developed on Nette framework

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