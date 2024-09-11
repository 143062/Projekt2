# N

## Contents
- [App description](#app-description)
- [Let's start!](#lets-start)

- [Database](#database)

- [Technologies Used](#technologies-used)
- [Screenshots](#screenshots)







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
### 3. Import database

To import database you can just simply open your browser and paste this link:
```shell
http://localhost:8080/import_database
```
It uses the newest database from app/Database/dumps/  
Required file is already included in the Repository

### 4. Logging in:

There are predefinied accounts already included with the imported database.

#### Admin Role:
<details>
  <summary>Admin</summary>

```shell
Login: admin
Password: admin
```

</details>

#### User Role:
<details>
  <summary>Scout</summary>

```shell
Login: scout
Password: scout
```

</details>

<details>
  <summary>Heavy</summary>

```shell
Login: heavy
Password: heavy
```

</details>

<details>
  <summary>Medic</summary>

```shell
Login: medic
Password: medic
```

</details>

<details>
  <summary>Spy</summary>

```shell
Login: spy
Password: spy
```

</details>

<details>
  <summary>Sniper</summary>

```shell
Login: sniper
Password: sniper
```

</details>


## Database

### ERD Diagram
![ERD Diagram](https://i.imgur.com/RTKmqNq.png)

### Schema
View [db_schema.sql](https://github.com/143062/Projekt/blob/main/Database/db_schema.sql)


## Technologies used
- Git
- Docker
- HTML
- CSS
- JavaScript
- PHP
- PostgreSQL


## Screenshots

### Dashboard
| Desktop       | Mobile     |
|:-------------:|:------------:|
|![Dashboard Main Desktop](https://i.imgur.com/ucmAE3l.png) | ![Dashboard Main Mobile](https://i.imgur.com/HYqgvvG.png)

### Profile
| Desktop       | Mobile     |
|:-------------:|:------------:|
|![Profile Main Desktop](https://i.imgur.com/KyRcnSp.png) | ![Profile Main Mobile](https://i.imgur.com/ZW9BFhX.png)

<details>
  <summary>Login Page</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Login Desktop](https://i.imgur.com/rrAu8RI.png) | ![Login Mobile](https://i.imgur.com/rgNtW6l.png)

</details>

<details>
  <summary>Register Page</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Register Desktop](https://i.imgur.com/hs4mTUG.png) | ![Register Mobile](https://i.imgur.com/JkFYzz8.png)

</details>

<details>
  <summary>Dashboard - Adding Note</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Adding Note Desktop](https://i.imgur.com/example.png) | ![Adding Note Mobile](https://i.imgur.com/example.png)

</details>

<details>
  <summary>Dashboard - Viewing Note</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Viewing Note Desktop](https://i.imgur.com/example.png) | ![Viewing Note Mobile](https://i.imgur.com/example.png)

</details>

<details>
  <summary>Dashboard - Editing Friends</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Editing Friends Desktop](https://i.imgur.com/example.png) | ![Editing Friends Mobile](https://i.imgur.com/example.png)

</details>

<details>
  <summary>Profile - Managing Friends</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Managing Friends Desktop](https://i.imgur.com/example.png) | ![Managing Friends Mobile](https://i.imgur.com/example.png)

</details>

<details>
  <summary>Profile - Changing Profile Picture</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Changing Profile Picture Desktop](https://i.imgur.com/example.png) | ![Changing Profile Picture Mobile](https://i.imgur.com/example.png)

</details>

<details>
  <summary>Admin Panel</summary>

  | Desktop       | Mobile     |
  |:-------------:|:------------:|
  |![Admin Panel Desktop](https://i.imgur.com/example.png) | ![Admin Panel Mobile](https://i.imgur.com/example.png)

</details>




