const Tache = require('../models/TacheModel');

exports.ajouterTache = async (req, res) => {
    // On extrait exactement ce que app.js envoie
    const { titre, type, date_limite, id_utilisateur } = req.body;

    if (!titre || !id_utilisateur) {
        return res.status(400).json({ message: 'Titre et utilisateur requis.' });
    }

    try {
        const newTacheId = await Tache.create(titre, type, date_limite, id_utilisateur);
        res.status(201).json({ message: 'Activité ajoutée !', tacheId: newTacheId });
    } catch (error) {
        res.status(500).json({ message: 'Erreur lors de la création.' });
    }
};

// Ajoute ceci en bas du fichier :
exports.modifierTache = async (req, res) => {
    // On récupère l'ID depuis l'URL (ex: /taches/5 -> req.params.id = 5)
    const id_tache = req.params.id; 
    
    // On récupère les nouvelles données depuis le fetch()
    const { titre, type, date_limite, id_utilisateur } = req.body;

    if (!titre || !id_utilisateur) {
        return res.status(400).json({ message: 'Titre et identifiant utilisateur requis.' });
    }

    try {
        const lignesModifiees = await Tache.update(id_tache, titre, type, date_limite, id_utilisateur);

        if (lignesModifiees === 0) {
            return res.status(404).json({ message: 'Tâche introuvable ou vous n\'avez pas la permission de la modifier.' });
        }

        res.status(200).json({ message: 'Activité mise à jour avec succès !' });

    } catch (error) {
        console.error('Erreur SQL:', error);
        res.status(500).json({ message: 'Erreur lors de la modification de la tâche.' });
    }
};
exports.supprimerTache = async (req, res) => {
    const id_tache = req.params.id;

    try {
        const lignesSupprimees = await Tache.delete(id_tache);
        if (lignesSupprimees === 0) {
            return res.status(404).json({ message: 'Tâche introuvable.' });
        }
        res.status(200).json({ message: 'Activité supprimée avec succès !' });
    } catch (error) {
        console.error('Erreur SQL:', error);
        res.status(500).json({ message: 'Erreur lors de la suppression.' });
    }
};
exports.getTachesUtilisateur = async (req, res) => {
    const id_utilisateur = req.params.id_user;

    try {
        const taches = await Tache.findAllByUser(id_utilisateur);
        res.status(200).json(taches);
    } catch (error) {
        console.error('Erreur SQL:', error);
        res.status(500).json({ message: 'Erreur lors de la récupération des tâches.' });
    }
};