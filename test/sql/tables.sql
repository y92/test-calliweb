DROP TABLE IF EXISTS Tokens;

DROP TABLE IF EXISTS Addresses;

DROP TABLE IF EXISTS Users;

CREATE TABLE IF NOT EXISTS Tokens(
    token VARCHAR(255) PRIMARY KEY,
    createdOn DATETIME NOT NULL DEFAULT NOW(),
    validUntil DATETIME
);

CREATE TABLE IF NOT EXISTS Users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    registeredOn DATETIME NOT NULL DEFAULT NOW(),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS Addresses(
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner INT NOT NULL,
    addedOn DATETIME NOT NULL DEFAULT NOW(),
    prefix VARCHAR(255),
    lastName VARCHAR(255),
    firstName VARCHAR(255),
    email VARCHAR(255),
    phoneNumber VARCHAR(255),
    addrL1 VARCHAR(255),
    addrL2 VARCHAR(255),
    postalCode VARCHAR(255),
    city VARCHAR(255),
    country VARCHAR(255),
    FOREIGN KEY (owner) REFERENCES Users(id) ON DELETE CASCADE
)