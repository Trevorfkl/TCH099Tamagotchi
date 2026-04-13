const db = require('../config/db');

class BoutiqueModel {
    static async obtenirTous() {
        const [rows] = await db.execute('SELECT * FROM ArticleBoutique');
        return rows;
    }

    static async obtenirVisibles(id_utilisateur) {
        const query = `
            SELECT a.*, 
                   IF(i.id_article IS NOT NULL, 1, 0) AS possede 
            FROM ArticleBoutique a
            LEFT JOIN InventaireUtilisateur i ON a.id = i.id_article AND i.id_utilisateur = ?
            WHERE a.est_visible = TRUE
        `;
        const [rows] = await db.execute(query, [id_utilisateur]);
        return rows;
    }

    static async obtenirInventaire(id_utilisateur) {
        const query = `
            SELECT a.*, i.date_achat 
            FROM InventaireUtilisateur i
            JOIN ArticleBoutique a ON i.id_article = a.id
            WHERE i.id_utilisateur = ?
            ORDER BY i.date_achat DESC
        `;
        const [rows] = await db.execute(query, [id_utilisateur]);
        return rows;
    }

    static async creer(nom, type, prix_coins, condition_taches, image, est_visible = true) {
        let imageUrl = image || '🎁';
        const [result] = await db.execute(
            'INSERT INTO ArticleBoutique (nom, description, type, prix_coins, condition_taches_completees, image_url, est_visible) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [nom, 'Nouvel article', type, Number(prix_coins) || 0, Number(condition_taches) || 0, imageUrl, est_visible]
        );
        return result.insertId;
    }

    static async basculerVisibilite(id, estVisible) {
        await db.execute('UPDATE ArticleBoutique SET est_visible = ? WHERE id = ?', [estVisible, id]);
    }

    static async supprimer(id) {
        await db.execute('DELETE FROM ArticleBoutique WHERE id = ?', [id]);
    }

    static async obtenirParId(id) {
        const [rows] = await db.execute('SELECT * FROM ArticleBoutique WHERE id = ?', [id]);
        return rows[0];
    }

    static async verifierPossession(id_utilisateur, id_article) {
        const [rows] = await db.execute('SELECT * FROM InventaireUtilisateur WHERE id_utilisateur = ? AND id_article = ?', [id_utilisateur, id_article]);
        return rows.length > 0;
    }

    static async compterTachesCompletees(id_utilisateur) {
        const [rows] = await db.execute('SELECT COUNT(*) as total FROM Tache WHERE id_utilisateur = ? AND statut = "completee"', [id_utilisateur]);
        return rows[0].total;
    }

    static async executerAchat(id_utilisateur, id_article, prix) {
        await db.execute('UPDATE Utilisateur SET coins = GREATEST(0, coins - ?) WHERE id = ?', [prix, id_utilisateur]);
        await db.execute('INSERT INTO InventaireUtilisateur (id_utilisateur, id_article) VALUES (?, ?)', [id_utilisateur, id_article]);
    }
}
module.exports = BoutiqueModel;