# 🏭 Mini ERP Industriel

> Système de gestion intégré pour une usine industrielle développé en PHP/MySQL

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📖 Description

**Mini ERP Industriel** est une application web complète de gestion d'usine qui permet de centraliser et d'automatiser les opérations principales d'une entreprise industrielle. Le système implémente les principes d'un ERP (Enterprise Resource Planning) simplifié avec 4 modules métier essentiels.

### ✨ Caractéristiques principales

- 🔐 **Authentification sécurisée** avec gestion de rôles (RBAC)
- 🏭 **Module Production** : Gestion des ordres de fabrication
- 📦 **Module Stock** : Inventaire et mouvements de stock avec alertes
- 👥 **Module RH** : Gestion des employés, congés et salaires
- 💰 **Module Ventes** : Commandes clients et génération de factures
- 💬 **Messagerie interne** : Communication entre tous les utilisateurs
- 📊 **Dashboard Admin** : Statistiques et graphiques en temps réel

---

## 🎯 Modules Fonctionnels

### 1. 🏭 Production
- Création et suivi des ordres de fabrication
- Assignation des employés aux tâches
- Suivi de l'avancement (en attente → en cours → terminé)
- Historique complet des productions

### 2. 📦 Stock / Inventaire
- Gestion du catalogue produits
- Entrées et sorties de stock
- Alertes automatiques pour stock minimum
- Catégorisation (matières premières, produits finis)

### 3. 👥 Ressources Humaines
- Fiches employés complètes
- Gestion des salaires mensuels
- Système de demandes de congé (approbation/refus)
- Statistiques RH (absences, présences)

### 4. 💰 Ventes
- Gestion des commandes clients
- Génération automatique de factures PDF
- Suivi des statuts de livraison
- Calcul du chiffre d'affaires

### 5. 💬 Messagerie
- Messagerie interne entre utilisateurs
- Notifications de messages non lus
- Filtrage automatique selon les rôles

### 6. 📊 Dashboard Admin
- Widgets de statistiques (utilisateurs, produits, commandes)
- Graphiques des ventes (Chart.js)
- Gestion centralisée de tous les utilisateurs
- Alertes en temps réel

---

## 👥 Rôles Utilisateurs

Le système implémente 4 niveaux d'accès distincts :

| Rôle | Production | Stock | RH | Ventes | Messagerie |
|------|-----------|-------|-----|--------|------------|
| 👑 **Admin** | ✅ Total | ✅ Total | ✅ Total | ✅ Total | ✅ Tous |
| 🧑‍💼 **Manager** | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ Tous |
| 👷 **Employé** | 👁️ Voir ses tâches | 👁️ Voir | ✏️ Ses congés | ❌ | ✅ Tous |
| 🛒 **Client** | ❌ | 👁️ Produits | ❌ | ✏️ Ses commandes | ✅ Manager |

---

## 🛠️ Technologies Utilisées

### Backend
- **PHP 8+** - Logique serveur et traitement des données
- **MySQL** - Base de données relationnelle
- **PDO** - Requêtes sécurisées (protection injection SQL)

### Frontend
- **Bootstrap 5** - Framework CSS responsive
- **HTML5 / CSS3** - Structure et styles
- 
### Environnement
- **XAMPP-XAMPPlite / WAMP** - Serveur local de développement
- **Apache** - Serveur web
- **phpMyAdmin** - Gestion de la base de données

---

## 📦 Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Apache 2.4
- Extension PDO pour PHP

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/mouradbenabdallah/mini-erp-industriel.git
cd mini-erp-industriel
```

2. **Importer la base de données**
```bash
# Ouvrir phpMyAdmin
# Créer une nouvelle base de données : erp_industriel
# Importer le fichier : database/erp_industriel.sql
```

3. **Configurer la connexion**
```php
// Modifier config/database.php
$host = 'localhost';
$dbname = 'erp_industriel';
$username = 'root';
$password = '';
```

4. **Démarrer le serveur**
```bash
# Démarrer XAMPP/WAMP
# Ouvrir : http://localhost/mini-erp-industriel
```

5. **Connexion par défaut**
```
Admin :
Email : admin@erp.com
Mot de passe : admin123

