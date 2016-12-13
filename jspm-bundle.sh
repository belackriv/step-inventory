#!/bin/bash
jspm bundle lib/main.js - [lib/**/*] -  [lib/**/*.hbs] assets/deps.js
jspm bundle lib/main.js assets/main-bundle.js
jspm bundle lib/admin/controller.js assets/admin-bundle.js
jspm bundle lib/inventory/controller.js assets/inventory-bundle.js