# Tableau de Gestion de Projets

Une application web de gestion de projets et de tâches avec un tableau Kanban interactif, développée en PHP.

## Fonctionnalités

- **Authentification des utilisateurs** : Inscription, connexion et déconnexion
- **Gestion de projets** : Création, modification et suppression de projets
- **Tableau Kanban** : Interface intuitive pour la gestion des tâches
- **Drag and Drop** : Déplacement des tâches entre les colonnes (À faire, En cours, Terminé)
- **AJAX** : Mise à jour du statut des tâches sans rechargement de la page

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, etc.)

## Installation

1. Clonez ou téléchargez ce dépôt dans le répertoire de votre serveur web.

2. Créez une base de données MySQL nommée `gestion_taches`.

3. Importez le schéma de la base de données en exécutant le fichier `config/schema.sql`.

4. Configurez les paramètres de connexion à la base de données dans le fichier `config/database.php`.

5. Assurez-vous que les permissions des fichiers et répertoires sont correctement configurées.

6. Accédez à l'application via votre navigateur web.

## Structure du projet

```
GestionTaches/
├── assets/             # Ressources statiques (CSS, JS, images)
│   ├── css/            # Fichiers CSS
│   ├── js/             # Fichiers JavaScript
│   └── images/         # Images
├── config/             # Configuration de l'application
├── controleurs/        # Contrôleurs de l'application
├── includes/           # Fichiers inclus (header, footer, fonctions)
├── modeles/            # Modèles pour l'accès aux données
├── vues/               # Vues de l'application (non utilisées dans cette version)
├── index.php           # Point d'entrée de l'application
└── README.md           # Ce fichier
```

## Utilisation

1. Créez un compte utilisateur en cliquant sur "Inscription".
2. Connectez-vous avec vos identifiants.
3. Créez un nouveau projet en cliquant sur "Nouveau Projet".
4. Accédez au tableau Kanban en cliquant sur "Tableau" pour un projet.
5. Ajoutez des tâches en cliquant sur "Nouvelle Tâche".
6. Déplacez les tâches entre les colonnes en les faisant glisser.

## Données de test

Un compte utilisateur de test est disponible :
- Email : jean.dupont@example.com
- Mot de passe : test123

## Crédits

Cette application utilise les bibliothèques suivantes :

- [Bootstrap](https://getbootstrap.com/) - Framework CSS
- [Font Awesome](https://fontawesome.com/) - Icônes
- [jQuery](https://jquery.com/) - Bibliothèque JavaScript
- [Dragula](https://github.com/bevacqua/dragula) - Bibliothèque de drag and drop 