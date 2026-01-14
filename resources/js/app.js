// public/js/app.js

// Global state
let state = {
    currentUser: null,
    caughtPokemonIds: [],
    userTeam: [],
    trading: {
        yourPokemon: null,
        trainerPokemon: null
    }
};

// Load user data from Laravel session
if (window.Laravel && window.Laravel.user) {
    state.currentUser = window.Laravel.user;
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Fetch wrapper with CSRF token
async function apiFetch(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    };
    
    const response = await fetch(`${API_URL}/${endpoint}`, {
        ...defaultOptions,
        ...options
    });
    
    if (!response.ok) {
        throw new Error(`API Error: ${response.status}`);
    }
    
    return response.json();
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Catch buttons
    document.querySelectorAll('.btn-catch').forEach(button => {
        button.addEventListener('click', async function() {
            const pokemonId = this.dataset.id;
            await catchPokemon(pokemonId);
        });
    });
    
    // Add to team buttons
    document.querySelectorAll('.btn-add-to-team').forEach(button => {
        button.addEventListener('click', async function() {
            const pokemonId = this.dataset.id;
            await addToTeam(pokemonId);
        });
    });
    
    // Modal close buttons
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Add Pokémon button
    const addPokemonBtn = document.getElementById('add-pokemon-btn');
    if (addPokemonBtn) {
        addPokemonBtn.addEventListener('click', function() {
            document.getElementById('add-pokemon-modal').style.display = 'block';
        });
    }
});

// Catch Pokémon function
async function catchPokemon(pokemonId) {
    try {
        const result = await apiFetch('catch', {
            method: 'POST',
            body: JSON.stringify({ pokemon_id: pokemonId })
        });
        
        if (result.success) {
            showNotification(result.message, 'success');
            
            // Update UI
            const card = document.querySelector(`.btn-catch[data-id="${pokemonId}"]`).closest('.pokemon-card');
            card.classList.add('caught');
            
            // Change button
            const catchBtn = card.querySelector('.btn-catch');
            catchBtn.className = 'btn-add-to-team';
            catchBtn.innerHTML = '<i class="fas fa-plus"></i> Add to Team';
            catchBtn.dataset.id = pokemonId;
            
            // Update progress
            updateProgress();
        } else {
            showNotification(result.error, 'error');
        }
    } catch (error) {
        showNotification('Failed to catch Pokémon', 'error');
        console.error(error);
    }
}

// Add to team function
async function addToTeam(pokemonId) {
    try {
        const result = await apiFetch(`team/add/${pokemonId}`, {
            method: 'POST'
        });
        
        if (result.success) {
            showNotification(result.message, 'success');
        } else {
            showNotification(result.error, 'error');
        }
    } catch (error) {
        showNotification('Failed to add to team', 'error');
        console.error(error);
    }
}

// Update progress bar
function updateProgress() {
    const progressFill = document.querySelector('.progress-fill');
    const progressCount = document.querySelector('.progress-count');
    
    if (progressFill && progressCount) {
        // Update from server or calculate locally
        fetch('/api/user/progress')
            .then(res => res.json())
            .then(data => {
                progressFill.style.width = `${data.percentage}%`;
                progressCount.textContent = `${data.caught}/${data.total}`;
            });
    }
}

// Initialize
if (state.currentUser) {
    updateProgress();
}