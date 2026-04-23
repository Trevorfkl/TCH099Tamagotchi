const express = require('express');
const router = express.Router();
const GardenController = require('../controllers/gardenController');
const authMiddleware   = require('../middleware/authMiddleware');

router.use(authMiddleware);

// GET /api/garden
router.get('/', (req, res) => GardenController.obtenirJardin(req, res));

// POST /api/garden
router.post('/', (req, res) => GardenController.creerCase(req, res));

// PUT /api/garden/:id/progress
router.put('/:id/progress', (req, res) => GardenController.mettreAJourProgression(req, res));

// DELETE /api/garden/:id/plant
router.delete('/:id/plant', (req, res) => GardenController.supprimerPlante(req, res));

module.exports = router;
