const db = require('../config/db');

class TacheModel {
    static async obtenirParUtilisateur(id_utilisateur) {
        const [rows] = await db.execute(
            'SELECT * FROM Tache WHERE id_utilisateur = ? ORDER BY date_limite ASC',
            [id_utilisateur]
        );
        return rows;
    }

    static async obtenirAujourdhui(id_utilisateur) {
        const [rows] = await db.execute(
            'SELECT * FROM Tache WHERE id_utilisateur = ? AND DATE(date_limite) = CURDATE() ORDER BY date_limite ASC',
            [id_utilisateur]
        );
        return rows;
    }

    static async obtenirParId(id, id_utilisateur) {
        const [rows] = await db.execute(
            'SELECT * FROM Tache WHERE id = ? AND id_utilisateur = ?',
            [id, id_utilisateur]
        );
        return rows[0] || null;
    }

    static async creer(titre, date_limite, id_utilisateur, id_case_jardin, description, type) {
        const [result] = await db.execute(
            'INSERT INTO Tache (titre, date_limite, id_utilisateur, id_case_jardin, description, type) VALUES (?, ?, ?, ?, ?, ?)',
            [titre, date_limite, id_utilisateur, id_case_jardin, description || null, type || 'etude']
        );
        return result.insertId;
    }

    static async mettreAJour(id, id_utilisateur, titre, date_limite, statut, description, type) {
        await db.execute(
            'UPDATE Tache SET titre = ?, date_limite = ?, statut = ?, description = ?, type = ? WHERE id = ? AND id_utilisateur = ?',
            [titre, date_limite, statut, description, type, id, id_utilisateur]
        );
    }

    static async supprimer(id, id_utilisateur) {
        const [result] = await db.execute(
            'DELETE FROM Tache WHERE id = ? AND id_utilisateur = ?',
            [id, id_utilisateur]
        );
        return result.affectedRows;
    }
}

module.exports = TacheModel;