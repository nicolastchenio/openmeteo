# openmeteo
## prompt1 ##
1. prompte 1

- prompt
Je dois developper une application qui réalise l’intégration de l’API Open-Meteo afin d’automatiser la collecte des données nécessaires au suivi des cyclones tropicaux dans la zone Océan Indien. L'idee est qu'apres qu'un utilisateur est rentré la latitude est la longitude d'un lieu l application interroge l api d'open meteo et retourne a l'utilisateur si il y a un risque elevé ou pas de cyclone. en t appuyant sur la fiche_technique_open_meteo.pdf transmis en piece jointe a ce prompt peux tu me rediger un prompt technique que je transmettrai a un llm pour realiser le codage de cette api. si tu as des besoins supplementaire n'hesite pas a me le demander avant la redaction de ton prompt technique.

- reponse chatGPT
PROMPT TECHNIQUE POUR GEMINI – INTEGRATION API OPEN-METEO EN PHP

Contexte du projet :
Tu es un expert en développement PHP, en architecture logicielle, en intégration d’API et en météorologie opérationnelle. Ta mission est de générer le code complet d’une application desktop fonctionnant avec un backend PHP et un frontend HTML/CSS/JS embarqué. L’application doit interroger l’API Open-Meteo afin de détecter un risque cyclonique dans la zone Océan Indien. L’utilisateur entre une latitude et une longitude, le système appelle les deux endpoints Open-Meteo, fusionne les données, applique des règles simples de détection, puis renvoie un booléen et un texte explicatif.

Tu dois produire un code clair, robuste, commenté, sans hallucinations, en respectant strictement les contraintes décrites ci-dessous.

SPECIFICATIONS API OPEN-METEO (obligatoires, provenant de la fiche technique fournie)

Endpoint atmosphérique (prévisions au sol) :
https://api.open-meteo.com/v1/forecast

Variables atmosphériques à interroger obligatoirement :
temperature_2m
relative_humidity_2m
pressure_msl
wind_speed_10m
wind_direction_10m
precipitation

Endpoint marin (données océaniques) :
https://marine-api.open-meteo.com/v1/marine

Variables océaniques à interroger obligatoirement :
sea_surface_temperature
wave_height

Paramètres à inclure obligatoirement dans les deux appels :
elevation=0
hourly=(liste des variables)
forecast_days=4
timezone=auto

REGLES SIMPLES POUR DETECTION DU RISQUE CYCLONIQUE

Le backend doit implémenter une fonction interne :
evaluateCycloneRisk($dataAtmos, $dataMarine): array

Elle renvoie la structure JSON suivante :
{
"risk": true|false,
"message": "explication"
}

Conditions simples :

Pression < 1005 hPa

Humidité > 80 %

Vent > 50 km/h

SST > 27°C

Précipitation > 10 mm/h

Règle finale :
Si au moins 3 conditions sont réunies, risk = true.
Le message doit lister les conditions déclenchées.

SPECIFICATIONS DU BACKEND PHP

Le backend doit :

Recevoir la latitude et la longitude envoyées par le frontend.

Construire correctement les URLs des API Open-Meteo (atmosphère et océan).

Utiliser obligatoirement cURL pour faire les requêtes HTTP. Aucun autre système n’est autorisé.

Gérer les erreurs et les timeouts.

Décoder le JSON et vérifier sa validité.

Fusionner les données atmosphériques et marines nécessaires.

Appeler evaluateCycloneRisk.

Retourner au frontend une réponse JSON contenant "risk" et "message".

Exemple de réponse :
{
"risk": true,
"message": "Pression basse, humidité élevée, SST élevée"
}

Exigences techniques Backend :

PHP 8 minimum

Organisation en classes : ApiClient (cURL), CycloneRiskEvaluator, Controller, etc.

Code commenté et propre.

Respect strict des noms de variables Open-Meteo.

FRONTEND HTML/CSS/JS (APPLICATION DESKTOP EMBARQUEE)

Le frontend doit être constitué uniquement de fichiers HTML, CSS et JavaScript (pas de framework, pas d’Electron, pas de navigateur embarqué sophistiqué). Ce frontend doit être compatible avec un chargement local (par exemple via un mini serveur PHP intégré).

Le frontend doit contenir :

Un champ pour la latitude

Un champ pour la longitude

Un bouton "Analyser"

Un composant JS qui envoie une requête AJAX au backend (fetch ou XMLHttpRequest)

Une zone où s’affiche le résultat : true/false + message descriptif

Aucun framework externe n’est autorisé (ni React, ni Vue, ni Electron).

STRUCTURE DE FICHIERS DEMANDEE

Le projet final doit être structuré comme suit :

/app
/Controllers
ApiController.php
/Services
OpenMeteoClient.php
CycloneRiskEvaluator.php
/Models
WeatherData.php
/backend
index.php
/frontend
index.html
app.js
style.css
/tests
(dossier vide pour le moment, les tests seront écrits plus tard)

DIAGRAMME DE FLUX (FORME TEXTE SIMPLE)