Manager :
Email : manager@erp.com
Mot de passe : manager123
```

---

## 📂 Structure du Projet

```
erp_industriel/
│
├── 📁 config/
│   └── database.php              # Connexion MySQL (PDO)
│
├── 📁 includes/
│   ├── header.php                # Navbar Bootstrap (commune à toutes les pages)
│   ├── footer.php                # Pied de page commun
│   └── auth_check.php            # Vérification : utilisateur connecté + rôle
│
├── 📁 auth/                       # MODULE AUTHENTIFICATION
│   ├── login.php                 # Page de connexion
│   ├── login_process.php         # Traitement de la connexion (vérif BDD)
│   ├── logout.php                # Déconnexion
│   ├── register.php              # Inscription (clients seulement)
│   └── register_process.php      # Traitement inscription
│
├── 📁 admin/                      # MODULE ADMIN
│   ├── index.php                 # Dashboard Admin (page principale admin)
│   ├── users.php                 # Gestion de tous les utilisateurs (CRUD)
│   ├── user_add.php              # Ajouter un utilisateur
│   ├── user_edit.php             # Modifier un utilisateur
│   ├── user_delete.php           # Supprimer/désactiver un utilisateur
│   ├── statistics.php            # Page statistiques (tableaux simples, PAS de Chart.js)
│   └── messages.php              # Messagerie de l'admin (envoyer/recevoir)
│
├── 📁 dashboard/                  # MODULE DASHBOARD (après connexion)
│   ├── admin_dashboard.php       # Dashboard spécifique Admin
│   ├── manager_dashboard.php     # Dashboard spécifique Manager
│   ├── employe_dashboard.php     # Dashboard spécifique Employé
│   └── client_dashboard.php      # Dashboard spécifique Client
│
├── 📁 production/                 # MODULE PRODUCTION
│   ├── index.php                 # Liste de tous les ordres de fabrication
│   ├── create.php                # Créer un nouvel ordre
│   ├── create_process.php        # Traitement création (INSERT en BDD)
│   ├── view.php                  # Voir détails d'un ordre (lecture seule)
│   ├── edit.php                  # Modifier un ordre existant
│   ├── edit_process.php          # Traitement modification (UPDATE)
│   ├── delete.php                # Supprimer un ordre (avec confirmation)
│   └── messages.php              # Messagerie liée à la production
│
├── 📁 stock/                      # MODULE STOCK / INVENTAIRE
│   ├── index.php                 # Liste de tous les produits
│   ├── create.php                # Ajouter un nouveau produit
│   ├── create_process.php        # Traitement ajout produit
│   ├── view.php                  # Voir détails d'un produit
│   ├── edit.php                  # Modifier un produit
│   ├── edit_process.php          # Traitement modification produit
│   ├── delete.php                # Supprimer un produit
│   ├── mouvement.php             # Enregistrer entrée/sortie de stock
│   ├── mouvement_process.php     # Traitement mouvement
│   ├── historique.php            # Historique de tous les mouvements
│   └── messages.php              # Messagerie liée au stock
│
├── 📁 rh/                         # MODULE RESSOURCES HUMAINES
│   ├── index.php                 # Liste de tous les employés
│   ├── employe_add.php           # Ajouter un employé
│   ├── employe_add_process.php   # Traitement ajout employé
│   ├── employe_view.php          # Voir fiche complète employé
│   ├── employe_edit.php          # Modifier un employé
│   ├── employe_edit_process.php  # Traitement modification
│   ├── employe_delete.php        # Supprimer/désactiver employé
│   ├── conges.php                # Liste des demandes de congé
│   ├── conge_add.php             # Demander un congé (employé)
│   ├── conge_approve.php         # Approuver/refuser congé (manager)
│   ├── salaires.php              # Gestion des salaires mensuels
│   ├── salaire_add.php           # Enregistrer paiement salaire
│   └── messages.php              # Messagerie RH
│
├── 📁 ventes/                     # MODULE VENTES
│   ├── index.php                 # Liste de toutes les commandes
│   ├── create.php                # Créer une nouvelle commande
│   ├── create_process.php        # Traitement création commande
│   ├── view.php                  # Voir détails d'une commande
│   ├── edit.php                  # Modifier statut commande (manager)
│   ├── edit_process.php          # Traitement modification
│   ├── delete.php                # Annuler une commande
│   ├── facture.php               # Générer facture simple (HTML → PDF optionnel)
│   └── messages.php              # Messagerie ventes
│
├── 📁 messagerie/                 # MODULE MESSAGERIE GLOBAL (optionnel)
│   ├── inbox.php                 # Boîte de réception globale
│   ├── sent.php                  # Messages envoyés
│   ├── send.php                  # Composer nouveau message
│   ├── send_process.php          # Traitement envoi
│   ├── view.php                  # Lire un message
│   └── delete.php                # Supprimer un message
│
├── 📁 assets/
│   ├── css/
│   │   └── style.css             # Styles CSS personnalisés
│   └── img/
│       └── logo.png              # Logo de l'ERP
│
├── 📁 database/
│   └── erp_industriel.sql        # Script SQL pour créer la BDD
│
└── index.php                      # Page d'accueil (redirection selon rôle)

```

---

## 🗓️ Roadmap

### Version 1.0 (Actuelle) ✅
- [x] Authentification et gestion des rôles
- [x] 4 modules métier (Production, Stock, RH, Ventes)
- [x] Messagerie interne
- [x] Dashboard Admin


---

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request


---

## 👨‍💻 Auteur

**Mourad Ben Abdallah**
- Université : ITEAM University
- Filière : TC-06
- Module : Dev Web 2
- LinkedIn : [Votre profil]([https://linkedin.com/in/votre-profil](https://www.linkedin.com/in/mourad-ben-abdallah-0b692117a/))

---



---

## 🙏 Remerciements

- Bootstrap pour le framework CSS
- FontAwesome pour les icônes
- La communauté PHP pour le support

---

<div align="center">
  
### ⭐ Si ce projet vous a aidé, n'hésitez pas à lui donner une étoile !

**Made with ❤️ for ITEAM University**

</div>
