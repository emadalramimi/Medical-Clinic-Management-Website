document.addEventListener('DOMContentLoaded', function () {

    // Récupère l'URL de la page courante et les paramètres de recherche
    const pathname = window.location.pathname;
    const searchParams = new URLSearchParams(window.location.search);

    // Si nous sommes sur la page usagers.php et que l'action est égale à add ou edit
    if (pathname.endsWith('/usagers.php') && (searchParams.get('action') === 'add' || searchParams.get('action') === 'edit')) {

        // Récupère les champs codes postaux et villes
        const codePostaux = document.querySelectorAll('.code_postal');
        const villes = document.querySelectorAll('.ville');

        /**
         * Remplit le champ ville avec les villes du code postal fourni
         * @param codePostalInput Champ code postal
         * @param villeInput Champ ville
         * @returns {Promise<void>} Résultat de la requête
         */
        function fetchCityData(codePostalInput, villeInput) {
            return new Promise((resolve, reject) => {
                let code_postal = codePostalInput.value;
                if (code_postal.length === 5) {
                    let url = `https://vicopo.selfbuild.fr/?code=${code_postal}`;
                    fetch(url)
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            // On vide le champ ville et on ajoute les nouvelles villes
                            villeInput.innerHTML = '';
                            if (data.cities.length > 0) {
                                for (let i = 0; i < data.cities.length; i++) {
                                    let option = document.createElement('option');
                                    option.value = data.cities[i].city;
                                    option.innerHTML = data.cities[i].city;
                                    villeInput.appendChild(option);
                                }
                                resolve();
                            }
                        })
                        .catch(error => {
                            reject(error);
                        });
                } else {
                    // Si le code postal est < 5 chiffres, on vide le champ ville
                    villeInput.innerHTML = '';
                    resolve();
                }
            });
        }

        // Pour chaque champ code postal, on ajoute un écouteur d'évènement
        codePostaux.forEach(function (codePostal, index) {
            codePostal.addEventListener('input', function () {
                // Si le code postal est > 5 chiffres, on le coupe (5 chiffres max)
                if (codePostal.value.length > 5) {
                    this.value = this.value.slice(0, 5);
                }
                
                fetchCityData(codePostal, villes[index]);
            });

            // Si le code postal est égal à 5 chiffres au chargement de la page, on récupère les villes
            if (codePostal.value.length === 5) {
                fetchCityData(codePostal, villes[index]).then(() => {
                    // On sélectionne la ville par défaut (celle déjà sélectionnée en cas de modification par exemple)
                    villes[index].value = villes[index].getAttribute('data-selected');
                });
            }
        });

        // Limitation du champ sécurité sociale à 15 chiffres
        document.getElementById('num_securite_sociale').addEventListener('input', function () {
            if (this.value.length > 15) {
                this.value = this.value.slice(0, 15);
            }
        });

    }
    
    // Si nous sommes sur la page consultations.php et que l'action est égale à add ou edit
    else if (pathname.endsWith('/consultations.php') && (searchParams.get('action') === 'add' || searchParams.get('action') === 'edit')) { 

        const usagerSelect = document.getElementById('id_usager');
        const medecinSelect = document.getElementById('id_medecin');

        var medecinTraitantNoticed = null;

        /**
         * Indique le médecin traitant dans le sélecteur des médecins de l'usager
         * @returns {void}
         */
        function noticeMedecinTraitant() {
            // Récupère l'option sélectionnée dans le sélecteur d'usagers
            const selectedOption = usagerSelect.options[usagerSelect.selectedIndex];
        
            // Vérifie si l'option sélectionnée a un attribut 'data-medecin-traitant'
            if (selectedOption.getAttribute('data-medecin-traitant')) {
                // Met à jour la valeur du sélecteur de médecins avec l'id du médecin traitant de l'usager sélectionné
                medecinSelect.value = selectedOption.getAttribute('data-medecin-traitant');
        
                // Recherche l'option du médecin correspondant dans le sélecteur de médecins
                const medecinOption = Array.from(medecinSelect.options).find(option => option.value === selectedOption.getAttribute('data-medecin-traitant'));
        
                // Si l'option du médecin est trouvée, ajoute une mention '(médecin traitant)' à son libellé
                if (medecinOption) {
                    medecinOption.text += ' (médecin traitant)';
                    medecinTraitantNoticed = medecinOption; // Stocke l'option du médecin traitant pour référence future
                }
            }
        }

        // Au chargement de la page, on indique le médecin traitant dans le sélecteur des médecins de l'usager
        noticeMedecinTraitant();

        // Lorsque l'usager change, on indique le médecin traitant dans le sélecteur des médecins de l'usager
        usagerSelect.addEventListener('change', function () {
            // Si un médecin traitant était indiqué, on le retire
            if (medecinTraitantNoticed !== null) {
                medecinTraitantNoticed.text = medecinTraitantNoticed.text.replace(' (médecin traitant)', '');
                medecinTraitantNoticed = null;
            }

            // On indique le médecin traitant dans le sélecteur des médecins de l'usager
            noticeMedecinTraitant();
        });

    }

});