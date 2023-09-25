-- Création de la table 'categorie'
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

-- Création de la table 'technologie'
CREATE TABLE technologie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    logo VARCHAR(255),
    categorie_id INT,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id)
);

-- Création de la table 'ressource'
CREATE TABLE ressource (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    technologie_id INT,
    FOREIGN KEY (technologie_id) REFERENCES technologie(id)
);