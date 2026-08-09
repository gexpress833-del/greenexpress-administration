# Configuration de l'intégration Next.js ↔ Laravel

## 🌐 Architecture de communication

Votre site Next.js hébergé sur Vercel communiquera avec votre backend Laravel via une API RESTful sécurisée.

```
┌─────────────────┐         HTTPS         ┌──────────────────────┐
│   Next.js       │  ──────────────────> │   Laravel API        │
│   (Vercel)      │  <──────────────────  │   (Render/Supabase)  │
└─────────────────┘         HTTPS         └──────────────────────┘
```

## 🔧 Méthodes de communication

### 1. **Protocole HTTP/HTTPS**
- Toutes les communications se font via HTTPS
- Format de données : JSON
- Méthodes HTTP : GET, POST, PUT, DELETE

### 2. **Authentification via Bearer Token**
- Utilisation de Laravel Sanctum
- Token généré lors du login
- Token inclus dans le header `Authorization: Bearer {token}`

### 3. **CORS (Cross-Origin Resource Sharing)**
- Configuré pour autoriser les requêtes depuis Vercel
- Nécessaire pour les requêtes cross-origin

## 📋 Configuration Laravel

### Variables d'environnement à ajouter

Ajoutez ces variables dans votre fichier `.env` sur Render :

```env
# URL de votre frontend Next.js sur Vercel
FRONTEND_URL=https://votre-site.vercel.app

# Domaines autorisés pour Sanctum
SANCTUM_STATEFUL_DOMAINS=votre-site.vercel.app,localhost:3000

# URL de votre API Laravel (sur Render)
APP_URL=https://votre-api.onrender.com
```

### Configuration CORS

Le fichier `config/cors.php` a été créé avec :

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => [
    env('FRONTEND_URL', 'http://localhost:3000'),
    env('VERCEL_URL', 'https://*.vercel.app'),
],
'supports_credentials' => true,
```

### Configuration Sanctum

Le fichier `config/sanctum.php` a été créé pour gérer l'authentification API.

## 🔐 Sécurité

### Authentification
1. **Login** : `POST /api/login` avec email/password
2. **Réponse** : Token + user
3. **Utilisation** : Inclure le token dans les headers des requêtes protégées

### Exemple de requête depuis Next.js

```javascript
// Login
const login = async (email, password) => {
  const response = await fetch('https://votre-api.onrender.com/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });
  
  const data = await response.json();
  // Stocker le token
  localStorage.setItem('token', data.token);
  return data.user;
};

// Requête authentifiée
const getOrders = async () => {
  const token = localStorage.getItem('token');
  const response = await fetch('https://votre-api.onrender.com/api/orders', {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });
  
  return response.json();
};
```

## 📡 Endpoints API

### Publics (sans authentification)
- `POST /api/login` - Authentification
- `GET /api/menus` - Liste des repas
- `GET /api/plans` - Liste des plans d'abonnement

### Protégés (avec token)
- `GET /api/orders` - Liste des commandes
- `GET /api/orders/{id}` - Détails d'une commande
- `POST /api/subscriptions` - Créer un abonnement
- `POST /api/subscriptions/{id}/renew` - Renouveler un abonnement
- `POST /api/quotes` - Demande de devis

## 🚀 Déploiement

### 1. **Backend Laravel (Render)**
- Le backend est déjà déployé sur Render
- Assurez-vous que les variables d'environnement sont configurées
- L'URL de votre API sera : `https://votre-api.onrender.com`

### 2. **Frontend Next.js (Vercel)**
- Configurez l'URL de l'API dans les variables d'environnement Vercel
- Exemple : `NEXT_PUBLIC_API_URL=https://votre-api.onrender.com`

### 3. **Configuration CORS**
- Assurez-vous que l'URL de votre frontend Vercel est ajoutée dans les `allowed_origins`
- Redéployez le backend après avoir modifié la configuration CORS

## 🐛 Dépannage

### Erreurs CORS
Si vous rencontrez des erreurs CORS :
1. Vérifiez que l'URL de votre frontend est dans `config/cors.php`
2. Vérifiez les variables d'environnement sur Render
3. Redéployez le backend après les modifications

### Erreurs d'authentification
Si le token ne fonctionne pas :
1. Vérifiez que Sanctum est correctement configuré
2. Assurez-vous que le token est bien inclus dans le header `Authorization`
3. Vérifiez que les domaines stateful sont corrects dans `config/sanctum.php`

### Erreurs de connexion
Si le frontend ne peut pas joindre l'API :
1. Vérifiez que l'URL de l'API est correcte
2. Vérifiez que le backend est en ligne sur Render
3. Vérifiez les logs de Render pour les erreurs

## 📝 Notes importantes

- **Source de vérité** : Laravel est la source de vérité pour toutes les données
- **Fallback** : Le frontend Next.js a des données de secours, mais Laravel doit toujours être prioritaire
- **HTTPS** : Toutes les communications doivent se faire en HTTPS
- **Sécurité** : Ne jamais exposer les tokens ou les clés API dans le code frontend
- **CORS** : Essentiel pour autoriser les requêtes cross-origin

## 🔗 Liens utiles

- Documentation Laravel Sanctum : https://laravel.com/docs/sanctum
- Documentation Laravel CORS : https://laravel.com/docs/cors
- Documentation Vercel : https://vercel.com/docs
- Documentation Render : https://render.com/docs
