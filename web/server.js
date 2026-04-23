const express = require('express');
const cors = require('cors');
const pool = require('./config/db');

const authRoutes    = require('./routes/utilisateurRoutes'); // renommé authController en interne
const tacheRoutes   = require('./routes/tacheRoutes');
const gardenRoutes  = require('./routes/gardenRoutes');
const profileRoutes = require('./routes/profileRoutes');

const boutiqueRoutes = require('./routes/boutiqueRoutes');

const app = express();

// Middlewares
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Fichiers statiques (HTML, CSS, JS frontend)
app.use(express.static(__dirname));

// Journalisation
app.use((req, res, next) => {
    console.log(`${new Date().toISOString()} - ${req.method} ${req.path}`);
    next();
});

// Routes
app.use('/api/auth',    authRoutes);
app.use('/api/tasks',   tacheRoutes);
app.use('/api/garden',  gardenRoutes);
app.use('/api/profile', profileRoutes);
app.use('/api/boutique', boutiqueRoutes);

// Santé API
app.get('/api/health', (req, res) => {
    res.status(200).json({ message: 'API Growchi en fonctionnement', timestamp: new Date().toISOString() });
});

// Route non trouvée
app.use((req, res) => {
    res.status(404).json({ message: 'Route non trouvée' });
});

// Erreur globale
app.use((err, req, res, next) => {
    console.error('Erreur non gérée:', err);
    res.status(500).json({ message: 'Erreur interne du serveur' });
});

// Démarrage
const PORT = process.env.PORT || 3000;

async function demarrer() {
    try {
        const conn = await pool.getConnection();
        console.log('✓ Connexion MySQL établie');
        conn.release();
        app.listen(PORT, () => {
            console.log(`✓ Serveur Growchi démarré sur le port ${PORT}`);
            console.log(`✓ Santé API : http://localhost:${PORT}/api/health`);
        });
    } catch (err) {
        console.error('✗ Erreur de démarrage:', err);
        process.exit(1);
    }
}

demarrer();
module.exports = app;