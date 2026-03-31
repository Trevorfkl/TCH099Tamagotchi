const express = require('express');
const cors = require('cors');
const utilisateurRoutes = require('./routes/utilisateurRoutes');
const tacheRoutes = require('./routes/tacheRoutes');
const app = express();

// Middlewares
app.use(express.json());
app.use(cors());
app.use('/api', tacheRoutes);
// On connecte les routes liées aux utilisateurs sous le préfixe /api
app.use('/api', utilisateurRoutes);

// Démarrage
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Serveur API démarré sur le port ${PORT} 🌱`);
});