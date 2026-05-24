# Assu_Motor
L'application gère les demande de souscription des assurance automobile de tout types de véhicule.

# Installation du projet Symfony (docker) AssuMotor

## Prérequis
- Docker
- Docker Compose

## Conteneurs utilisés
assuMotor_php
assuMotor_nginx
assuMotor_mysql
assuMotor_phpmyadmin
assuMotor_mailhog

## Étapes d’installation

1. Cloner le dépôt

git clone https://github.com/MohandBir/Assu_Motor.git
cd Assu_Motor

2. Démarrer les conteneurs

Vérifier que les ports ne sont pas déjà utilisés

docker-compose up -d --build

3. Installer les dépendances Symfony

docker exec -it assuMotor_php composer install

4. Configurer l’environnement
Créer le fichier .env.local :

cp .env .env.local
Vérifier la configuration de la base de données :

DATABASE_URL="mysql://user:pwd@mysql:3306/assuMotor?serverVersion=8.0.32&charset=utf8mb4"
MAILER_DSN=smtp://mailhog:1025
MESSENGER_TRANSPORT_DSN=sync://

## Commandes utiles
Accéder au conteneur PHP :
docker exec -it assuMotor_php sh
Voir les logs :
docker-compose logs -f
Arrêter les conteneurs :
docker-compose down


## Suivi de projet
Lien Jira : https://birmoho-1775809868476.atlassian.net/jira/software/projects/PP/boards/34
