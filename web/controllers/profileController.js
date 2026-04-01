const UtilisateurModel = require('../models/UtilisateurModel');
const TacheModel       = require('../models/TacheModel');
const CaseJardinModel  = require('../models/CaseJardinModel');

class ProfileController {
    // GET /api/profile
    static async obtenirProfil(req, res) {
        try {
            const utilisateur = await UtilisateurModel.obtenirParId(req.userId);
            if (!utilisateur) return res.status(404).json({ message: 'Utilisateur non trouvé' });
            return res.status(200).json({ message: 'Profil récupéré', utilisateur });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la récupération' });
        }
    }

    // PUT /api/profile
    static async mettreAJourProfil(req, res) {
        try {
            const utilisateur = await UtilisateurModel.obtenirParId(req.userId);
            if (!utilisateur) return res.status(404).json({ message: 'Utilisateur non trouvé' });

            const { prenom, nom, biographie } = req.body;
            await UtilisateurModel.mettreAJourProfil(
                req.userId,
                prenom     || utilisateur.prenom,
                nom        || utilisateur.nom,
                biographie !== undefined ? biographie : utilisateur.biographie
            );

            const utilisateurMaj = await UtilisateurModel.obtenirParId(req.userId);
            return res.status(200).json({ message: 'Profil mis à jour', utilisateur: utilisateurMaj });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la mise à jour' });
        }
    }

    // PUT /api/profile/color
    static async mettreAJourCouleur(req, res) {
        try {
            const { couleurProfil } = req.body;
            if (!couleurProfil) return res.status(400).json({ message: 'La couleur est requise' });

            const regex = /^#[0-9A-F]{6}$/i;
            if (!regex.test(couleurProfil)) {
                return res.status(400).json({ message: 'Format de couleur invalide (ex: #RRGGBB)' });
            }

            await UtilisateurModel.mettreAJourCouleur(req.userId, couleurProfil);
            const utilisateur = await UtilisateurModel.obtenirParId(req.userId);
            return res.status(200).json({ message: 'Couleur mise à jour', utilisateur });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la mise à jour' });
        }
    }

    // PUT /api/profile/coins
    static async deduireCoins(req, res) {
        try {
            const { montant } = req.body;
            if (!montant || montant <= 0) return res.status(400).json({ message: 'Montant invalide' });

            const utilisateur = await UtilisateurModel.obtenirParId(req.userId);
            if (!utilisateur) return res.status(404).json({ message: 'Utilisateur non trouvé' });
            if (utilisateur.coins < montant) return res.status(400).json({ message: 'Coins insuffisants' });

            await UtilisateurModel.retirerCoins(req.userId, montant);
            const utilisateurMaj = await UtilisateurModel.obtenirParId(req.userId);
            return res.status(200).json({ message: 'Coins déduits', utilisateur: utilisateurMaj });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la déduction' });
        }
    }

    // GET /api/profile/stats
    static async obtenirStats(req, res) {
        try {
            const utilisateur = await UtilisateurModel.obtenirParId(req.userId);
            if (!utilisateur) return res.status(404).json({ message: 'Utilisateur non trouvé' });

            const taches         = await TacheModel.obtenirParUtilisateur(req.userId);
            const tachesCompletees = taches.filter(t => t.statut === 'completee').length;
            const cases          = await CaseJardinModel.obtenirParUtilisateur(req.userId);
            const totalPlantes   = cases.filter(c => c.stage_croissance === 3).length;

            const stats = {
                tachesCompletees,
                totalTaches: taches.length,
                pourcentageCompletion: taches.length > 0 ? Math.round((tachesCompletees / taches.length) * 100) : 0,
                totalPlantes,
                totalCoins: utilisateur.coins,
                dateInscription: utilisateur.date_inscription
            };

            return res.status(200).json({ message: 'Statistiques récupérées', stats });
        } catch (e) {
            console.error(e);
            return res.status(500).json({ message: 'Erreur lors de la récupération des stats' });
        }
    }
}

module.exports = ProfileController;
