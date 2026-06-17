// Récupération des éléments du formulaire
const brandSelect = document.getElementById('quote_vehicle_brand');
const modelSelect = document.getElementById('quote_vehicle_model');
const yearSelect = document.getElementById('quote_vehicle_vehicleYear');

// Sauvegarde des valeurs initiales
const initialBrand = brandSelect.value;
const initialModel = modelSelect.value;
const initialYear = yearSelect.value;

// FONCTION : Charger les models d'une marque dans le select
function loadModels(brandName, selectAfter = null) {
    return fetch(`http://localhost:8080/quote/brand/${brandName}`)
        .then(response => response.json())
        .then(models => {
            modelSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Choisissez le modèle';
            modelSelect.appendChild(placeholder);

            for (let i = 0; i < models.length; i++) {
                    const option = document.createElement('option');
                    option.value = models[i];
                    option.textContent = models[i];
                    modelSelect.appendChild(option);  
            }
           
            // cas pré-sellection (modification)
            if (selectAfter) {
                modelSelect.value = selectAfter;
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
        });
}

// FONCTION : Charger les années d'un model dans le select
function loadYears(modelName, selectAfter = null) {
    return fetch(`http://localhost:8080/quote/model/${modelName}`)
        .then(response => response.json())
        .then(years => {
            yearSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Choisissez l\'année';
            yearSelect.appendChild(placeholder);

            for (let i = 0; i < years.length; i++) {
                    const option = document.createElement('option');
                    option.value = years[i];
                    option.textContent = years[i];
                    yearSelect.appendChild(option);  
            }
           
            // cas pré-sellection (modification)
            if (selectAfter) {
                yearSelect.value = selectAfter;
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
        });
}

// EVENT : L'utilisateur change la marque
// → on charge les models correspondants (et on reset year)
brandSelect.addEventListener("change", () => {
    const selectedBrand = brandSelect.value;
    if (!selectedBrand) {
        modelSelect.innerHTML = '<option value="">Choisissez le modèle</option>';
        yearSelect.innerHTML = '<option value="">Choisissez l\'année</option>';
        return;
    }
    // Reset le select year aussi (car le model va changer)
    yearSelect.innerHTML = '<option value="">Choisissez l\'année</option>';    

    // Charger les nouveaux models (sans pré-sélection)
    loadModels(selectedBrand)
});

// EVENT : L'utilisateur change le model
// → on charge les années correspondantes
modelSelect.addEventListener("change", () => {
    const selectedModel = modelSelect.value;
    if (!selectedModel) {
        yearSelect.innerHTML = '<option value="">Choisissez l\'année</option>';
        return;
    }    
    loadYears(selectedModel)
});

// AU CHARGEMENT DE LA PAGE
// Si une marque est déjà sélectionnée (cas de la modification
// d'un devis existant), on charge les models de cette marque
// puis les années du model, en gardant la sélection initiale.
if (initialBrand) {
    loadModels(initialBrand, initialModel).then(() => {
        // Une fois les models chargés et re-sélectionnés,
        // on peut charger les années du model initial
        if (initialModel) {
            loadYears(initialModel, initialYear);
        }
    });
}