const API_URL = 'http://php-dev-2.online/api/';

// Fonction pour récupérer toutes les technologies
async function fetchTechnologies() {
    const response = await fetch(API_URL + 'technologies');
    const technologies = await response.json();
    const technologyList = document.getElementById('technology-list');
    technologyList.innerHTML = '';  // Clear the list
    technologies.forEach(technology => {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item';
        listItem.textContent = technology.name + ' (' + technology.category + ')';
        technologyList.appendChild(listItem);
    });
}

// Fonction pour ajouter une nouvelle technologie
async function addTechnology(name, category) {
    const response = await fetch(API_URL + 'technologies', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, category }),
    });
    const result = await response.json();
    console.log(result);
    fetchTechnologies();  // Refresh the list
}

// Écouteur d'événements pour le formulaire d'ajout de technologie
document.getElementById('add-technology-form').addEventListener('submit', event => {
    event.preventDefault();
    const name = document.getElementById('name').value;
    const category = document.getElementById('category').value;
    addTechnology(name, category);
});

async function fetchCategories() {
    const response = await fetch(API_URL + 'categories');
    const categories = await response.json();
    const categoryList = document.getElementById('category-list');
    categoryList.innerHTML = '';  // Clear the list
    categories.forEach(category => {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item';
        listItem.textContent = category.name;
        categoryList.appendChild(listItem);
    });
}

// Fonction pour ajouter une nouvelle catégorie
async function addCategory(name) {
    const response = await fetch(API_URL + 'categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name }),
    });
    const result = await response.json();
    console.log(result);
    fetchCategories();  // Refresh the list
}

// Écouteur d'événements pour le formulaire d'ajout de catégorie
document.getElementById('add-category-form').addEventListener('submit', event => {
    event.preventDefault();
    const name = document.getElementById('category-name').value;
    addCategory(name);
});

// Fonction pour récupérer toutes les ressources
async function fetchResources() {
    const response = await fetch(API_URL + 'resources');
    const resources = await response.json();
    const resourceList = document.getElementById('resource-list');
    resourceList.innerHTML = '';  // Clear the list
    resources.forEach(resource => {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item';
        listItem.textContent = resource.name + ' (' + resource.url + ')';
        resourceList.appendChild(listItem);
    });
}

// Fonction pour ajouter une nouvelle ressource
async function addResource(name, url) {
    const response = await fetch(API_URL + 'resources', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, url }),
    });
    const result = await response.json();
    console.log(result);
    fetchResources();  // Refresh the list
}

// Écouteur d'événements pour le formulaire d'ajout de ressource
document.getElementById('add-resource-form').addEventListener('submit', event => {
    event.preventDefault();
    const name = document.getElementById('resource-name').value;
    const url = document.getElementById('resource-url').value;
    addResource(name, url);
});

// Charger les catégories et les ressources au chargement de la page
window.addEventListener('load', () => {
    fetchTechnologies();
    fetchCategories();
    fetchResources();
});