Diagramme de flux du fonctionnement de l’application :

Frontend (HTML/JS) --> Backend PHP
Backend PHP --> API Open-Meteo Atmosphérique
Backend PHP --> API Open-Meteo Marine
API Atmos + API Marine --> Fusion des données
Fusion des données --> Module evaluateCycloneRisk
Module evaluateCycloneRisk --> Réponse JSON
Réponse JSON --> Frontend (affichage du risque)

Tu dois reproduire ce diagramme sous forme Mermaid dans ta réponse.

EXEMPLES DE REQUETES BACKEND

Requête frontend vers backend :
POST /analyze
{
"latitude": -21.1151,
"longitude": 55.5364
}

Réponse backend :
{
"risk": true,
"message": "Pression basse, humidité élevée, SST > 27C"
}

CONTRAINTES STRICTES ANTI-HALLUCINATION

Ne jamais inventer de variables Open-Meteo.

Utiliser uniquement ces variables :
temperature_2m
relative_humidity_2m
pressure_msl
wind_speed_10m
wind_direction_10m
precipitation
sea_surface_temperature
wave_height

Ne jamais créer d’endpoints Open-Meteo inexistants.

Si une donnée manque, retourner une erreur propre.

Ne pas ajouter de modèles météo non mentionnés.

Respecter strictement la structure JSON attendue.

Ne pas inventer de frameworks ou outils non demandés.

CE QUE TU DOIS PRODUIRE DANS TA REPONSE

Le code complet du backend PHP (toutes les classes, contrôleur, index.php).

Le code complet du frontend HTML/CSS/JS.

Le diagramme de flux au format Mermaid.

La structure finale du projet.

Les exemples d’appels API.

Les instructions pour exécuter l’application en local.

Les recommandations pour l’écriture ultérieure des tests unitaires.

FIN DU PROMPT TECHNIQUE

Tu dois répondre uniquement avec le code, les diagrammes, les explications techniques et les éléments demandés. Aucune digression.

FIN.


# Instructions pour l'Exécution Locale #

   1. Prérequis : Assurez-vous d'avoir PHP 8 (ou une version supérieure) installé sur votre machine et accessible depuis la ligne de commande.

   2. Démarrer le serveur :
       * Ouvrez un terminal ou une console de commande.
       * Naviguez jusqu'au dossier racine de votre projet (le dossier openmeteo).
       * Lancez le serveur de développement intégré de PHP en exécutant la commande suivante :
   1         php -S localhost:8000

   3. Accéder à l'application :
       * Ouvrez votre navigateur web.
       * Rendez-vous à l'adresse : `http://localhost:8000/frontend/`

      Vous devriez voir l'interface de l'application. Les valeurs par défaut pour La Réunion sont pré-remplies. Cliquez sur "Analyser" pour interroger le backend et afficher
  le résultat.

  5. Recommandations pour les Tests Unitaires

  Le dossier /tests est prêt à être utilisé. Pour écrire des tests unitaires efficaces pour ce projet avec un framework comme PHPUnit, je vous recommande de vous concentrer
  sur les points suivants :

   1. Tester `CycloneRiskEvaluator` en isolation :
       * C'est le test le plus important car il valide la logique métier.
       * Créez des fichiers JSON mock représentant des réponses d'API complètes (dataAtmos et dataMarine).
       * Créez plusieurs scénarios :
           * Un cas où 0 condition est remplie.
           * Un cas où 2 conditions sont remplies (devrait retourner risk: false).
           * Un cas où 3 conditions sont remplies (devrait retourner risk: true).
           * Un cas où 5 conditions sont remplies (devrait retourner risk: true).
           * Un cas avec des données manquantes ou nulles pour vérifier que le code ne plante pas.
       * Dans vos tests, chargez ces mocks, passez-les à evaluateCycloneRisk et affirmez (assertEquals) que la sortie (risk et message) est exactement celle attendue.

   2. Tester `OpenMeteoClient` avec des Mocks :
       * Tester le client cURL est plus complexe. Vous ne voulez pas faire de vrais appels HTTP dans vos tests unitaires car ils sont lents et peu fiables.
       * La meilleure approche est de "mocker" la fonction curl_exec pour qu'elle retourne des réponses prédéfinies ou des erreurs. Des bibliothèques comme vfsStream ou des 
         fonctionnalités de mocking de PHPUnit peuvent aider à simuler des réponses.
       * Testez les cas d'erreur : timeout cURL, code de statut HTTP non-200, JSON invalide, réponse d'erreur de l'API.

   3. Tester `ApiController` :
       * Injectez des versions "mock" de OpenMeteoClient et CycloneRiskEvaluator dans le constructeur de ApiController.
       * Configurez les mocks pour qu'ils retournent des résultats spécifiques (par exemple, faites en sorte que le client retourne une erreur).
       * Appelez handleAnalysisRequest et vérifiez que le contrôleur retourne le bon format de tableau et définit le code de réponse HTTP approprié (http_response_code).