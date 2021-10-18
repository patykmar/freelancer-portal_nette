FROM php:5.6.40-alpine

WORKDIR /app

COPY . .

CMD ["php","-S","0.0.0.0:8081","-t","/app/www"]

EXPOSE 8081