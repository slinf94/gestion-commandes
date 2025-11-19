/**
 * Script réutilisable pour les champs de recherche avec autocomplete
 * S'active automatiquement sur tous les champs avec la classe 'search-autocomplete-input'
 */

(function() {
    'use strict';

    // Initialiser tous les champs de recherche
    document.addEventListener('DOMContentLoaded', function() {
        const searchInputs = document.querySelectorAll('.search-autocomplete-input');
        
        searchInputs.forEach(function(input) {
            initializeSearchAutocomplete(input);
        });
    });

    function initializeSearchAutocomplete(input) {
        const wrapper = input.closest('.search-autocomplete-wrapper');
        if (!wrapper) return;

        const resultsContainer = wrapper.querySelector('.search-autocomplete-results');
        const resultsList = wrapper.querySelector('.search-autocomplete-list');
        const emptyMessage = wrapper.querySelector('.search-autocomplete-empty');
        const spinner = wrapper.querySelector('.search-autocomplete-spinner');
        
        const searchUrl = input.getAttribute('data-search-url');
        const resultKey = input.getAttribute('data-result-key') || 'data';
        const minLength = parseInt(input.getAttribute('data-min-length') || '2');
        const debounceDelay = parseInt(input.getAttribute('data-debounce-delay') || '500');
        
        let debounceTimer = null;
        let isSearching = false;
        let currentFocus = -1;

        // Fonction de recherche
        function performSearch(query) {
            if (!searchUrl || query.length < minLength) {
                hideResults();
                return;
            }

            if (isSearching) return;
            isSearching = true;

            // Afficher le spinner
            if (spinner) spinner.style.display = 'block';
            showResults();

            // Préparer l'URL avec les paramètres
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', query);
            url.searchParams.set('limit', '10');

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                displayResults(data, resultKey, query);
            })
            .catch(error => {
                console.error('Erreur recherche:', error);
                showError('Erreur lors de la recherche');
            })
            .finally(() => {
                isSearching = false;
                if (spinner) spinner.style.display = 'none';
            });
        }

        // Afficher les résultats
        function displayResults(data, resultKey, query) {
            if (!resultsList || !resultsContainer) return;

            const results = getNestedValue(data, resultKey) || [];
            
            if (results.length === 0) {
                showEmptyMessage();
                return;
            }

            hideEmptyMessage();
            resultsList.innerHTML = '';

            results.forEach((item, index) => {
                const itemElement = createResultItem(item, index, query);
                resultsList.appendChild(itemElement);
            });

            currentFocus = -1;
        }

        // Créer un élément de résultat
        function createResultItem(item, index, query) {
            const div = document.createElement('div');
            div.className = 'search-autocomplete-item';
            div.setAttribute('data-index', index);
            div.setAttribute('tabindex', '0');

            // Rendu personnalisable selon le type de résultat
            const title = item.title || item.name || item.label || item.text || '';
            const subtitle = item.subtitle || item.description || item.email || item.phone || '';
            
            div.innerHTML = `
                <div class="search-autocomplete-item-title">${highlightText(title, query)}</div>
                ${subtitle ? `<div class="search-autocomplete-item-subtitle">${highlightText(subtitle, query)}</div>` : ''}
            `;

            // Événement de clic
            div.addEventListener('click', function() {
                selectItem(item);
            });

            // Événement clavier
            div.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    selectItem(item);
                }
            });

            return div;
        }

        // Mettre en surbrillance le texte de recherche
        function highlightText(text, query) {
            if (!text || !query) return escapeHtml(text);
            
            const escapedText = escapeHtml(text);
            const escapedQuery = escapeHtml(query);
            const regex = new RegExp(`(${escapedQuery})`, 'gi');
            return escapedText.replace(regex, '<mark>$1</mark>');
        }

        // Échapper HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Sélectionner un élément
        function selectItem(item) {
            // Remplir le champ avec la valeur sélectionnée
            if (item.value !== undefined) {
                input.value = item.value;
            } else if (item.title) {
                input.value = item.title;
            } else if (item.name) {
                input.value = item.name;
            }

            // Déclencher un événement personnalisé
            input.dispatchEvent(new CustomEvent('search:selected', {
                detail: { item: item }
            }));

            hideResults();
            input.focus();
        }

        // Obtenir une valeur imbriquée
        function getNestedValue(obj, path) {
            return path.split('.').reduce((current, key) => current && current[key], obj);
        }

        // Afficher les résultats
        function showResults() {
            if (resultsContainer) {
                resultsContainer.style.display = 'block';
            }
        }

        // Masquer les résultats
        function hideResults() {
            if (resultsContainer) {
                resultsContainer.style.display = 'none';
            }
            currentFocus = -1;
        }

        // Afficher le message vide
        function showEmptyMessage() {
            if (emptyMessage) emptyMessage.style.display = 'block';
            if (resultsList) resultsList.innerHTML = '';
        }

        // Masquer le message vide
        function hideEmptyMessage() {
            if (emptyMessage) emptyMessage.style.display = 'none';
        }

        // Afficher une erreur
        function showError(message) {
            if (resultsList) {
                resultsList.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #dc3545;">
                        <i class="fas fa-exclamation-circle me-2"></i>${message}
                    </div>
                `;
            }
            showResults();
        }

        // Événement de saisie avec debounce
        input.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            clearTimeout(debounceTimer);
            
            if (query.length === 0) {
                hideResults();
                return;
            }

            if (query.length < minLength) {
                hideResults();
                return;
            }

            debounceTimer = setTimeout(function() {
                performSearch(query);
            }, debounceDelay);
        });

        // Masquer les résultats quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                hideResults();
            }
        });

        // Navigation au clavier
        input.addEventListener('keydown', function(e) {
            const items = resultsList ? resultsList.querySelectorAll('.search-autocomplete-item') : [];
            
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentFocus = (currentFocus < items.length - 1) ? currentFocus + 1 : 0;
                setActiveItem(items, currentFocus);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentFocus = (currentFocus > 0) ? currentFocus - 1 : items.length - 1;
                setActiveItem(items, currentFocus);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (currentFocus >= 0 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            } else if (e.key === 'Escape') {
                hideResults();
            }
        });

        // Définir l'élément actif
        function setActiveItem(items, index) {
            items.forEach((item, i) => {
                if (i === index) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    item.classList.remove('active');
                }
            });
        }
    }
})();














