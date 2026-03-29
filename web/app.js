const API_URL = 'http://localhost/growchi/mvc/api/index.php';

function getToken() { return sessionStorage.getItem('growchi_token'); }
function setToken(token) { sessionStorage.setItem('growchi_token', token); }
function removeToken() { sessionStorage.removeItem('growchi_token'); sessionStorage.removeItem('growchi_user'); }
function getCurrentUser() { const u = sessionStorage.getItem('growchi_user'); return u ? JSON.parse(u) : null; }
function setCurrentUser(user) { sessionStorage.setItem('growchi_user', JSON.stringify(user)); }

async function apiRequest(action, method = 'GET', body = null, id = null) {
    const headers = { 'Content-Type': 'application/json' };
    const token = getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    let url = API_URL + '?action=' + action;
    if (id) url += '&id=' + id;

    const options = { method, headers };
    if (body) options.body = JSON.stringify(body);

    const response = await fetch(url, options);
    const data = await response.json();
    if (!response.ok) throw new Error(data.erreur || 'Erreur serveur');
    return data;
}

async function apiConnexion(email, mot_passe) { return apiRequest('connexion', 'POST', { email, mot_passe }); }
async function apiInscription(prenom, nom, email, mot_passe) { return apiRequest('inscription', 'POST', { prenom, nom, email, mot_passe }); }
async function apiGetTaches() { return apiRequest('taches'); }
async function apiCreerTache(nom, type, date) { return apiRequest('taches', 'POST', { nom, type, date }); }
async function apiModifierTache(id, nom, type, date) { return apiRequest('tache_modifier', 'PUT', { nom, type, date }, id); }
async function apiSupprimerTache(id) { return apiRequest('tache_supprimer', 'DELETE', null, id); }
async function apiCompleterTache(id) { return apiRequest('tache_completer', 'PATCH', null, id); }
async function apiGetProfil() { return apiRequest('profil'); }
async function apiUpdateProfil(prenom, nom, telephone, bio, couleur) { return apiRequest('profil', 'PUT', { prenom, nom, telephone, bio, couleur }); }

function logout() { removeToken(); window.location.href = 'index.html'; }
function requireAuth() { if (!getToken()) { window.location.href = 'index.html'; return false; } return true; }