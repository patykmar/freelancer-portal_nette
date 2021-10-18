
# Freelancer portal

Developed on Nette framework

## Versions of components
|Nette|PHP|
|-----|---|
|2.1.x|5.6|

## Docker

### Development
Docker command for developing inside docker container

```shell
docker run -dp 8081:8081 -w /app -v "$(pwd):/app" php:5.6.40-alpine sh -c "php -S 0.0.0.0:8081 -t /app/www"
```

### Build 

```shell
docker build -t freelancer-portal-nette .
```