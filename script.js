document.addEventListener('DOMContentLoaded', () => {
    const formationSelect = document.getElementById('formation-select');
    const documentList = document.getElementById('document-list');
    const uploadFileInput = document.getElementById('upload-file');
    const uploadButton = document.getElementById('upload-button');

    // Charger les formations
    fetch('http://poc.fr/api.php?action=get_formations')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(formation => {
                const option = document.createElement('option');
                option.value = formation.id;
                option.textContent = formation.nom;
                formationSelect.appendChild(option);
            });
        });

    // Charger les documents
    formationSelect.addEventListener('change', () => {
        fetch(`http://poc.fr/api.php?action=get_documents&formation_id=${formationSelect.value}`)
            .then(response => response.json())
            .then(data => {
                documentList.innerHTML = '';
                data.forEach(doc => {
                    const li = document.createElement('li');
                    li.textContent = doc.titre;
                    li.dataset.chemin = doc.chemin;
                    li.addEventListener('click', () => {
                        // Utiliser l'API OpenBoard pour ajouter le document
                        window.sankore.addObject(doc.chemin);
                    });
                    documentList.appendChild(li);
                });
            });
    });

    // Gérer le téléchargement du fichier
    uploadButton.addEventListener('click', () => {
        uploadFileInput.click();
    });

    uploadFileInput.addEventListener('change', () => {
        const file = uploadFileInput.files[0];
        const titre = file.name; // Utiliser le nom du fichier comme titre
        const formData = new FormData();
        formData.append('file', file);
        formData.append('utilisateur_id', 1); // Remplacer par l'ID utilisateur réel
        formData.append('formation_id', formationSelect.value);
        formData.append('titre', titre);

        fetch('http://poc.fr/api.php?action=save_document', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document enregistré.');
            } else {
                alert('Erreur lors de l\'enregistrement du document.');
            }
        });
    });
});