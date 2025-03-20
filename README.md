# Installing modx

#### Requirments:
- composer
- docker + docker-compose
- php

#### Step 1: run commands in terminal for install modx depends
1. install php lib - sudo apt install php-xml php-gd php-zip
1. run in project lib - composer install
2. move to dir in project - cd /_build
3. cp build.config.sample.php build.config.php
4. cp build.properties.sample.php build.properties.php
5. php transport.core.php

#### Step 2: run docker build and setup modx
1. run: docker-composer up -d
2. go to /setup page and fill all fields
3. finish setup and remove /setup folder

#### Step 3:
1. add .env file with API_KEY var for use api

#### Migrations:
1. You can't change old migrations → create new ones.
2. Make changes (ALTER TABLE) in separate migrations.
3. If you need to cancel the migration, do it manually via MySQL.