# Deploiement Production

## 1. Se connecter au serveur et aller dans le projet

```bash
cd /var/www/html
```

## 2. Tirer les derniers changements

```bash
git pull origin main
```

## 3. Installer les dependances

```bash
composer install --no-dev --optimize-autoloader
```

## 4. Mettre a jour la base de donnees

```bash
php artisan migrate --force
```

## 5. Optimiser l application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6. Verifier que tout marche

```bash
php artisan migrate:status
```

## Rollback (si besoin)

```bash
php artisan migrate:rollback --step=1
```
