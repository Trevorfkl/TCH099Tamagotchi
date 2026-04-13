const express = require('express');
const router = express.Router();
const ProfileController = require('../controllers/profileController');
const authMiddleware    = require('../middleware/authMiddleware');

router.use(authMiddleware);

// GET /api/profile
router.get('/', (req, res) => ProfileController.obtenirProfil(req, res));

// PUT /api/profile
router.put('/', (req, res) => ProfileController.mettreAJourProfil(req, res));

// PUT /api/profile/color
router.put('/color', (req, res) => ProfileController.mettreAJourCouleur(req, res));

// PUT /api/profile/coins
router.put('/coins', (req, res) => ProfileController.deduireCoins(req, res));

// GET /api/profile/stats
router.get('/stats', (req, res) => ProfileController.obtenirStats(req, res));

// PUT /api/profile/test-coins
router.put('/test-coins', (req, res) => ProfileController.ajouterTestCoins(req, res));

router.put('/icone', authMiddleware, (req, res) => ProfileController.updateIcone(req, res));
router.put('/equiper', authMiddleware, (req, res) => ProfileController.equiper(req, res));
router.get('/taches-actives', authMiddleware, (req, res) => ProfileController.getTachesActives(req, res));
router.put('/tache/:id/plante', authMiddleware, (req, res) => ProfileController.equiperPlanteTache(req, res));
router.put('/taches-type/plante', authMiddleware, (req, res) => ProfileController.equiperPlanteParType(req, res));
module.exports = router;
