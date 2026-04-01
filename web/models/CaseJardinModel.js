const db = require('../config/db');

class CaseJardinModel {
    static async obtenirParUtilisateur(id_utilisateur) {
        const [rows] = await db.execute(
            'SELECT cj.*, p.nom AS nom_plante, p.image_stage1, p.image_stage2, p.image_stage3 FROM CaseJardin cj LEFT JOIN Plante p ON cj.id_plante = p.id WHERE cj.id_utilisateur = ? ORDER BY cj.numero_jour ASC',
            [id_utilisateur]
        );
        return rows;
    }

    static async obtenirParId(id, id_utilisateur) {
        const [rows] = await db.execute(
            'SELECT * FROM CaseJardin WHERE id = ? AND id_utilisateur = ?',
            [id, id_utilisateur]
        );
        return rows[0] || null;
    }

    static async obtenirParNumeroDuJour(id_utilisateur, numero_jour) {
        const [rows] = await db.execute(
            'SELECT * FROM CaseJardin WHERE id_utilisateur = ? AND numero_jour = ?',
            [id_utilisateur, numero_jour]
        );
        return rows[0] || null;
    }

    static async creer(id_utilisateur, numero_jour, total_taches, id_plante) {
        const [result] = await db.execute(
            'INSERT INTO CaseJardin (id_utilisateur, numero_jour, total_taches, id_plante) VALUES (?, ?, ?, ?)',
            [id_utilisateur, numero_jour, total_taches, id_plante]
        );
        return result.insertId;
    }

    static async mettreAJourTotal(id, total_taches) {
        await db.execute('UPDATE CaseJardin SET total_taches = ? WHERE id = ?', [total_taches, id]);
    }

    // Met à jour les tâches complétées et recalcule le stage
    // Formule : minimum 3 tâches totales, chaque tier = floor(total/3) tâches complétées
    // Ex: 3 tâches → tier=1 → 1 complétée=stage1, 2=stage2, 3=stage3
    // Ex: 6 tâches → tier=2 → 2 complétées=stage1, 4=stage2, 6=stage3
    // Le total réel est recompté depuis la BD pour corriger total_taches si incohérent
    static async mettreAJourProgression(id, id_utilisateur, taches_completees) {
        const caseJardin = await CaseJardinModel.obtenirParId(id, id_utilisateur);
        if (!caseJardin) return null;

        // Compter le vrai total de tâches liées à cette case (source de vérité)
        const [rowsTotal] = await db.execute(
            'SELECT COUNT(*) AS total FROM Tache WHERE id_case_jardin = ?',
            [id]
        );
        const totalUtilise = rowsTotal[0].total;

        let nouveauStage = 0;
        if (totalUtilise >= 3) {
            const tier = Math.floor(totalUtilise / 3);
            nouveauStage = Math.min(3, Math.floor(taches_completees / tier));
        }

        await db.execute(
            'UPDATE CaseJardin SET taches_completees = ?, stage_croissance = ?, total_taches = ? WHERE id = ? AND id_utilisateur = ?',
            [taches_completees, nouveauStage, totalUtilise, id, id_utilisateur]
        );
        return nouveauStage;
    }
}

module.exports = CaseJardinModel;
