# Déploiement du site public Next.js (`frontend/`)

Le dépôt **`silasmas`** contient **deux parties** :

| Partie | Dossier | Production typique |
|--------|---------|-------------------|
| API + admin Filament | racine Laravel | `https://api.silasmas.com` |
| Site vitrine Next.js | `frontend/` | `https://silasmas.com` |

Un simple `git push` sur **`silasmas`** met à jour l’**API**, pas automatiquement le **site Next.js**, sauf si Hostinger est configuré pour builder `frontend/` ou si vous poussez aussi vers **`silasmas-web`**.

---

## Pourquoi le site ne change pas après un push ?

1. **Mauvais dépôt** — Hostinger `silasmas.com` est lié à **`silasmas-web`**, mais vous ne poussez que **`silasmas`**.
2. **Pas de rebuild** — Next.js doit exécuter `npm run build` après chaque pull (les fichiers `.next/` ne sont pas sur Git).
3. **Variables au build** — `NEXT_PUBLIC_API_URL` et `NEXT_PUBLIC_SITE_URL` sont figées **au moment du build**. Les changer dans `.env` sans rebuild ne suffit pas.
4. **Cache navigateur / CDN** — forcer un rechargement (Ctrl+F5) ou vider le cache Hostinger.
5. **Mauvaise branche** — déploiement depuis `main` alors que vous poussez une autre branche.

---

## Architecture recommandée (2 repos)

Comme sur Hostinger :

- **`silasmas`** → `api.silasmas.com` (Laravel)
- **`silasmas-web`** → `silasmas.com` (contenu = dossier `frontend/`)

### À chaque modification du front

Depuis la racine de `silasmas` :

```bash
# Une seule fois : ajouter le remote du site
git remote add silasmas-web https://github.com/silasmas/silasmas-web.git

# À chaque release front
git subtree push --prefix=frontend silasmas-web main
```

Sous **PowerShell**, même commandes (Git Bash ou Git for Windows).

Ensuite, sur **Hostinger** (application Node pour `silasmas-web`) :

1. Déclencher **Redéployer** / attendre le déploiement auto Git.
2. Vérifier que la **racine** du projet est bien la racine du repo (pas `frontend/` si le repo *est déjà* le contenu de `frontend`).
3. Commande de build Hostinger : `npm ci && npm run build` puis `npm start` (ou la commande indiquée par Hostinger).

### Variables d’environnement sur Hostinger (`silasmas-web`)

```
NEXT_PUBLIC_API_URL=https://api.silasmas.com/api
NEXT_PUBLIC_SITE_URL=https://silasmas.com
```

Puis **redéployer** (rebuild obligatoire).

---

## Si tout est dans un seul repo sur Hostinger

Si une seule app pointe vers **`silasmas`** :

- Le site public **ne se mettra pas à jour** tant que l’hébergeur ne lance pas `cd frontend && npm ci && npm run build && npm start`.
- Laravel seul sert `public/index.php` — ce n’est **pas** le Next.js dans `frontend/`.

Il faut soit une **deuxième application Node** sur `frontend/`, soit le flux **`silasmas-web`** ci-dessus.

---

## API Laravel après un push (rappel)

Sur `api.silasmas.com`, après pull :

```
GET https://api.silasmas.com/deploy/migrate?secret=VOTRE_DEPLOY_SECRET
```

(`DEPLOY_MIGRATE_ENABLED=true` dans `.env` API.)

Cela ne met **pas** à jour le front Next.js.

---

## Erreur « Inscription impossible » sur silasmas.com

Souvent **CORS** : l’API doit autoriser `https://silasmas.com` dans `.env` sur **api.silasmas.com** :

```env
FRONTEND_URL=https://silasmas.com
CORS_ALLOWED_ORIGINS=http://localhost:3000,https://silasmas.com,https://www.silasmas.com
```

Puis `php artisan config:clear` ou `config:cache` sur le serveur API.

Test : F12 → Réseau → requête `register` en rouge = CORS ou URL API incorrecte.

---

## Vérifications rapides

| Test | Résultat attendu |
|------|------------------|
| Dernier commit sur GitHub `silasmas-web` | Date récente, fichiers `src/` modifiés |
| Build Hostinger | Logs sans erreur `npm run build` |
| Page d’accueil | Code source → scripts `_next/static/...` récents |
| API | `https://api.silasmas.com/api/academy/sessions` répond en JSON |

---

## Script PowerShell (optionnel)

Voir `scripts/push-frontend-to-silasmas-web.ps1` à la racine du projet.
