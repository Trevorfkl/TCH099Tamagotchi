const db = require('../config/db');

class Tache {
    // Le paramètre "titre" va recevoir ce que l'utilisateur a tapé dans la case "Description" du HTML
    static async create(titre, type, date_limite, id_utilisateur) {
        const [result] = await db.execute(
            `INSERT INTO Tache (titre, type, date_limite, id_utilisateur) 
             VALUES (?, ?, ?, ?)`,
            [titre, type, date_limite, id_utilisateur]
        );
        return result.insertId;
    }
    // Ajoute ceci dans la classe Tache :
    static async update(id_tache, titre, type, date_limite, id_utilisateur) {
        // La condition "AND id_utilisateur = ?" empêche un utilisateur de modifier la tâche de quelqu'un d'autre !
        const [result] = await db.execute(
            `UPDATE Tache 
             SET titre = ?, type = ?, date_limite = ? 
             WHERE id = ? AND id_utilisateur = ?`,
            [titre, type, date_limite, id_tache, id_utilisateur]
        );
        return result.affectedRows; // Retourne le nombre de lignes modifiées (1 ou 0)
    }
    // À ajouter dans la classe Tache :
    static async delete(id_tache) {
        const [result] = await db.execute(
            'DELETE FROM Tache WHERE id = ?',
            [id_tache]
        );
        return result.affectedRows;
    }
    // À ajouter dans la classe Tache :
    static async findAllByUser(id_utilisateur) {
        const [rows] = await db.execute(
            'SELECT * FROM Tache WHERE id_utilisateur = ? ORDER BY date_limite ASC',
            [id_utilisateur]
        );
        return rows;
    }
}

module.exports = Tache;