const express = require('express');
const router = express.Router();
const utilisateurController = require('../controllers/utilisateurController');

// Définition de la route POST
router.post('/inscription', utilisateurController.inscrireUtilisateur);
router.post('/connexion', utilisateurController.connecterUtilisateur);
module.exports = router;