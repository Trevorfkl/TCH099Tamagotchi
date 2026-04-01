const db = require('../config/db');

class UtilisateurModel {
    static async obtenirParEmail(courriel) {
        const [rows] = await db.execute('SELECT * FROM Utilisateur WHERE courriel = ?', [courriel]);
        return rows[0] || null;
    }

    static async obtenirParId(id) {
        const [rows] = await db.execute('SELECT * FROM Utilisateur WHERE id = ?', [id]);
        return rows[0] || null;
    }

    static async creer(prenom, nom, courriel, mot_de_passe) {
        const [result] = await db.execute(
            'INSERT INTO Utilisateur (prenom, nom, courriel, mot_de_passe) VALUES (?, ?, ?, ?)',
            [prenom, nom, courriel, mot_de_passe]
        );
        return result.insertId;
    }

    static async mettreAJourProfil(id, prenom, nom, biographie) {
        await db.execute(
            'UPDATE Utilisateur SET prenom = ?, nom = ?, biographie = ? WHERE id = ?',
            [prenom, nom, biographie, id]
        );
    }

    static async mettreAJourCouleur(id, couleurProfil) {
        await db.execute('UPDATE Utilisateur SET couleur_profil = ? WHERE id = ?', [couleurProfil, id]);
    }

    static async ajouterCoins(id, montant) {
        await db.execute('UPDATE Utilisateur SET coins = coins + ? WHERE id = ?', [montant, id]);
    }

    static async retirerCoins(id, montant) {
        await db.execute('UPDATE Utilisateur SET coins = GREATEST(0, coins - ?) WHERE id = ?', [montant, id]);
    }
}

module.exports = UtilisateurModel;