# Assu_Motor

## 📋 Présentation

**Assu_Motor** est une application web développée avec **Symfony** permettant de gérer les demandes de souscription à des assurances automobiles pour différents types de véhicules.

L'application permet de centraliser les demandes de souscription et de faciliter leur gestion dans le cadre du processus d'assurance automobile.

---

# 🐳 Installation du projet Symfony avec Docker

## Prérequis

Pour installer et exécuter le projet, les éléments suivants sont nécessaires :

* Docker
* Docker Compose
* Git

---

## Conteneurs utilisés

Le projet utilise plusieurs conteneurs Docker afin de séparer les différents services nécessaires au fonctionnement de l'application :

* **assuMotor_php** : conteneur contenant l'application PHP/Symfony.
* **assuMotor_nginx** : serveur web Nginx permettant d'accéder à l'application.
* **assuMotor_mysql** : conteneur contenant la base de données MySQL.
* **assuMotor_phpmyadmin** : interface permettant d'administrer la base de données MySQL.
* **assuMotor_mailhog** : service utilisé pour tester les emails envoyés par l'application en environnement de développement.

---

# 🚀 Étapes d'installation

## 1. Cloner le dépôt

Cloner le projet depuis GitHub puis se placer dans le répertoire du projet :

```bash
git clone https://github.com/MohandBir/Assu_Motor.git
cd Assu_Motor
```

## 2. Démarrer les conteneurs

Avant de démarrer le projet, vérifier que les ports utilisés par Docker ne sont pas déjà occupés par d'autres applications.

Construire les images et démarrer les différents conteneurs :

```bash
docker-compose up -d --build
```

Cette commande permet de construire les images nécessaires et de démarrer l'ensemble des services en arrière-plan.

## 3. Installer les dépendances Symfony

Une fois les conteneurs démarrés, installer les dépendances du projet avec Composer :

```bash
docker exec -it assuMotor_php composer install
```

## 4. Configurer l'environnement

Créer le fichier `.env.local` à partir du fichier `.env` :

```bash
cp .env .env.local
```

Vérifier ensuite les différentes variables d'environnement nécessaires au fonctionnement de l'application.

### Base de données

```env
DATABASE_URL="mysql://user:pwd@mysql:3306/assuMotor?serverVersion=8.0.32&charset=utf8mb4"
```

### MailHog

```env
MAILER_DSN=smtp://mailhog:1025
```

### Messenger

```env
MESSENGER_TRANSPORT_DSN=sync://
```

---

# 🛠️ Commandes utiles

### Accéder au conteneur PHP

```bash
docker exec -it assuMotor_php sh
```

Cette commande permet d'ouvrir un terminal directement dans le conteneur PHP.

### Voir les logs

Pour suivre les logs des différents conteneurs :

```bash
docker-compose logs -f
```

### Arrêter les conteneurs

Pour arrêter et supprimer les conteneurs du projet :

```bash
docker-compose down
```

---

# 📌 Suivi du projet

Le suivi des tâches et de l'avancement du projet est réalisé avec Jira.

**Jira :**
