# A generic docker containers for PHP project
###### This project provide an ability to build up a development environment to run any pure PHP codebase as well as some popular PHP frameworks: Laravel and Symfony.

### Supported Software Stack (Containers)

- **Database Engine:**
	- MySQL
- **PHP Server:**
	- NGINX
- **PHP Compiler:**
	- PHP-FPM
- **Tool:**
	- PhpMyAdmin

## Quick Start
- Put your PHP codebase into the `application` directory.

- Create `env` with a content from `env.example` file which is located in the root directory and update your desire environment configuration.

```bash
cp env.example env
```

- Run the following command in the root directory where contains the `docker-compose.yml` file

```bash
docker-compose up -d
```

## Configuration Variables
All the configuration variables are mandatory.

- `APP_CODE_PATH_HOST`: Point to the path of your applications code on your host
- `APP_CODE_PATH_CONTAINER`: Point to where the `APP_CODE_PATH_HOST` should be in the container
- `MYSQL_VERSION`: MYSQL version.
- `MYSQL_DATABASE`: MYSQL database.
- `MYSQL_PASSWORD`: MYSQL password
- `PMA_PORT`: phpmyadmin port.

### phpMyAdmin Usage

1 - Ensure the `phpmyadmin` container has been start along with at least `mysql` container

2 - Open your browser and visit the localhost on port **8080**:  `http://localhost:8080`
