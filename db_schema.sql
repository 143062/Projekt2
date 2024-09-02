-- db_schema.sql

-- Tworzenie tabeli ról użytkowników
CREATE TABLE Roles (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Dodanie domyślnych ról do tabeli Roles
INSERT INTO Roles (name) VALUES ('user'), ('admin');

-- Tworzenie tabeli użytkowników
CREATE TABLE Users (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    login VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default_profile_picture.jpg', -- Zmieniono domyślne rozszerzenie na .jpg
    role_id UUID REFERENCES Roles(id) ON DELETE SET NULL
);

-- Tworzenie tabeli notatek
CREATE TABLE Notes (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    user_id UUID REFERENCES Users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tworzenie tabeli znajomych
CREATE TABLE Friends (
    user_id UUID REFERENCES Users(id) ON DELETE CASCADE,
    friend_id UUID REFERENCES Users(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, friend_id)
);

-- Tworzenie tabeli udostępnionych notatek
CREATE TABLE Shared_Notes (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    note_id UUID REFERENCES Notes(id) ON DELETE CASCADE,
    shared_with_user_id UUID REFERENCES Users(id) ON DELETE CASCADE,
    shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
