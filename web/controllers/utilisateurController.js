const UtilisateurModel = require('../models/UtilisateurModel');
const bcrypt = require('bcryptjs');
const jwt    = require('jsonwebtoken');

const SECRET = 'growchi-secret-key';

class AuthController {
    // POST /api/auth/register
    static async register(req, res) {
        try {
            const { prenom, nom, courriel, motDePasse } = req.body;

            if (!prenom || !nom || !courriel || !motDePasse) {
                return res.status(400).json({ message: 'Tous les champs sont requis' });
            }

            if (!courriel.includes('@')) {
                return res.status(400).json({ message: 'Veuillez fournir une adresse courriel valide.' });
            }

            const existant = await UtilisateurModel.obtenirParEmail(courriel);
            if (existant) {
                return res.status(409).json({ message: 'Cet email est déjà utilisé' });
            }

            const motDePasseHashe = await bcrypt.hash(motDePasse, 10);
            const idUtilisateur   = await UtilisateurModel.creer(prenom, nom, courriel, motDePasseHashe);

            const token = jwt.sign(
                { userId: idUtilisateur, prenom, nom },
                SECRET,
                { expiresIn: '24h' }
            );

            return res.status(201).json({
                message: 'Utilisateur créé avec succès',
                token,
                utilisateur: { id: idUtilisateur, prenom, nom, courriel }
            });
        } catch (erreur) {
            console.error('Erreur register:', erreur);
            return res.status(500).json({ message: 'Erreur lors de l\'enregistrement' });
        }
    }

    // POST /api/auth/login
    static async login(req, res) {
        try {
            const { courriel, motDePasse } = req.body;

            if (!courriel || !motDePasse) {
                return res.status(400).json({ message: 'Email et mot de passe requis' });
            }

            const utilisateur = await UtilisateurModel.obtenirParEmail(courriel);
            if (!utilisateur) {
                return res.status(401).json({ message: 'Email ou mot de passe incorrect' });
            }

            const valide = await bcrypt.compare(motDePasse, utilisateur.mot_de_passe);
            if (!valide) {
                return res.status(401).json({ message: 'Email ou mot de passe incorrect' });
            }

            const token = jwt.sign(
                { userId: utilisateur.id, prenom: utilisateur.prenom, nom: utilisateur.nom },
                SECRET,
                { expiresIn: '24h' }
            );

            return res.status(200).json({
                message: 'Connexion réussie',
                token,
                utilisateur: {
                    id: utilisateur.id,
                    prenom: utilisateur.prenom,
                    nom: utilisateur.nom,
                    courriel: utilisateur.courriel,
                    coins: utilisateur.coins,
                    role: utilisateur.role
                }
            });
        } catch (erreur) {
            console.error('Erreur login:', erreur);
            return res.status(500).json({ message: 'Erreur lors de la connexion' });
        }
    }

    // POST /api/auth/forgot-password
    static async forgotPassword(req, res) {
        try {
            const { courriel } = req.body;
            if (!courriel) {
                return res.status(400).json({ message: 'Email requis' });
            }
            // Réponse neutre (sécurité — ne pas révéler si l'email existe)
            return res.status(200).json({
                message: 'Si cet email existe dans notre système, vous recevrez un email de réinitialisation'
            });
        } catch (erreur) {
            return res.status(500).json({ message: 'Erreur lors du traitement' });
        }
    }
    // GET /api/auth/users (Admin seulement)
    static async getAllUsers(req, res) {
        try {
            // Optionnel : Tu pourrais vérifier ici si req.user.role === 'admin'
            const users = await UtilisateurModel.obtenirTous();
            return res.status(200).json({ users });
        } catch (erreur) {
            return res.status(500).json({ message: 'Erreur lors de la récupération des utilisateurs' });
        }
    }

    // PUT /api/auth/users/:id/role
    static async updateUserRole(req, res) {
        try {
            const { id } = req.params;
            const { role } = req.body;
            await UtilisateurModel.mettreAJourRole(id, role);
            return res.status(200).json({ message: 'Rôle mis à jour' });
        } catch (erreur) {
            return res.status(500).json({ message: 'Erreur lors de la mise à jour' });
        }
    }

    // DELETE /api/auth/users/:id
    static async deleteUser(req, res) {
        try {
            const { id } = req.params;
            await UtilisateurModel.supprimer(id);
            return res.status(200).json({ message: 'Utilisateur supprimé' });
        } catch (erreur) {
            return res.status(500).json({ message: 'Erreur lors de la suppression' });
        }
    }

    // PUT /api/auth/users/:id/coins
    static async updateUserCoins(req, res) {
        try {
            const { id } = req.params;
            const { montant } = req.body; 

            if (montant > 0) {
                await UtilisateurModel.ajouterCoins(id, montant);
            } else if (montant < 0) {
                // On utilise Math.abs pour envoyer un nombre positif à la fonction retirerCoins
                await UtilisateurModel.retirerCoins(id, Math.abs(montant));
            }

            return res.status(200).json({ message: 'Jetons mis à jour' });
        } catch (erreur) {
            console.error(erreur);
            return res.status(500).json({ message: 'Erreur lors de la modification des jetons' });
        }
    }
    // GET /api/auth/leaderboard
    // GET /api/auth/leaderboard
    static async getLeaderboard(req, res) {
        try {
            const periode = req.query.periode || 'toujours';
            const typeTache = req.query.type || 'toutes'; // <-- NOUVEAU

            // On passe les deux paramètres au modèle
            const leaderboard = await UtilisateurModel.obtenirLeaderboard(periode, typeTache);
            return res.status(200).json({ leaderboard });
        } catch (erreur) {
            console.error('Erreur leaderboard:', erreur);
            return res.status(500).json({ message: 'Erreur lors de la récupération du classement' });
        }
    }
}

module.exports = AuthController;