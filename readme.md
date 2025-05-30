# Cinélog

Cinélog est un réseau social dédié au cinéma, permettant aux utilisateurs de noter, critiquer, et partager leurs avis sur des films avec d'autres cinéphiles.

## Installation

Exécutez les commandes suivantes pour installer les dépendances du projet :

```bash
composer install
```

```bash
npm install
```

## Base de données : 

Créer un .env.local et définir les informations de connexion à la base de données :

```env
DB_USER=
DB_PASS=
DB_HOST=
DB_PORT=
DB_NAME=
```

Puis créer la base de données :

```bash
php bin/console doctrine:database:create
```

## Migrations

```bash
php bin/console doctrine:migrations:migrate
```

## Clés JWT

Pour générer les clés JWT, il faut éxécuter ces commandes :

```bash
openssl genpkey -algorithm RSA -out config/jwt/private.pem
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

**OU**

```bash
php bin/console lexik:jwt:generate-keypair
```


## Utilisation

Pour lancer le projet, il faut exécuter ces deux commandes dans deux terminaux différents :

```bash
npm run watch
```
```bash
symfony server:start
```

## License

[MIT](https://choosealicense.com/licenses/mit/)