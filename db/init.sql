-- Création de la table 'categorie'
CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table 'technologie'
CREATE TABLE IF NOT EXISTS technologie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table 'ressource'
CREATE TABLE IF NOT EXISTS ressource (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    url VARCHAR(2048) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table d'association entre les technologies et les catégories
CREATE TABLE IF NOT EXISTS technologie_categorie (
    technologie_id INT NOT NULL,
    categorie_id INT NOT NULL,
    PRIMARY KEY (technologie_id, categorie_id),
    FOREIGN KEY (technologie_id) REFERENCES technologie(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table d'association entre les technologies et les ressources
CREATE TABLE IF NOT EXISTS technologie_ressource (
    technologie_id INT NOT NULL,
    ressource_id INT NOT NULL,
    PRIMARY KEY (technologie_id, ressource_id),
    FOREIGN KEY (technologie_id) REFERENCES technologie(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ressource_id) REFERENCES ressource(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;