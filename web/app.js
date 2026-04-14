// =========================
// CONSTANTES
// =========================
const API_URL   = 'http://localhost:3000/api';
const TOKEN_KEY = 'token';
const USER_KEY  = 'utilisateur';

// =========================
// AUTH HELPERS
// =========================
function setToken(token)  { localStorage.setItem(TOKEN_KEY, token); }
function getToken()       { return localStorage.getItem(TOKEN_KEY); }
function removeToken()    { localStorage.removeItem(TOKEN_KEY); }

function setCurrentUser(user) { localStorage.setItem(USER_KEY, JSON.stringify(user)); }
function getCurrentUser() {
    const raw = localStorage.getItem(USER_KEY);
    return raw ? JSON.parse(raw) : null;
}

function requireAuth() {
    if (!getToken()) {
        window.location.href = 'index.html';
        return false;
    }
    return true;
}

function logout() {
    removeToken();
    localStorage.removeItem(USER_KEY);
    window.location.href = 'index.html';
}

// Helper fetch avec Authorization Bearer
function authHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getToken()
    };
}

// =========================
// API - AUTHENTIFICATION
// =========================
async function apiConnexion(courriel, motDePasse) {
    const reponse = await fetch(`${API_URL}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ courriel, motDePasse })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || 'Erreur de connexion');
    return data;
}

async function apiInscription(prenom, nom, courriel, motDePasse) {
    const reponse = await fetch(`${API_URL}/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prenom, nom, courriel, motDePasse })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || 'Erreur lors de l\'inscription');
    return data;
}

// =========================
// API - TÂCHES (CRUD)
// =========================
async function apiGetTaches() {
    const reponse = await fetch(`${API_URL}/tasks`, { headers: authHeaders() });
    if (!reponse.ok) return [];
    const data = await reponse.json();
    const taches = data.taches || [];

    return taches.map(t => ({
        id:       t.id,
        nom:      t.titre,
        type:     t.type,
        date:     t.date_limite ? new Date(t.date_limite).toISOString().split('T')[0] : null,
        completee: t.statut === 'completee',
        description: t.description,
        icone:    t.icone
    }));
}

async function apiGetTachesAujourdhui() {
    const reponse = await fetch(`${API_URL}/tasks/today`, { headers: authHeaders() });
    if (!reponse.ok) return [];
    const data = await reponse.json();
    const taches = data.taches || [];

    return taches.map(t => ({
        id:       t.id,
        nom:      t.titre,
        type:     t.type,
        date:     t.date_limite ? new Date(t.date_limite).toISOString().split('T')[0] : null,
        completee: t.statut === 'completee',
        description: t.description,
        icone:    t.icone // L'icône est maintenant conservée !
    }));
}

async function apiGetTachesParDate(date) {
    const reponse = await fetch(`${API_URL}/tasks/date/${date}`, { headers: authHeaders() });
    if (!reponse.ok) return [];
    const data = await reponse.json();
    const taches = data.taches || [];

    return taches.map(t => ({
        id:       t.id,
        nom:      t.titre,
        type:     t.type,
        date:     t.date_limite ? new Date(t.date_limite).toISOString().split('T')[0] : null,
        completee: t.statut === 'completee',
        description: t.description,
        icone:    t.icone  // L'icône est maintenant conservée !
    }));
}


async function apiCreerTache(titre, type, dateLimite, icone, description = '') {
    const reponse = await fetch(`${API_URL}/tasks`, {
        method: 'POST',
        headers: authHeaders(),
        // On envoie bien l'icone ET la description au serveur !
        body: JSON.stringify({ titre, type, dateLimite, icone, description }) 
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    return data;
}

async function apiSupprimerTache(id) {
    const reponse = await fetch(`${API_URL}/tasks/${id}`, {
        method: 'DELETE',
        headers: authHeaders()
    });
    if (!reponse.ok) {
        const data = await reponse.json();
        throw new Error(data.message || 'Erreur lors de la suppression');
    }
    return true;
}

async function apiCompleterTache(id, currentStatut) {
    const nouveauStatut = currentStatut === 'completee' ? 'en_attente' : 'completee';
    const reponse = await fetch(`${API_URL}/tasks/${id}`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ statut: nouveauStatut })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    const tache = data.tache || {};
    return {
        completee: tache.statut === 'completee',
        coins: null // les coins sont mis à jour côté serveur, recharger le profil si besoin
    };
}

// =========================
// API - PROFIL
// =========================
async function apiGetProfil() {
    const reponse = await fetch(`${API_URL}/profile`, { headers: authHeaders() });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || 'Erreur profil');
    return data.utilisateur;
}

async function apiGetStats() {
    const reponse = await fetch(`${API_URL}/profile/stats`, { headers: authHeaders() });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || 'Erreur stats');
    return data.stats;
}

async function apiUpdateProfil(prenom, nom, biographie) {
    const reponse = await fetch(`${API_URL}/profile`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ prenom, nom, biographie })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);

    // Mettre à jour le localStorage
    const user = getCurrentUser();
    if (user) setCurrentUser({ ...user, prenom, nom });
    return data;
}

async function apiUpdateCouleur(couleurProfil) {
    const reponse = await fetch(`${API_URL}/profile/color`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ couleurProfil })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    return data;
}

async function apiDeduireCoins(montant) {
    const reponse = await fetch(`${API_URL}/profile/coins`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ montant })
    });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message);
    return data;
}

// =========================
// API - JARDIN
// =========================
async function apiGetJardin() {
    const reponse = await fetch(`${API_URL}/garden`, { headers: authHeaders() });
    const data = await reponse.json();
    if (!reponse.ok) throw new Error(data.message || 'Erreur jardin');
    return data.cases || [];
}