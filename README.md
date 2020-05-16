# This is a personal website

There's not much for you here. You might find some code samples in here
but nothing I'm really proud of.

# Install

Start by coying sample `*.env` files:

```sh
cp docker.env.dist docker.env
cp env.dist .env
```

Then edit them, start by generated new passwords and change them all.

Then link the targeted environement docker-composer configuration.

For development environment:

```sh
ln -s docker-compose-dev.yaml docker-compose.yaml
```

Or for production environement:

```sh
ln -s docker-compose-prod.yaml docker-compose.yaml
```

Now you may run `docker-compose` to build containers:

```sh
./docker-rebuild.sh
```

Once done, if everything went fine, connect to postgres container
to set up the database user:

```
docker exec -ti chlovet-postgres /bin/bash
su - postgres
createuser chlovet --createdb --login
createdb chlovet --owner='Here set the .env file database user'
psql -c "ALTER USER chlovet WITH PASSWORD 'Here set the .env file database password';"
```

Download `composer.phar` in the `app/` folder. Ensure this is a legitimate copy
by checking the file using its checksum.

Exit the container, and now let's finally prepare PHP:

```
docker exec -ti chlovet-php-fpm /bin/bash
```

Then for development environment:

```sh
php ./composer.phar install
```

Or for production environement:

```sh
php ./composer.phar install --no-dev --prefer-dist -o
```

And we will finish by creating the schema and clear the cache,
you still need to be in the PHP container:

```sh
bin/console c:c
bin/console doctrine:migrations:migrate
```

Now you're almost done, we have a permission problem with nginx, so you
need to run this from inside the `app/` folder, but outside of docker:

```sh
cd app/
bin/user_rights -o -u root -g www-data -w public/
```

Now everything should work, if not, stop the containers and restart
them this way:

```sh
docker stop chlovet-nginx chlovet-postgres chlovet-php-fpm
./docker-up.sh
```
