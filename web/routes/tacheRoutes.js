const express = require('express');
const router = express.Router();
const TacheController  = require('../controllers/tacheController');
const authMiddleware   = require('../middleware/authMiddleware');

// Toutes les routes protégées par JWT
router.use(authMiddleware);

// GET /api/tasks
router.get('/', (req, res) => TacheController.obtenirTous(req, res));

// GET /api/tasks/today
router.get('/today', (req, res) => TacheController.obtenirAujourdhui(req, res));

// GET /api/tasks/date/:date
router.get('/date/:date', (req, res) => TacheController.obtenirParDate(req, res));

// POST /api/tasks
router.post('/', (req, res) => TacheController.creer(req, res));

// PUT /api/tasks/:id
router.put('/:id', (req, res) => TacheController.mettreAJour(req, res));

// DELETE /api/tasks/:id
router.delete('/:id', (req, res) => TacheController.supprimer(req, res));

module.exports = router;