const express = require('express');
const router = express.Router();
const AuthController = require('../controllers/utilisateurController');

// POST /api/auth/register
router.post('/register', (req, res) => AuthController.register(req, res));

// POST /api/auth/login
router.post('/login', (req, res) => AuthController.login(req, res));

// POST /api/auth/forgot-password
router.post('/forgot-password', (req, res) => AuthController.forgotPassword(req, res));

module.exports = router;