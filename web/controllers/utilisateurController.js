const bcrypt = require('bcrypt');
const Utilisateur = require('../models/UtilisateurModel');

exports.connecterUtilisateur = async (req, res) => {
    const { email, password } = req.body;

    try {
        // 1. Chercher l'utilisateur par son courriel
        const users = await Utilisateur.findByEmail(email);
        
        if (users.length === 0) {
            return res.status(401).json({ message: "Utilisateur non trouvé." });
        }

        const user = users[0];

        // 2. Comparer le mot de passe avec le hash en BD
        const match = await bcrypt.compare(password, user.mot_de_passe);

        if (!match) {
            return res.status(401).json({ message: "Mot de passe incorrect." });
        }

        // 3. Succès : on renvoie les infos (sans le mot de passe !)
        res.status(200).json({
            message: "Connexion réussie !",
            token: "vrai-token-jwt-bientôt",
            utilisateur: {
                id: user.id,
                prenom: user.prenom,
                nom: user.nom,
                email: user.courriel,
                coins: user.coins
            }
        });

    } catch (error) {
        console.error(error);
        res.status(500).json({ message: "Erreur technique lors de la connexion." });
    }
};