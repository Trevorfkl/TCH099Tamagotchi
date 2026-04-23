// Middleware pour vérifier le JWT et protéger les routes
const jwt = require('jsonwebtoken');

const authMiddleware = (req, res, next) => {
    try {
        const authHeader = req.headers['authorization'];
        if (!authHeader) {
            return res.status(401).json({ message: 'Token manquant' });
        }

        const parts = authHeader.split(' ');
        if (parts.length !== 2 || parts[0] !== 'Bearer') {
            return res.status(401).json({ message: 'Format du token invalide' });
        }

        const token = parts[1];
        const decoded = jwt.verify(token, 'growchi-secret-key');

        req.userId = decoded.userId;
        req.user   = decoded;
        next();
    } catch (erreur) {
        console.error('Erreur authentification:', erreur.message);
        return res.status(401).json({ message: 'Token invalide ou expiré' });
    }
};

module.exports = authMiddleware;
