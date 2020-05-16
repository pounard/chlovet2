# This is a personal website

There's not much for you here. You might find some code samples in here
but nothing I'm really proud of.

# Install

 - copy `docker.env.dist` as `.env` and `docker.env` files (yes, this is ugly),
 - modify them, be careful with passwords,
 - create `app/.env` for symfony (yes, I will move those into `docker.env` soon),
 - wget `composer` somewhere in the `app/` folder,
 - run `./docker-rebuild.sh` and prey for it to work (it should),
 - run `docker exec -ti chlovet-postgres /bin/bash`, su as `postgres` user and create database,
 - run `docker exec -ti chlovet-php-fpm /bin/bash`,
 - in docker, then run `composer install --no-dev --prefer-dist -o`,
 - in docker, then run `bin/console doctrine:migrations:migrate`,
 - run `bin/user_rights -o -u root -g www-data -w public/` otherwise nginx won't find public assets.

