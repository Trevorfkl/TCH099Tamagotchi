const express = require('express');
const router = express.Router();
const tacheController = require('../controllers/tacheController');

router.post('/taches', tacheController.ajouterTache);
// Ajoute cette ligne sous ton router.post(...) :
router.delete('/taches/:id', tacheController.supprimerTache);
// Route PUT pour modifier une tâche existante
router.put('/taches/:id', tacheController.modifierTache);
router.get('/taches/utilisateur/:id_user', tacheController.getTachesUtilisateur);
module.exports = router;