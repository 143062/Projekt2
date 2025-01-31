  <img src="https://raw.githubusercontent.com/143062/Projekt2/main/app-laravel/public/img/logo.svg" alt="Logo" width="200">


## Contents
- [App description](#app-description)
- [Let's start!](#lets-start)
- [Database](#database)
- [Technologies Used](#technologies-used)
- [Swagger Documentation](#swagger-documentation)
- [Screenshots](#screenshots)
- [Choices](#choices)


## App description
N is an application that allows you to create notes and easily share them with your friends! 

## Let's start!

### 1. Clone Repository
To clone repository you can use the command below:
```shell
git clone https://github.com/143062/Projekt.git
```

### 2. Run Docker Image
You need to have [Docker](https://www.docker.com/) installed on your environment  
Move to the project's directory and run the command below:

```shell
docker-compose up --build
```
### 3. Access PHP Container

To access use this command:
```shell
docker exec -it projekt2-php-1 sh
```

### 4. Migration

To run miration use this command:
```shell
php artisan migrate:fresh
```

### 5. Seeders 

Seeders are already included in the code, to run all of them use this command:

```shell
php artisan db:seed
```

However, they aren't defined perfectly, I'd recommend running only those 3 seeders for clean environment:

```shell
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=UserSeeder
```


### 6. Logging in:

There are predefinied accounts already included in Seeders 

#### Admin Role:
<details>
  <summary>Admin</summary>

```shell
Login: admin
Password: adminadmin
```

</details>

#### User Role:
<details>
  <summary>User</summary>

```shell
Login: user
Password: useruser
```
</details>



## Database

### ERD Diagram
![ERD Diagram](https://i.imgur.com/RTKmqNq.png)

### Schema
View [db_schema.sql](https://github.com/143062/Projekt2/blob/main/app-laravel/database/schema/db_schema.sql)


## Technologies used
- Laravel (11.31) + Eloquent
- Sanctum (4.0)
- Swagger (8.6)
- RabbitMQ (3.13.7) + Laravel-queue-rabbitmq (14.1)
- Git
- Docker
- HTML
- CSS
- JavaScript
- PHP
- PostgreSQL

## Swagger documentation

To access documentation, visit link:

```shell
http://localhost:8080/api/docs#/
```


## Screenshots

### Dashboard
| Desktop       | Mobile     |
|:-------------:|:------------:|
|![Dashboard Main Desktop](https://i.imgur.com/ucmAE3l.png) | ![Dashboard Main Mobile](https://i.imgur.com/HYqgvvG.png)

### Profile
| Desktop       | Mobile     |
|:-------------:|:------------:|
|![Profile Main Desktop](https://i.imgur.com/KyRcnSp.png) | ![Profile Main Mobile](https://i.imgur.com/ZW9BFhX.png)


### Login Page

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Login Desktop](https://i.imgur.com/rrAu8RI.png) | ![Login Mobile](https://i.imgur.com/rgNtW6l.png)


### Register Page

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Register Desktop](https://i.imgur.com/hs4mTUG.png) | ![Register Mobile](https://i.imgur.com/JkFYzz8.png)


### Dashboard - Adding Note

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Adding Note Desktop](https://i.imgur.com/NX7v19K.png) | ![Adding Note Mobile](https://i.imgur.com/LiiMhfT.png)


### Dashboard - Viewing Note

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Viewing Note Desktop](https://i.imgur.com/nqb2AAj.png) | ![Viewing Note Mobile](https://i.imgur.com/RU8Nes4.png)


### Dashboard - Editing Friends

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Editing Friends Desktop](https://i.imgur.com/IBESfLB.png) | ![Editing Friends Mobile](https://i.imgur.com/GqeG1JL.png)


### Profile - Managing Friends

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Managing Friends Desktop](https://i.imgur.com/eb1Qyjr.png) | ![Managing Friends Mobile](https://i.imgur.com/oLq1oqa.png)


### Profile - Changing Profile Picture

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Changing Profile Picture Desktop](https://i.imgur.com/3kfsxdl.png) | ![Changing Profile Picture Mobile](https://i.imgur.com/1BXJsYS.png)




### Admin Panel

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Admin Panel Desktop](https://i.imgur.com/ObfIPKR.png) | ![Admin Panel Mobile](https://i.imgur.com/Rjboas2.png)



## Choices

The architecture, based on REST API, RabbitMQ, and containerization, makes the project modular, easy to maintain, and ready for future development. Laravel ensures stability and strong support for modern technologies.


### CHOICE OF FRAMEWORK

The project is built using Laravel as the main backend framework.

Why Laravel?

🔹 Eloquent ORM – A simple and efficient way to manage the database using an object-relational model.

🔹 Sanctum – A built-in API authentication system, ideal for REST API integration.

🔹 Modularity – Laravel allows easy feature expansion (e.g., integration with RabbitMQ).

🔹 Queue & Events – Supports asynchronous tasks (e.g., sending emails after registration).

🔹 Swagger – Built-in API documentation facilitates testing and development.

🔹 Docker Support – Laravel works well with containerization, making environment management easier.

With these features, Laravel provides fast development, stability, and easy code management.


### JUSTIFICATION FOR ARCHITECTURAL CHOICES

🔹 REST API-Based Architecture

The project separates the backend (Laravel) from the frontend (pure PHP, HTML, CSS, JS).

A REST API makes it easy to expand the project to other platforms (e.g., a mobile app).

🔹 Separation of Responsibilities

- Backend (Laravel + PostgreSQL)

      Handles authentication (Sanctum).

      Manages users, notes, and friends.

      Uses RabbitMQ for task queuing (e.g., sending emails).

- Frontend (Pure PHP, HTML, JS, CSS)

      Communicates with the API using AJAX / Fetch API.

      No frontend frameworks for simplicity and performance.

🔹 Handling Asynchronous Tasks

RabbitMQ is used for sending emails after registration, preventing backend blocking.

Queues allow efficient handling of a large number of users in the future.

🔹 Containerization (Docker)

Docker + docker-compose ensures easy environment setup.

Separate containers for PHP, PostgreSQL, Nginx, RabbitMQ, improving scalability.