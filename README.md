Learn-chinese
=============
![Lear-chinese logo](logo.png)

This app allow you to learn chinese characters.

![Screenshot from app](screenshot.png)

Characters are stored in SQLite file `web/app.db`.

# Installation

## With docker
```bash
$ git clone https://github.com/nicolasgrancher/learn-chinese.git
$ cd learn-chinese
$ cp .env.dist .env
```

Edit `.env` :
```bash
APP_NAME= # your app name

NGINX_PROXY_NETWORK= # network, leave empty if none

VIRTUAL_HOST= # vhost
LETSENCRYPT_HOST= # config for Let's Encrypt certificate
LETSENCRYPT_EMAIL= # config for Let's Encrypt certificate
```

Launch it :
```bash
$ docker-compose up --build -d
```
Install vendors :
```bash
$ docker-compose exec php bash
bash-4.3# composer install
...
bash-4.3# exit
```
