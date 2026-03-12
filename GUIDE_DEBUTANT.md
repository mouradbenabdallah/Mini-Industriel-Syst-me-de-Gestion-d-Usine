# 📚 GUIDE DÉBUTANT - Usine Industriel

> **Code simplifié pour débutants - Sans jointures, sans CSS custom**

---

## 🎯 Ce qui a été simplifié

### ✅ 1. CSS supprimé
- **Avant** : 800+ lignes de CSS custom
- **Maintenant** : Seulement Bootstrap 5 !
- Le fichier `assets/css/style.css` est vide

### ✅ 2. Header simplifié
- **Avant** : 450 lignes de code complexe
- **Maintenant** : 180 lignes simples avec Bootstrap
- Navigation claire et facile à comprendre

### ✅ 3. Footer simplifié
- **Avant** : Design complexe avec gradients
- **Maintenant** : Footer simple Bootstrap

### ✅ 4. Requêtes SQL SANS jointures
- Toutes les requêtes sont simples
- Pas de `JOIN`, `LEFT JOIN`, etc.
- Facile à comprendre pour débutants

---

## 📁 Structure du Projet

```
usine_industriel/
│
├── config/
│   └── database.php          ← Connexion MySQL
│
├── includes/
│   ├── header.php             ← Navigation (SIMPLIFIÉ)
│   ├── footer.php             ← Footer (SIMPLIFIÉ)
│   └── auth_check.php         ← Vérification connexion
│
├── auth/
│   ├── login.php              ← Page de connexion
│   ├── login_process.php      ← Traiter la connexion
│   └── logout.php             ← Déconnexion
│
├── dashboard/
│   ├── admin_dashboard.php
│   ├── manager_dashboard.php
│   ├── employe_dashboard.php
│   └── client_dashboard.php
│
├── production/
│   ├── index.php              ← Liste des productions
│   ├── create.php             ← Créer production
│   └── ...
│
├── stock/
│   ├── index.php              ← Liste des produits
│   ├── create.php             ← Ajouter produit
│   └── ...
│
├── rh/
│   ├── employes.php           ← Liste des employés
│   ├── conges.php             ← Demandes de congé
│   └── ...
│
└── ventes/
    ├── commandes.php          ← Liste des commandes
    ├── commande_add.php       ← Nouvelle commande
    └── ...
```

---

## 🔌 Connexion à la base de données

**Fichier : `config/database.php`**

```php
<?php
// Configuration
$host = 'localhost';
$dbname = 'usine_industriel';
$username = 'root';
$password = '';

// Connexion PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $username, $password, $options);
?>
```

**C'est tout !** Simple et clair.

---

## 🔒 Vérification d'authentification

**Fichier : `includes/auth_check.php`**

```php
<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Vérifier le rôle (si $allowed_roles est défini)
if (isset($allowed_roles) && !in_array($_SESSION['user']['role'], $allowed_roles)) {
    header('Location: ../index.php');
    exit;
}
?>
```

**Comment l'utiliser** dans une page :

```php
<?php
session_start();
$allowed_roles = ['admin', 'manager']; // Qui peut accéder à cette page
require_once '../config/database.php';
require_once '../includes/auth_check.php';
?>
```

---

## 📄 Structure d'une page simple

### Exemple : `stock/index.php`

```php
<?php
// 1. Démarrer la session
session_start();

// 2. Définir qui peut accéder
$allowed_roles = ['admin', 'manager', 'employe'];

// 3. Connexion BDD
require_once '../config/database.php';

// 4. Vérifier l'authentification
require_once '../includes/auth_check.php';

// 5. Récupérer les données (SANS jointure)
$stmt = $pdo->query('SELECT * FROM produits ORDER BY nom');
$produits = $stmt->fetchAll();

// 6. Titre de la page
$page_title = 'Gestion du stock';

// 7. Inclure le header
require_once '../includes/header.php';
?>

<!-- 8. Votre HTML ici -->
<h2>Liste des produits</h2>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Quantité</th>
            <th>Prix</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produits as $p): ?>
        <tr>
            <td><?php echo $p['nom']; ?></td>
            <td><?php echo $p['quantite']; ?></td>
            <td><?php echo $p['prix']; ?> TND</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php 
// 9. Inclure le footer
require_once '../includes/footer.php'; 
?>
```

