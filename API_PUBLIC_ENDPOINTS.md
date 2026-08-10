# Configuration API Publique Green Express

## 🚨 Diagnostic actuel

### Problème identifié
Le backend Laravel actuel semble être configuré principalement comme interface d'administration. Les routes API publiques que nous avons créées (`/api/menus`, `/api/plans`, etc.) retournent 404 sur Render.

### Causes possibles

1. **Configuration APP_URL incorrecte** sur Render
2. **Routes API non enregistrées correctement** dans le routing Laravel
3. **Backend uniquement admin** - pas configuré pour servir des API publiques
4. **Problème de middleware** - CORS ou authentification bloquant les routes publiques

## 🔍 Configuration requise

### Variables d'environnement Render
Assurez-vous que ces variables sont correctement configurées sur Render :

```env
APP_URL=https://greenexpress-administration.onrender.com
APP_ENV=production
APP_DEBUG=false
```

### Vérification de l'enregistrement des routes

Dans `bootstrap/app.php`, nous avons déjà ajouté :
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // ✅ Ajouté
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

## 🛠️ Solutions

### Option 1 : Vérifier la configuration APP_URL sur Render

Allez sur votre dashboard Render et vérifiez :
- Que `APP_URL` est bien `https://greenexpress-administration.onrender.com`
- Que `APP_ENV` est bien `production`
- Que `APP_DEBUG` est bien `false`

### Option 2 : Redéployer avec les bonnes variables

Si les variables sont incorrectes :
1. Modifiez-les dans le dashboard Render
2. Cliquez sur "Manual Deploy" → "Deploy latest commit"
3. Attendez le déploiement
4. Testez à nouveau

### Option 3 : Créer un service API séparé

Si le backend actuel est uniquement admin, nous pourrions :
1. Créer un nouveau service Render dédié à l'API
2. Configurer ce service pour n'exposer que les routes API
3. Utiliser une base de données partagée

### Option 4 : Ajouter une route de test pour diagnostiquer

Créer une route simple pour tester si l'API fonctionne :

```php
// Dans routes/api.php
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'API is working']);
});
```

Puis tester : `curl https://greenexpress-administration.onrender.com/api/test`

## 📋 Prochaines étapes recommandées

1. **Vérifier les variables d'environnement** sur Render
2. **Surveiller les logs Render** pour voir les erreurs
3. **Tester la route /api/test** une fois déployé
4. **Si le problème persiste**, envisager un service API séparé

## 🎯 Configuration Next.js

Une fois l'API fonctionnelle, configurez votre frontend :

```env
# Dans Vercel
NEXT_PUBLIC_API_URL=https://greenexpress-administration.onrender.com
```

```typescript
// Dans next.config.ts ou votre configuration API
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'https://greenexpress-administration.onrender.com';
```
