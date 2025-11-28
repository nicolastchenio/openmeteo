document.addEventListener('DOMContentLoaded', () => {
    const analyzeBtn = document.getElementById('analyze-btn');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const resultContainer = document.getElementById('result-container');
    const resultDiv = document.getElementById('result');
    const spinner = document.getElementById('spinner');

    // L'URL du point d'entrée du backend PHP
    const backendUrl = '../backend/index.php';

    analyzeBtn.addEventListener('click', () => {
        const latitude = parseFloat(latitudeInput.value);
        const longitude = parseFloat(longitudeInput.value);

        if (isNaN(latitude) || isNaN(longitude)) {
            alert('Veuillez entrer une latitude et une longitude valides.');
            return;
        }

        // Préparer l'interface pour le résultat
        resultContainer.style.display = 'block';
        resultDiv.style.display = 'none';
        spinner.style.display = 'block';
        resultDiv.textContent = '';
        resultDiv.className = '';

        // Envoyer la requête au backend
        fetch(backendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ latitude, longitude }),
        })
        .then(response => {
            if (!response.ok) {
                // Si le statut HTTP n'est pas 2xx, tenter de lire le message d'erreur JSON
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Erreur HTTP ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            spinner.style.display = 'none';
            resultDiv.style.display = 'block';
            
            if (data.error) {
                 throw new Error(data.message);
            }

            // Mettre à jour le contenu et le style en fonction du risque
            const riskText = data.risk ? 'Risque Détecté' : 'Aucun Risque Détecté';
            resultDiv.innerHTML = `<strong>${riskText}</strong><br>${data.message}`;
            
            if (data.risk) {
                resultDiv.classList.add('risk-true');
            } else {
                resultDiv.classList.add('risk-false');
            }
        })
        .catch(error => {
            spinner.style.display = 'none';
            resultDiv.style.display = 'block';
            resultDiv.textContent = `Erreur lors de l'analyse : ${error.message}`;
            resultDiv.className = 'risk-true'; // Style d'erreur
        });
    });
});
