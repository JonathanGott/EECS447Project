-- Movie Review System Database Schema
-- Group 29: Ben Haney, Kai Barnhart, Jonathan Gott

-- Users table
CREATE TABLE IF NOT EXISTS User (
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movies table
CREATE TABLE IF NOT EXISTS Movie (
    Movie_ID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(200) NOT NULL,
    Genre VARCHAR(50),
    Release_Year INT,
    Director VARCHAR(100)
);

-- Reviews table
CREATE TABLE IF NOT EXISTS Review (
    Review_ID INT AUTO_INCREMENT PRIMARY KEY,
    Rating INT NOT NULL CHECK (Rating BETWEEN 1 AND 10),
    Review_Text TEXT,
    Review_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    User_ID INT NOT NULL,
    Movie_ID INT NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES User(User_ID) ON DELETE CASCADE,
    FOREIGN KEY (Movie_ID) REFERENCES Movie(Movie_ID) ON DELETE CASCADE
);

-- Sample movie data
INSERT INTO Movie (Title, Genre, Release_Year, Director) VALUES
('The Shawshank Redemption', 'Drama', 1994, 'Frank Darabont'),
('The Godfather', 'Crime', 1972, 'Francis Ford Coppola'),
('The Dark Knight', 'Action', 2008, 'Christopher Nolan'),
('Pulp Fiction', 'Crime', 1994, 'Quentin Tarantino'),
('Forrest Gump', 'Drama', 1994, 'Robert Zemeckis'),
('Inception', 'Sci-Fi', 2010, 'Christopher Nolan'),
('The Matrix', 'Sci-Fi', 1999, 'The Wachowskis'),
('Goodfellas', 'Crime', 1990, 'Martin Scorsese'),
('Interstellar', 'Sci-Fi', 2014, 'Christopher Nolan'),
('Fight Club', 'Drama', 1999, 'David Fincher');