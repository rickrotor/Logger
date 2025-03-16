# Installing modx

#### Requirments:
- composer
- docker + docker-compose

#### Step 1: run commands in terminal for install modx depends
1. composer install
2. cd www/_build
3. cp build.config.sample.php build.config.php
4. cp build.properties.sample.php build.properties.php
5. php transport.core.php

#### Step 2:
1. run: docker-composer up -d
2. go to /setup page and fill all fields
3. finish setup and remove /setup folder

#### Migrations:
1. You can't change old migrations → create new ones.
2. Make changes (ALTER TABLE) in separate migrations.
3. If you need to cancel the migration, do it manually via MySQL.