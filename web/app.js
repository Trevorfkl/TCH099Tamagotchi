// =========================
// STORAGE KEYS
// =========================
const TOKEN_KEY = "token";
const USER_KEY = "utilisateur";

// =========================
// AUTH HELPERS
// =========================
function setToken(token) { localStorage.setItem(TOKEN_KEY, token); }
function getToken() { return localStorage.getItem(TOKEN_KEY); }
function removeToken() { localStorage.removeItem(TOKEN_KEY); }

function setCurrentUser(user) { localStorage.setItem(USER_KEY, JSON.stringify(user)); }
function getCurrentUser() {
    const raw = localStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
}

function requireAuth() {
    if (!getToken()) {
        window.location.href = "index.html";
        return false;
    }
    return true;
}

function logout() {
    removeToken();
    localStorage.removeItem(USER_KEY);
    window.location.href = "index.html";
}

// =========================
// API - UTILISATEURS (Connexion / Inscription)
// =========================
async function apiConnexion(email, password) {
    const reponse = await fetch('http://localhost:3000/api/connexion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || "Erreur de connexion");
    return data;
}

async function apiInscription(prenom, nom, email, password) {
    const reponse = await fetch('http://localhost:3000/api/inscription', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            firstName: prenom, 
            lastName: nom, 
            email: email, 
            password: password 
        })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || 'Erreur lors de l\'inscription');
    return data;
}

// =========================
// API - TÂCHES (CRUD)
// =========================
async function apiGetTaches() {
    const user = getCurrentUser();
    if (!user || !user.id) return [];
    const reponse = await fetch(`http://localhost:3000/api/taches/utilisateur/${user.id}`);
    if (!reponse.ok) return [];
    const donneesSQL = await reponse.json();

    return donneesSQL.map(tacheSQL => ({
        id: tacheSQL.id,
        nom: tacheSQL.titre,
        type: tacheSQL.type,
        date: new Date(tacheSQL.date_limite).toISOString().split('T')[0],
        completee: tacheSQL.statut === 'completee'
    }));
}

async function apiCreerTache(titre, type, date_limite) {
    const user = getCurrentUser();
    const reponse = await fetch('http://localhost:3000/api/taches', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ titre, type, date_limite, id_utilisateur: user.id })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    return data;
}

async function apiModifierTache(id_tache, titre, type, date_limite) {
    const user = getCurrentUser();
    const reponse = await fetch(`http://localhost:3000/api/taches/${id_tache}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ titre, type, date_limite, id_utilisateur: user.id })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    return data;
}

async function apiSupprimerTache(id_tache) {
    const reponse = await fetch(`http://localhost:3000/api/taches/${id_tache}`, { method: 'DELETE' });
    if (!reponse.ok) throw new Error('Erreur lors de la suppression');
    return true;
}

async function apiCompleterTache(id_tache) {
    const reponse = await fetch(`http://localhost:3000/api/taches/${id_tache}/toggle`, { method: 'PATCH' });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    return {
        completee: data.nouveauStatut === 'completee',
        coins: data.nouveauxCoins
    };
}

// =========================
// API - PROFIL (VRAIE CONNEXION)
// =========================
async function apiGetProfil() {
    const user = getCurrentUser();
    if (!user) throw new Error("Non connecté");

    // On récupère les données fraîches du serveur
    const reponse = await fetch(`http://localhost:3000/api/utilisateurs/${user.id}`);
    const data = await reponse.json();

    // Calcul des stats pour l'affichage (peut aussi être fait par le serveur)
    const tasks = await apiGetTaches();
    const completees = tasks.filter(t => t.completee).length;
    const progression = tasks.length > 0 ? Math.round((completees / tasks.length) * 100) : 0;

    return {
        prenom: data.prenom,
        nom: data.nom,
        email: data.courriel, // Traduction SQL -> HTML
        telephone: data.telephone || "",
        bio: data.biographie || "",
        couleur: data.couleur_profil || "#4a8a55",
        coins: data.coins || 0,
        stats: {
            total: tasks.length,
            completees: completees,
            progression: progression
        }
    };
}

async function apiUpdateProfil(prenom, nom, telephone, bio, couleur) {
    const user = getCurrentUser();
    const reponse = await fetch(`http://localhost:3000/api/utilisateurs/${user.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prenom, nom, telephone, biographie: bio, couleur_profil: couleur })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    
    // On met à jour le localStorage pour que le nom change partout instantanément
    setCurrentUser({ ...user, prenom, nom, coins: data.coins });
    return data;
}