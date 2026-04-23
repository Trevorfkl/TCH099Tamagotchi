const TacheModel        = require('../models/TacheModel');
const UtilisateurModel  = require('../models/UtilisateurModel');
const CaseJardinModel   = require('../models/CaseJardinModel');

class TacheController {
    // GET /api/tasks
    static async obtenirTous(req, res) {
        try {
            const taches = await TacheModel.obtenirParUtilisateur(req.userId);
            return res.status(200).json({ message: 'Tâches récupérées', taches });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la récupération des tâches' });
        }
    }

    // GET /api/tasks/today
    static async obtenirAujourdhui(req, res) {
        try {
            const taches = await TacheModel.obtenirAujourdhui(req.userId);
            return res.status(200).json({ message: 'Tâches d\'aujourd\'hui récupérées', taches });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la récupération' });
        }
    }

    // GET /api/tasks/date/:date
    static async obtenirParDate(req, res) {
        try {
            const { date } = req.params;
            const pool = require('../config/db');
            const conn = await pool.getConnection();
            const [rows] = await conn.query(
                'SELECT * FROM Tache WHERE id_utilisateur = ? AND DATE(date_limite) = ? ORDER BY date_limite ASC',
                [req.userId, date]
            );
            conn.release();
            return res.status(200).json({ message: 'Tâches récupérées', taches: rows });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la récupération' });
        }
    }

    // POST /api/tasks
    static async creer(req, res) {
        try {
            const { titre, dateLimite, idCaseJardin, description, type } = req.body;

            if (!titre) {
                return res.status(400).json({ message: 'Le titre est requis' });
            }

            const idTache = await TacheModel.creer(
                titre,
                dateLimite || null,
                req.userId,
                idCaseJardin || null,
                description,
                type
            );

            // Lier la tâche à la case du jour correspondant
            let numeroJour;
            if (dateLimite) {
                numeroJour = parseInt(dateLimite.split('-')[2], 10);
            } else {
                numeroJour = new Date().getDate();
            }

            let caseJardin = await CaseJardinModel.obtenirParNumeroDuJour(req.userId, numeroJour);
            const pool = require('../config/db');

            if (!caseJardin) {
                const idCase = await CaseJardinModel.creer(req.userId, numeroJour, 1, null);
                caseJardin = await CaseJardinModel.obtenirParId(idCase, req.userId);
            } else {
                await CaseJardinModel.mettreAJourTotal(caseJardin.id, caseJardin.total_taches + 1);
            }

            if (caseJardin) {
                const conn = await pool.getConnection();
                await conn.query('UPDATE Tache SET id_case_jardin = ? WHERE id = ?', [caseJardin.id, idTache]);
                conn.release();
            }

            const tache = await TacheModel.obtenirParId(idTache, req.userId);
            return res.status(201).json({ message: 'Tâche créée avec succès', tache });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la création' });
        }
    }

    // PUT /api/tasks/:id
    static async mettreAJour(req, res) {
        try {
            const { id } = req.params;
            const { titre, dateLimite, statut, description, type } = req.body;

            const tache = await TacheModel.obtenirParId(id, req.userId);
            if (!tache) return res.status(404).json({ message: 'Tâche non trouvée' });

            await TacheModel.mettreAJour(
                id, req.userId,
                titre       || tache.titre,
                dateLimite  || tache.date_limite,
                statut      || tache.statut,
                description !== undefined ? description : tache.description,
                type        || tache.type
            );

            // Coins +10 si tâche complétée
            if (statut === 'completee' && tache.statut !== 'completee') {
                await UtilisateurModel.ajouterCoins(req.userId, 10);

                if (tache.id_case_jardin) {
                    const caseJardin = await CaseJardinModel.obtenirParId(tache.id_case_jardin, req.userId);
                    if (caseJardin) {
                        const tachesCompletees = caseJardin.taches_completees + 1;
                        const nouveauStage = await CaseJardinModel.mettreAJourProgression(
                            caseJardin.id, req.userId, tachesCompletees
                        );
                        // Bonus +50 si plante mature (stage 3)
                        if (nouveauStage === 3 && caseJardin.stage_croissance < 3 && caseJardin.total_taches >= 3) {
                            await UtilisateurModel.ajouterCoins(req.userId, 50);
                        }
                    }
                }
            }

            // Coins -10 si tâche décomplétée (en_cours depuis mobile OU en_attente depuis web)
            if (statut && statut !== 'completee' && tache.statut === 'completee') {
                await UtilisateurModel.retirerCoins(req.userId, 10);

                if (tache.id_case_jardin) {
                    const caseJardin = await CaseJardinModel.obtenirParId(tache.id_case_jardin, req.userId);
                    if (caseJardin && caseJardin.taches_completees > 0) {
                        await CaseJardinModel.mettreAJourProgression(
                            caseJardin.id, req.userId, caseJardin.taches_completees - 1
                        );
                    }
                }
            }

            const tacheMaj = await TacheModel.obtenirParId(id, req.userId);
            return res.status(200).json({ message: 'Tâche mise à jour', tache: tacheMaj });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la mise à jour' });
        }
    }

    // DELETE /api/tasks/:id
    static async supprimer(req, res) {
        try {
            const { id } = req.params;
            const tache = await TacheModel.obtenirParId(id, req.userId);
            if (!tache) return res.status(404).json({ message: 'Tâche non trouvée' });

            await TacheModel.supprimer(id, req.userId);

            // Retirer coins si la tâche était complétée
            if (tache.statut === 'completee') {
                await UtilisateurModel.retirerCoins(req.userId, 10);
            }

            // Toujours recalculer total_taches et stage après suppression
            // (mettreAJourProgression recompte le vrai total depuis la BD)
            if (tache.id_case_jardin) {
                const caseJardin = await CaseJardinModel.obtenirParId(tache.id_case_jardin, req.userId);
                if (caseJardin) {
                    const tachesCompleteesMaj = tache.statut === 'completee'
                        ? Math.max(0, caseJardin.taches_completees - 1)
                        : caseJardin.taches_completees;
                    await CaseJardinModel.mettreAJourProgression(
                        caseJardin.id, req.userId, tachesCompleteesMaj
                    );
                }
            }

            return res.status(200).json({ message: 'Tâche supprimée avec succès' });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la suppression' });
        }
    }
}

module.exports = TacheController;