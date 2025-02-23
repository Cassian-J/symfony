# Habit Tracker - Symfony Project

## Description
Ce projet est un tracker d'habitudes gamifié inspiré du site Habitica, développé avec le framework PHP Symfony. Il permet aux utilisateurs de créer des comptes, de rejoindre des groupes et de suivre leurs habitudes pour gagner ou perdre des points en fonction de leur complétion.

## Fonctionnalités principales
- **Gestion des comptes utilisateurs** :
  - Création de compte avec pseudo ou email
  - Connexion sécurisée avec mot de passe hashé
  - Upload d'une photo de profil (taille < 1Mo)
- **Gestion des groupes d'amis** :
  - Création et gestion d'un groupe unique par utilisateur
  - Invitations et acceptation d'autres membres dans un groupe
  - Possibilité de quitter un groupe
- **Ajout et suivi des habitudes** :
  - Ajout d'habitudes quotidiennes ou hebdomadaires
  - Association d'une couleur, d'un niveau de difficulté et d'une cible (individu ou groupe)
  - Un utilisateur peut ajouter une habitude par jour
  - Un chef de groupe peut ajouter une habitude pour toute l'équipe
- **Système de points et suivi des performances** :
  - Attribution de points en fonction de la difficulté des tâches
  - Pénalité en cas de non-accomplissement des tâches
  - Affichage des points gagnés/perdus à chaque connexion
  - Dissolution du groupe si le score atteint 0
- **Journalisation des actions et impact du temps** :
  - Historique des points gagnés et perdus stocké en base de données
  - Calcul des événements à la connexion des utilisateurs

## Installation
### Prérequis
- PHP >= 8.0
- Symfony CLI
- Composer
- Un serveur MySQL ou MariaDB
- Un environnement de développement local comme XAMPP, WAMP ou Docker (optionnel)

### Étapes d'installation
1. Cloner le dépôt :
   ```bash
   git clone <URL_du_dépôt>
   cd symfony-Ymmersion
   ```
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
décompressé le fichier vendor.zip qui se trouve dans le fichier assets ( si un fichier vendor n'existe pas déjà)
3. Configurer la base de données :
   - Créer la base de données et exécuter les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
4. Lancer le serveur Symfony :
    pour le lancer en local:
   ```bash
   symfony server 
   ```
   sinon :
    ```bash
   symfony server --allow-all-ip
   ```

## Utilisation
1. Accéder à l'application
2. Créer un compte et se connecter
3. Créer ou rejoindre un groupe d'amis
4. Ajouter et accomplir des habitudes pour accumuler des points
5. Surveiller l'évolution du score et éviter la dissolution du groupe !

