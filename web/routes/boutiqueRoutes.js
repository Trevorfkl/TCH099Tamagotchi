const express = require('express');
const router = express.Router();
const BoutiqueController = require('../controllers/boutiqueController');
const authMiddleware = require('../middleware/authMiddleware');

// 1. Les routes spécifiques EN PREMIER
router.get('/admin', authMiddleware, (req, res) => BoutiqueController.getAllAdmin(req, res));
router.get('/inventaire', authMiddleware, (req, res) => BoutiqueController.getInventaire(req, res));

// 2. Les routes générales
router.get('/', authMiddleware, (req, res) => BoutiqueController.getShopPublic(req, res));
router.post('/', authMiddleware, (req, res) => BoutiqueController.create(req, res));

// 3. Les routes avec des paramètres (:id) TOUJOURS À LA FIN
router.post('/:id/acheter', authMiddleware, (req, res) => BoutiqueController.acheter(req, res));
router.put('/:id/visibility', authMiddleware, (req, res) => BoutiqueController.toggleVisibility(req, res));
router.delete('/:id', authMiddleware, (req, res) => BoutiqueController.delete(req, res));

module.exports = router;