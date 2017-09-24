<?php

require 'recipe/symfony3.php';

task('deploy:asset_symlinks', function () {
	run("cd {{release_path}}/web && ln -sfn {{release_path}}/assets assets"); // Atomic override symlink.
	run("cd {{release_path}}/web && ln -sfn {{release_path}}/assets/jspm_packages jspm_packages"); // Atomic override symlink.
})->desc('Creating aseets symlinks to assets');

set('repository', 'git@github.com:belackriv/step-inventory.git');

server('demo', '138.68.29.149', 22)
    ->user('deployer')
    ->identityFile()
    ->stage('demo')
    ->env('deploy_path', '/var/www/step-inventory');

before('deploy:cache:clear', 'database:migrate');
before('deploy:symlink', 'deploy:asset_symlinks');