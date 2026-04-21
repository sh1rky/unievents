// ========== CHARGER LES ÉVÉNEMENTS ==========
async function loadEvents() {
    try {
        const response = await fetch('http://localhost/unievents/api/get_events.php');
        const result = await response.json();
        
        if (result.success && result.data) {
            displayEvents(result.data);
        } else {
            console.error('Erreur API:', result);
            document.getElementById('events-grid').innerHTML = '<div class="error">Erreur chargement événements</div>';
        }
    } catch (error) {
        console.error('Fetch error:', error);
        document.getElementById('events-grid').innerHTML = '<div class="error">Impossible de charger les événements</div>';
    }
}

// ========== AFFICHER LES ÉVÉNEMENTS ==========
function displayEvents(events) {
    const grid = document.getElementById('events-grid');
    if (!grid) return;
    
    if (events.length === 0) {
        grid.innerHTML = '<div class="no-events">Aucun événement trouvé</div>';
        return;
    }
    
    grid.innerHTML = '';
    
    events.forEach(event => {
        const card = document.createElement('div');
        card.className = 'carte';
        card.setAttribute('data-categorie', event.category);
        
        let placesClass = 'available';
        let placesLeft = event.places_restantes || (event.capacity - event.current_registrations);
        
        if (placesLeft <= 5) placesClass = 'critical';
        else if (placesLeft <= 15) placesClass = 'warning';
        
        let fillPercentage = event.fill_percentage || (event.current_registrations / event.capacity * 100);
        
        card.innerHTML = `
            <span class="categorie">${getCategoryIcon(event.category)} ${event.category}</span>
            <h3>${escapeHtml(event.title)}</h3>
            <div class="details">
                <p>📅 ${event.date} • ${event.time}</p>
                <p>📍 ${escapeHtml(event.location)}</p>
                ${event.speaker ? `<p>👤 ${escapeHtml(event.speaker)}</p>` : ''}
            </div>
            <p class="description">${escapeHtml(event.description.substring(0, 120))}...</p>
            <div class="event-footer">
                <div class="places-info">
                    <span class="places ${placesClass}">${placesLeft} places restantes</span>
                    <span class="places-count">/${event.capacity}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${fillPercentage}%;"></div>
                </div>
                <button onclick="openInscriptionModal(${event.id}, '${escapeHtml(event.title)}', ${event.capacity})" class="btn-small">📝 S'inscrire</button>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

function getCategoryIcon(category) {
    const icons = {
        'Conférence': '🎤',
        'Workshop': '💻',
        'Hackathon': '🚀',
        'Club': '🎮',
        'Séminaire': '📚'
    };
    return icons[category] || '📌';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ========== MODAL INSCRIPTION ==========
let currentEventId = null;
let currentEventCapacity = null;

function openInscriptionModal(eventId, eventName, capacity) {
    currentEventId = eventId;
    currentEventCapacity = capacity;
    document.getElementById('event-name-display').textContent = eventName;
    document.getElementById('inscription').style.display = 'flex';
}

function closeModal() {
    document.getElementById('inscription').style.display = 'none';
    document.getElementById('inscription-form').reset();
}

// ========== SOUMETTRE INSCRIPTION ==========
async function submitRegistration(event) {
    event.preventDefault();
    
    const formData = {
        event_id: currentEventId,
        nom: document.getElementById('nom').value,
        email: document.getElementById('email').value,
        tel: document.getElementById('tel').value,
        niveau: document.getElementById('niveau').value,
        specialite: document.getElementById('specialite').value
    };
    
    try {
        const response = await fetch('http://localhost/unievents/api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal();
            document.getElementById('user-email').textContent = formData.email;
            document.getElementById('confirmation').style.display = 'flex';
            showToast(result.message);
            loadEvents(); // Recharger les événements
        } else {
            showToast('❌ ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de l\'inscription');
    }
}

// ========== CRÉER ÉVÉNEMENT ==========
async function createEvent(event) {
    event.preventDefault();
    
    const formData = {
        title: document.getElementById('event-title').value,
        category: document.getElementById('event-category').value,
        capacity: document.getElementById('event-capacity').value,
        date: document.getElementById('event-date').value,
        time: document.getElementById('event-time').value,
        location: document.getElementById('event-location').value,
        description: document.getElementById('event-description').value,
        speaker: document.getElementById('event-speaker').value
    };
    
    try {
        const response = await fetch('http://localhost/unievents/api/create_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('✨ Événement créé avec succès !');
            document.getElementById('event-form').reset();
            loadEvents();
        } else {
            showToast('❌ ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de la création');
    }
}

// ========== ENVOYER MESSAGE CONTACT ==========
async function sendMessage(event) {
    event.preventDefault();
    
    const formData = {
        name: document.getElementById('contact-name').value,
        email: document.getElementById('contact-email').value,
        subject: document.getElementById('contact-subject').value,
        message: document.getElementById('contact-message').value
    };
    
    try {
        const response = await fetch('http://localhost/unievents/api/contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('📨 ' + result.message);
            document.getElementById('contact-form').reset();
        } else {
            showToast('❌ ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de l\'envoi');
    }
}

// ========== DEMANDE ORGANISATEUR ==========
async function submitOrganizer(event) {
    event.preventDefault();
    
    const formData = {
        name: document.getElementById('org-name').value,
        email: document.getElementById('org-email').value,
        club: document.getElementById('org-club').value,
        role: document.getElementById('org-role').value
    };
    
    try {
        const response = await fetch('http://localhost/unievents/api/organizer_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('📋 ' + result.message);
            closeOrganizerModal();
            document.getElementById('organizer-form').reset();
        } else {
            showToast('❌ ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de l\'envoi');
    }
}

// ========== FERMETURE MODALS ==========
function closeConfirmation() {
    document.getElementById('confirmation').style.display = 'none';
}

function closeWaitlist() {
    document.getElementById('waitlist').style.display = 'none';
}

function closeOrganizerModal() {
    document.getElementById('organizer-modal').style.display = 'none';
}

function showOrganizerInfo() {
    document.getElementById('organizer-modal').style.display = 'flex';
}

function joinWaitlist() {
    showToast('⏳ Vous avez rejoint la liste d\'attente !');
    closeWaitlist();
}

// ========== FILTRES ==========
function filterEvents(categorie) {
    const cards = document.querySelectorAll('.carte');
    cards.forEach(card => {
        if (categorie === 'all' || card.getAttribute('data-categorie') === categorie) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Mettre à jour les boutons actifs
    document.querySelectorAll('.filtre-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.includes(categorie) || (categorie === 'all' && btn.textContent.includes('Tous'))) {
            btn.classList.add('active');
        }
    });
}

// ========== TOAST NOTIFICATION ==========
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    // Cacher les modals
    document.getElementById('inscription').style.display = 'none';
    document.getElementById('confirmation').style.display = 'none';
    document.getElementById('waitlist').style.display = 'none';
    document.getElementById('organizer-modal').style.display = 'none';
    
    // Charger les événements
    loadEvents();
    
    // Menu burger responsive
    const burger = document.querySelector('.menu-burger');
    const navMenu = document.querySelector('nav ul');
    if (burger && navMenu) {
        burger.addEventListener('click', function() {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
    }
    
    // Fermer les modals en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        const modals = ['inscription', 'confirmation', 'waitlist', 'organizer-modal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
});