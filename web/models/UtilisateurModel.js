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

    // ==========================================
    // LEADERBOARD (Classement public)
    // ==========================================
    static async obtenirLeaderboard(periode, typeTache) {
        let conditionSQL = '';
        const params = []; // Tableau sécurisé pour les variables SQL
        
        // Filtres SQL basés sur la date
        if (periode === '7jours') {
            conditionSQL += ' AND t.date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
        } else if (periode === '30jours') {
            conditionSQL += ' AND t.date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
        } else if (periode === 'annee') {
            conditionSQL += ' AND YEAR(t.date_creation) = YEAR(NOW())';
        }

        // NOUVEAU : Filtre par type de tâche
        if (typeTache && typeTache !== 'toutes') {
            conditionSQL += ' AND t.type = ?';
            params.push(typeTache);
        }

        const query = `
            SELECT 
                u.id, u.prenom, u.nom, u.coins, u.couleur_profil,
                COUNT(t.id) AS total_taches,
                COALESCE(SUM(CASE WHEN t.statut = 'completee' THEN 1 ELSE 0 END), 0) AS taches_completees
            FROM Utilisateur u
            LEFT JOIN Tache t ON u.id = t.id_utilisateur ${conditionSQL}
            GROUP BY u.id
        `;
        
        // On passe les params à db.execute pour plus de sécurité
        const [rows] = await db.execute(query, params);
        return rows;
    }
    // ==========================================
    // FONCTIONS POUR LE PANNEAU ADMIN
    // ==========================================

    // Cette fonction fait le calcul en direct pour que les colonnes se mettent à jour !
    static async obtenirTous() {
        const query = `
            SELECT 
                u.id, u.prenom, u.nom, u.courriel, u.role, u.coins, u.date_inscription,
                COUNT(t.id) AS total_taches,
                COALESCE(SUM(CASE WHEN t.statut = 'completee' THEN 1 ELSE 0 END), 0) AS taches_completees,
                COALESCE(SUM(CASE WHEN t.statut IN ('en_attente', 'en_cours') THEN 1 ELSE 0 END), 0) AS taches_en_cours
            FROM Utilisateur u
            LEFT JOIN Tache t ON u.id = t.id_utilisateur
            GROUP BY u.id
        `;
        const [rows] = await db.execute(query);
        return rows;
    }

    static async mettreAJourRole(id, role) {
        await db.execute('UPDATE Utilisateur SET role = ? WHERE id = ?', [role, id]);
    }

    static async supprimer(id) {
        await db.execute('DELETE FROM Utilisateur WHERE id = ?', [id]);
    }

    static async mettreAJourIcone(id, icone) {
        await db.execute('UPDATE Utilisateur SET icone_profil = ? WHERE id = ?', [icone, id]);
    }

    static async equiperItem(id, type, valeur) {
        let colonne = '';
        if (type === 'icone_profil') colonne = 'icone_profil';
        else if (type === 'theme') colonne = 'theme_equipe';
        else if (type === 'plante') colonne = 'plante_equipee';
        else throw new Error("Type d'équipement invalide");

        await db.execute(`UPDATE Utilisateur SET ${colonne} = ? WHERE id = ?`, [valeur, id]);
    }

}



module.exports = UtilisateurModel;