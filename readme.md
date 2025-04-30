# La Place des Mutuelles

💬 Un projet de forum communautaire dédié aux adhérents de mutuelles, pour désengorger les hotlines et centraliser les questions fréquentes.

## Objectifs

- Créer un espace d'échange entre adhérents autour des problématiques courantes (remboursements, CG, carte de tiers payant…).
- Offrir une interface claire, simple et utile.
- Tester et faire évoluer **EasyFrameWork**, un micro-framework PHP développé maison.

## Technologies

- PHP (EasyFrameWork)
- HTML / CSS / JavaScript
- MySQL
- Hébergement sur serveur personnel (Debian + Apache/Nginx)

## Architecture du projet

Le projet s'appuie sur une structure MVC maison, organisée comme suit :

- `public/` : point d’entrée du site et ressources front-end.
- `app/` : logique métier (contrôleurs, appels async, entités SQL, etc.)
- `vendor/easyFrameWork/` : micro-framework PHP développé en interne.
  - `core/` : classes cœur (routeur, controller, base, vue...)
     - `config/` : configurations spécifiques du framework
     - `master/` : gestion de l'initialisation du framework

## Installation locale

```bash
git clone https://github.com/ton-compte/place-des-mutuelles.git
cd place-des-mutuelles
cp .env.example .env
composer install
