const express = require('express');
const router = express.Router();
const AuthController = require('../controllers/utilisateurController');
const authMiddleware = require('../middleware/authMiddleware');

// POST /api/auth/register
router.post('/register', (req, res) => AuthController.register(req, res));

// POST /api/auth/login
router.post('/login', (req, res) => AuthController.login(req, res));

// POST /api/auth/forgot-password
router.post('/forgot-password', (req, res) => AuthController.forgotPassword(req, res));

router.get('/users', authMiddleware, (req, res) => AuthController.getAllUsers(req, res));
router.put('/users/:id/role', authMiddleware, (req, res) => AuthController.updateUserRole(req, res));
router.delete('/users/:id', authMiddleware, (req, res) => AuthController.deleteUser(req, res));
router.put('/users/:id/coins', authMiddleware, (req, res) => AuthController.updateUserCoins(req, res));
// Route Leaderboard (Accessible à tous les connectés)
router.get('/leaderboard', authMiddleware, (req, res) => AuthController.getLeaderboard(req, res));
module.exports = router;