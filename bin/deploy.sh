#!/bin/bash
echo "Déploiement en cours..."

cd /var/www/place-des-mutuelles
git pull origin main
composer install --no-dev
php migrate.php # ou autre script de mise à jour DB

echo "Déploiement terminé."
