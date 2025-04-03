CREATE TABLE IF NOT EXISTS Entraineur (
                                          idEntraineur INT PRIMARY KEY AUTO_INCREMENT,
                                          nom VARCHAR(50) NOT NULL,
                                          prenom VARCHAR(50) NOT NULL,
                                          email VARCHAR(50) NOT NULL,
                                          equipe VARCHAR(20) NOT NULL,
                                          motDePasse VARCHAR(60) NOT NULL
);
ALTER TABLE Entraineur CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;