---

## 🔍 Requêtes SQL SANS jointures

### ❌ AVANT (avec jointure - compliqué)

```php
// Difficile pour débutant
$sql = "SELECT c.*, u.nom as client_nom, p.nom as produit_nom 
        FROM commandes c 
        LEFT JOIN users u ON c.client_id = u.id 
        LEFT JOIN produits p ON c.produit_id = p.id";
$stmt = $pdo->query($sql);
```

### ✅ MAINTENANT (sans jointure - simple)

```php
// 1. Récupérer les commandes
$stmt = $pdo->query('SELECT * FROM commandes');
$commandes_list = $stmt->fetchAll();

// 2. Pour chaque commande, récupérer le nom du client
$commandes = [];
foreach ($commandes_list as $c) {
    // Nom du client
    $stmt = $pdo->prepare('SELECT nom FROM users WHERE id = ?');
    $stmt->execute([$c['client_id']]);
    $client = $stmt->fetch();
    
    // Nom du produit
    $stmt = $pdo->prepare('SELECT nom FROM produits WHERE id = ?');
    $stmt->execute([$c['produit_id']]);
    $produit = $stmt->fetch();
    
    $commandes[] = [
        'id' => $c['id'],
        'client_nom' => $client['nom'],
        'produit_nom' => $produit['nom'],
        'quantite' => $c['quantite'],
        'total' => $c['total']
    ];
}
```

**C'est plus long mais BEAUCOUP plus facile à comprendre !**

---

## 🎨 Design avec Bootstrap uniquement

### Classes Bootstrap utiles

#### Boutons
```html
<button class="btn btn-primary">Bleu</button>
<button class="btn btn-success">Vert</button>
<button class="btn btn-danger">Rouge</button>
<button class="btn btn-warning">Jaune</button>
```

#### Tableaux
```html
<table class="table table-striped table-hover">
    <thead class="table-primary">
        <tr>
            <th>Colonne 1</th>
            <th>Colonne 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Donnée 1</td>
            <td>Donnée 2</td>
        </tr>
    </tbody>
</table>
```

#### Cartes
```html
<div class="card">
    <div class="card-header">
        Titre
    </div>
    <div class="card-body">
        Contenu
    </div>
</div>
```

#### Alertes
```html
<div class="alert alert-success">Succès !</div>
<div class="alert alert-danger">Erreur !</div>
<div class="alert alert-warning">Attention !</div>
```

#### Badges
```html
<span class="badge bg-primary">Bleu</span>
<span class="badge bg-success">Vert</span>
<span class="badge bg-danger">Rouge</span>
```

---

## 🚀 Comment ajouter une nouvelle page

### Étape 1 : Créer le fichier

Par exemple : `stock/nouveau.php`

```php
<?php
session_start();
$allowed_roles = ['admin', 'manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$page_title = 'Ma nouvelle page';
require_once '../includes/header.php';
?>

<h2>Ma nouvelle page</h2>
<p>Contenu ici...</p>

<?php require_once '../includes/footer.php'; ?>
```

### Étape 2 : Ajouter dans la navigation

Éditer `includes/header.php` et ajouter un lien :

```php
<li class="nav-item">
    <a class="nav-link" href="../stock/nouveau.php">
        <i class="bi bi-star"></i> Nouveau
    </a>
</li>
```

**C'est tout !**

---

## 💡 Exemples de code simple

### Insérer des données

