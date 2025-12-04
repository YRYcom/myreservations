function organizeToolbar() {
    const toggleContainer = document.getElementById('display-finished-toggle-container');
    const toolbar = document.querySelector('.fi-ta-header-toolbar');
    
    if (!toggleContainer || !toolbar) return;
    
    // Si déjà bien placé, ne rien faire
    if (toolbar.contains(toggleContainer) && toolbar.firstElementChild === toggleContainer) {
        return;
    }
    
    // Trouver la barre de recherche
    const searchField = toolbar.querySelector('.fi-ta-search-field');
    
    // Créer le conteneur de recherche si nécessaire
    let searchContainer = toolbar.querySelector('.search-container-div');
    if (!searchContainer && searchField) {
        searchContainer = document.createElement('div');
        searchContainer.className = 'search-container-div';
        searchField.parentElement.replaceChild(searchContainer, searchField);
        searchContainer.appendChild(searchField);
    }
    
    // Déplacer le toggle au début de la toolbar
    if (!toolbar.contains(toggleContainer)) {
        toolbar.insertBefore(toggleContainer, toolbar.firstChild);
    } else if (toolbar.firstElementChild !== toggleContainer) {
        toggleContainer.remove();
        toolbar.insertBefore(toggleContainer, toolbar.firstChild);
    }
}

// Exécuter au chargement et après les mises à jour Livewire
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(organizeToolbar, 100);
    });
} else {
    setTimeout(organizeToolbar, 100);
}

if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', () => setTimeout(organizeToolbar, 100));
    });
}

// Observer les changements de la toolbar
const observer = new MutationObserver(() => {
    const toggleContainer = document.getElementById('display-finished-toggle-container');
    const toolbar = document.querySelector('.fi-ta-header-toolbar');
    if (toggleContainer && toolbar && !toolbar.contains(toggleContainer)) {
        organizeToolbar();
    }
});

observer.observe(document.body, { childList: true, subtree: true });

