# Femte Arenan

## Introduction
Femte Arenan is a personal website for Anders Björkland. It performs as a portfolio and api site for personal projects.
The project is built upon Symfony 5 and utilizes Doctrine.

## Deployment
How the project should be deployed depends on the hosting service. 
### One.com
Deploying to one.com requires SSH as the project needs to be split into private and public sub-categories.

#### Generate a .htdocs
#### Edit composer.json

origin | destination
--- | ---
/public/ | httpd.www/
/composer.json | httpd.www/
