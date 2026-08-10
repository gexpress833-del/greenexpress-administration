# Diagnostic du déploiement API sur Render

## 🚨 Problème actuel
Les routes API retournent 404 sur Render : `https://greenexpress-administration.onrender.com/api/menus`

## ✅ État local
- `bootstrap/app.php` : ✅ Contient `api: __DIR__.'/../routes/api.php'`
- `routes/api.php` : ✅ Existe et contient les routes
- Contrôleurs API : ✅ Créés et fonctionnels

## 🔍 Diagnostic

### 1. Vérifier le déploiement sur Render
Aller sur votre dashboard Render et vérifier :
- Statut du dernier déploiement (en cours/terminé/échoué)
- Logs de déploiement pour voir s'il y a des erreurs
- Si le déploiement est bien basé sur le dernier commit

### 2. Vider le cache sur Render
Le cache de routes Laravel peut être en cause. Ajoutez un script de déploiement :

Dans Render → Build & Deploy → Configure Build Command :
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### 3. Vérifier les variables d'environnement sur Render
Assurez-vous que les variables suivantes sont configurées :
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://greenexpress-administration.onrender.com`

### 4. Vérifier le fichier bootstrap/app.php sur Render
Parfois, les fichiers ne sont pas bien déployés. Vérifiez dans les logs Render que le fichier `bootstrap/app.php` contient bien la ligne :
```php
api: __DIR__.'/../routes/api.php',
```

## 🛠️ Solutions immédiates

### Option 1 : Redéployer manuellement sur Render
1. Allez sur votre dashboard Render
2. Cliquez sur "Manual Deploy" → "Deploy latest commit"
3. Attendez que le déploiement se termine
4. Testez à nouveau

### Option 2 : Forcer la reconstruction
Ajoutez un fichier vide pour forcer la reconstruction :
```bash
# Dans votre projet local
touch .rebuild
git add .rebuild
git commit -m "Force rebuild"
git push
```

### Option 3 : Vérifier les logs Render
Allez dans les logs de votre service Render et cherchez :
- Des erreurs PHP
- Des erreurs de routes
- Des erreurs de configuration

## 🧪 Test local

Pour vérifier que les routes fonctionnent localement :
```bash
php artisan serve
# Dans un autre terminal
curl http://localhost:8000/api/menus
curl http://localhost:8000/api/plans
```

## 📝 Si le problème persiste

Si après ces étapes le problème persiste, il peut s'agir de :
1. Un problème de configuration Render
2. Un cache persistant
3. Un problème avec le fichier `bootstrap/app.php` dans le déploiement

Contactez-moi avec les logs Render pour un diagnostic plus approfondi.
