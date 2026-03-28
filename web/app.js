
// Growchi - Utilitaire API REST



const API_URL = 'https://growchi.page.gd/api';

// ---- Token ----
function getToken() {
    return sessionStorage.getItem('growchi_token');
}
function setToken(token) {
    sessionStorage.setItem('growchi_token', token);
}
function removeToken() {
    sessionStorage.removeItem('growchi_token');
    sessionStorage.removeItem('growchi_user');
}

// ---- Utilisateur courant ----
function getCurrentUser() {
    const u = sessionStorage.getItem('growchi_user');
    return u ? JSON.parse(u) : null;
}
function setCurrentUser(user) {
    sessionStorage.setItem('growchi_user', JSON.stringify(user));
}

// ---- Requête générique avec token ----
async function apiRequest(endpoint, method = 'GET', body = null) {
    const headers = { 'Content-Type': 'application/json' };
    const token = getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    const options = { method, headers };
    if (body) options.body = JSON.stringify(body);

    const response = await fetch(API_URL + endpoint, options);
    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.erreur || 'Erreur serveur');
    }
    return data;
}

// ---- Auth ----
async function apiConnexion(email, mot_passe) {
    return apiRequest('/auth/connexion', 'POST', { email, mot_passe });
}
async function apiInscription(prenom, nom, email, mot_passe) {
    return apiRequest('/auth/inscription', 'POST', { prenom, nom, email, mot_passe });
}

// ---- Tâches ----
async function apiGetTaches() {
    return apiRequest('/taches');
}
async function apiCreerTache(nom, type, date) {
    return apiRequest('/taches', 'POST', { nom, type, date });
}
async function apiModifierTache(id, nom, type, date) {
    return apiRequest('/taches/' + id, 'PUT', { nom, type, date });
}
async function apiSupprimerTache(id) {
    return apiRequest('/taches/' + id, 'DELETE');
}
async function apiCompleterTache(id) {
    return apiRequest('/taches/' + id + '/completer', 'PATCH');
}

// ---- Profil ----
async function apiGetProfil() {
    return apiRequest('/profil');
}
async function apiUpdateProfil(prenom, nom, telephone, bio, couleur) {
    return apiRequest('/profil', 'PUT', { prenom, nom, telephone, bio, couleur });
}

// ---- Déconnexion ----
function logout() {
    removeToken();
    window.location.href = 'index.html';
}

// ---- Vérification auth  ----
function requireAuth() {
    if (!getToken()) {
        window.location.href = 'index.html';
        return false;
    }
    return true;
}