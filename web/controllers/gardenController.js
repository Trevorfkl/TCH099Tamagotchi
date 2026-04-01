const CaseJardinModel  = require('../models/CaseJardinModel');
const pool             = require('../config/db');

class GardenController {
    // GET /api/garden
    static async obtenirJardin(req, res) {
        try {
            const cases = await CaseJardinModel.obtenirParUtilisateur(req.userId);
            return res.status(200).json({ message: 'Jardin récupéré', cases });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la récupération du jardin' });
        }
    }

    // POST /api/garden
    // Si une case existe déjà pour ce jour (créée automatiquement lors de la création des tâches),
    // on met à jour la plante plutôt que créer un doublon.
    static async creerCase(req, res) {
        try {
            const { numeroJour, idPlante } = req.body;
            if (!numeroJour) {
                return res.status(400).json({ message: 'Le numéro de jour est requis' });
            }

            let caseJardin = await CaseJardinModel.obtenirParNumeroDuJour(req.userId, numeroJour);
            if (caseJardin) {
                // Case existante : on y assigne juste la plante
                const conn = await pool.getConnection();
                await conn.query(
                    'UPDATE CaseJardin SET id_plante = ? WHERE id = ? AND id_utilisateur = ?',
                    [idPlante || null, caseJardin.id, req.userId]
                );
                conn.release();
                caseJardin = await CaseJardinModel.obtenirParId(caseJardin.id, req.userId);
            } else {
                // Aucune case pour ce jour : créer
                const idCase = await CaseJardinModel.creer(req.userId, numeroJour, 0, idPlante || null);
                caseJardin = await CaseJardinModel.obtenirParId(idCase, req.userId);
            }

            return res.status(201).json({ message: 'Case créée avec succès', caseJardin });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la création' });
        }
    }

    // PUT /api/garden/:id/progress
    static async mettreAJourProgression(req, res) {
        try {
            const { id } = req.params;
            const { tachesCompletees } = req.body;
            const nouveauStage = await CaseJardinModel.mettreAJourProgression(id, req.userId, tachesCompletees);
            const caseJardin = await CaseJardinModel.obtenirParId(id, req.userId);
            return res.status(200).json({ message: 'Progression mise à jour', caseJardin, nouveauStage });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la mise à jour' });
        }
    }

    // DELETE /api/garden/:id/plant
    static async supprimerPlante(req, res) {
        try {
            const { id } = req.params;
            const pool = require('../config/db');
            const conn = await pool.getConnection();
            await conn.query(
                'UPDATE CaseJardin SET id_plante = NULL, stage_croissance = 0, taches_completees = 0 WHERE id = ? AND id_utilisateur = ?',
                [id, req.userId]
            );
            conn.release();
            return res.status(200).json({ message: 'Plante retirée de la case' });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la suppression de la plante' });
        }
    }
}

module.exports = GardenController;
