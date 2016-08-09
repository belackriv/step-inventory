<?php

// All Deployer recipes are based on `recipe/common.php`.
require 'recipe/symfony.php';

after('deploy:vendors', 'database:migrate');

// Define a server for deployment.
// Let's name it "prod" and use port 22.
server('demo', '138.68.29.149', 22)
    ->user('deployer')
    ->password(null)
    ->forwardAgent() // You can use identity key, ssh config, or username/password to auth on the server.
    ->stage('production')
    ->env('deploy_path', '/var/www/step-inventory'); // Define the base path to deploy your project to.

// Specify the repository from which to download your project's code.
// The server needs to have git installed for this to work.
// If you're not using a forward agent, then the server has to be able to clone
// your project from this repository.
set('repository', 'git@github.com:belackriv/step-inventory.git');