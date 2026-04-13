const BoutiqueModel = require('../models/BoutiqueModel');
const UtilisateurModel = require('../models/UtilisateurModel');

class BoutiqueController {
    static async getShopPublic(req, res) {
        try {
            const items = await BoutiqueModel.obtenirVisibles(req.userId);
            return res.status(200).json({ items });
        } catch (e) { return res.status(500).json({ message: 'Erreur shop' }); }
    }

    static async getAllAdmin(req, res) {
        try {
            const items = await BoutiqueModel.obtenirTous();
            return res.status(200).json({ items });
        } catch (e) { return res.status(500).json({ message: 'Erreur admin' }); }
    }

    static async getInventaire(req, res) {
        try {
            const inventaire = await BoutiqueModel.obtenirInventaire(req.userId);
            return res.status(200).json({ inventaire });
        } catch (e) { return res.status(500).json({ message: 'Erreur inventaire' }); }
    }

    static async create(req, res) {
        try {
            const { nom, type, prix, milestone, image, est_visible } = req.body;
            if (!nom) return res.status(400).json({ message: 'Nom obligatoire.' });
            await BoutiqueModel.creer(nom, type, prix, milestone, image, est_visible !== false);
            return res.status(201).json({ message: 'Article créé !' });
        } catch (e) { return res.status(500).json({ message: 'Erreur création.' }); }
    }

    static async toggleVisibility(req, res) {
        try {
            await BoutiqueModel.basculerVisibilite(req.params.id, req.body.est_visible);
            return res.status(200).json({ message: 'Visibilité modifiée' });
        } catch (e) { return res.status(500).json({ message: 'Erreur' }); }
    }

    static async delete(req, res) {
        try {
            await BoutiqueModel.supprimer(req.params.id);
            return res.status(200).json({ message: 'Supprimé' });
        } catch (e) { return res.status(500).json({ message: 'Erreur' }); }
    }

    static async acheter(req, res) {
        try {
            const id_article = req.params.id;
            const id_utilisateur = req.userId;

            const article = await BoutiqueModel.obtenirParId(id_article);
            if (!article) return res.status(404).json({ message: "Article introuvable." });

            const possede = await BoutiqueModel.verifierPossession(id_utilisateur, id_article);
            if (possede) return res.status(400).json({ message: "Tu possèdes déjà cet article !" });

            const user = await UtilisateurModel.obtenirParId(id_utilisateur);
            if (user.coins < article.prix_coins) return res.status(400).json({ message: "Fonds insuffisants. 💰" });

            if (article.condition_taches_completees > 0) {
                const taches = await BoutiqueModel.compterTachesCompletees(id_utilisateur);
                if (taches < article.condition_taches_completees) {
                    return res.status(400).json({ message: `Bloqué 🔒: Requiert ${article.condition_taches_completees} tâches.` });
                }
            }

            await BoutiqueModel.executerAchat(id_utilisateur, id_article, article.prix_coins);
            return res.status(200).json({ message: "Achat réussi ! 🎉" });

        } catch (e) { return res.status(500).json({ message: "Erreur achat." }); }
    }
}
module.exports = BoutiqueController;