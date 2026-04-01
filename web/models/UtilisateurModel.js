const db = require('../config/db');

class Utilisateur {
    // Cherche un utilisateur par son courriel
    static async findByEmail(email) {
        const [rows] = await db.execute(
            'SELECT * FROM Utilisateur WHERE courriel = ?',
            [email]
        );
        return rows;
    }

    // Crée un nouvel utilisateur
    static async create(prenom, nom, courriel, mot_de_passe) {
        const [result] = await db.execute(
            'INSERT INTO Utilisateur (prenom, nom, courriel, mot_de_passe) VALUES (?, ?, ?, ?)',
            [prenom, nom, courriel, mot_de_passe]
        );
        return result.insertId; // Retourne l'ID généré par AUTO_INCREMENT
    }
    static async findById(id) {
    const [rows] = await db.execute('SELECT * FROM Utilisateur WHERE id = ?', [id]);
    return rows[0];
}

static async updateProfile(id, data) {
    const { prenom, nom, telephone, biographie, couleur_profil } = data;
    await db.execute(
        `UPDATE Utilisateur 
         SET prenom = ?, nom = ?, biographie = ?, couleur_profil = ? 
         WHERE id = ?`,
        [prenom, nom, biographie, couleur_profil, id]
    );
}
}

module.exports = Utilisateur;