```php
<?php
// Formulaire traité
if ($_POST) {
    $nom = $_POST['nom'];
    $quantite = $_POST['quantite'];
    
    $stmt = $pdo->prepare('INSERT INTO produits (nom, quantite) VALUES (?, ?)');
    $stmt->execute([$nom, $quantite]);
    
    $_SESSION['success'] = 'Produit ajouté !';
    header('Location: index.php');
    exit;
}
?>
```

### Modifier des données

```php
<?php
$id = $_GET['id'];

if ($_POST) {
    $nom = $_POST['nom'];
    
    $stmt = $pdo->prepare('UPDATE produits SET nom = ? WHERE id = ?');
    $stmt->execute([$nom, $id]);
    
    $_SESSION['success'] = 'Produit modifié !';
    header('Location: index.php');
    exit;
}

// Récupérer le produit
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);
$produit = $stmt->fetch();
?>
```

### Supprimer des données

```php
<?php
$id = $_GET['id'];

$stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
$stmt->execute([$id]);

$_SESSION['success'] = 'Produit supprimé !';
header('Location: index.php');
exit;
?>
```

---

## 🎓 Conseils pour débutants

### 1. **Toujours commencer par ces 4 lignes :**
```php
session_start();
$allowed_roles = ['admin', 'manager'];
require_once '../config/database.php';
require_once '../includes/auth_check.php';
```

### 2. **Toujours utiliser les requêtes préparées :**
```php
// ✅ BON
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);

// ❌ MAUVAIS (injection SQL)
$stmt = $pdo->query("SELECT * FROM produits WHERE id = $id");
```

### 3. **Toujours échapper le HTML :**
```php
// ✅ BON
echo htmlspecialchars($nom);

// ❌ MAUVAIS (faille XSS)
echo $nom;
```

### 4. **Utiliser les messages flash :**
```php
// Définir le message
$_SESSION['success'] = 'Opération réussie !';

// Il s'affichera automatiquement sur la prochaine page
header('Location: index.php');
```

### 5. **Toujours terminer par le footer :**
```php
<?php require_once '../includes/footer.php'; ?>
```

---

## 🔐 Les 4 rôles du système

| Rôle | Accès |
|------|-------|
| **admin** | Tout |
| **manager** | Production, Stock, RH, Ventes |
| **employe** | Voir ses tâches, demander congés |
| **client** | Voir produits, passer commandes |

### Vérifier le rôle dans le code

```php
<?php
$role = $_SESSION['user']['role'];

if ($role == 'admin') {
    echo "Vous êtes administrateur";
}

if ($role == 'admin' || $role == 'manager') {
    echo "Vous êtes admin ou manager";
}
?>
```

---

## 🛠️ Dépannage

### Erreur : "Cannot modify header information"
**Solution** : Vérifier qu'il n'y a pas d'espace ou de texte avant `<?php`

### Erreur : "Call to a member function on null"
**Solution** : Vérifier que la requête SQL est correcte

### Page blanche
**Solution** : Activer l'affichage des erreurs :
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

### Les CSS ne s'appliquent pas
**Solution** : Normal ! On utilise seulement Bootstrap maintenant.

---

## 📖 Ressources

- **Bootstrap 5** : https://getbootstrap.com/docs/5.3/
- **PHP PDO** : https://www.php.net/manual/fr/book.pdo.php
- **Bootstrap Icons** : https://icons.getbootstrap.com/

---

## ✅ Checklist avant de créer une page

- [ ] `session_start()` en premier
- [ ] Définir `$allowed_roles`
- [ ] Inclure `database.php`
- [ ] Inclure `auth_check.php`
- [ ] Définir `$page_title`
- [ ] Inclure `header.php`
- [ ] Écrire votre contenu HTML
- [ ] Inclure `footer.php`

---

**Bon courage ! 🚀**

*Ce guide est fait pour les débutants. Si vous avez des questions, relisez ce guide étape par étape.*