document.addEventListener('DOMContentLoaded', () => {
    const formationSelect = document.getElementById('formation-select');
    const documentList = document.getElementById('document-list');

    // Charger les formations
    fetch('http://poc.fr/api.php?action=get_formations')
        .then(response => response.json())
        .then(data => {
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
                        window.open(`http://poc.fr/${doc.chemin}`, '_blank');
                    });
                    documentList.appendChild(li);
                });
            });
    });
});
