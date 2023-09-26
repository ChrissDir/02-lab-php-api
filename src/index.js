const API_URL = 'http://php-dev-2.online/';

// Fonctions pour TechnologyController
async function fetchTechnologies() {
    try {
        const response = await fetch(API_URL + 'technologies');
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const technologies = await response.json();
        displayTechnologies(technologies);
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function addTechnology(name, logo, category_id) {
    try {
        const response = await fetch(API_URL + 'technologies', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, logo, category_id }),
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchTechnologies();
        populateTechnologyDropdown();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function updateTechnology(id, name, logo, category_id) {
    try {
        const response = await fetch(API_URL + 'technologies/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, logo, category_id }),
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchTechnologies();
        populateTechnologyDropdown();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function deleteTechnology(id) {
    try {
        const response = await fetch(API_URL + 'technologies/' + id, {
            method: 'DELETE',
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchTechnologies();
        populateTechnologyDropdown();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

// Fonctions pour ResourceController
async function fetchResources() {
    try {
        const response = await fetch(API_URL + 'resources');
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const resources = await response.json();
        displayResources(resources);
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function addResource(name, url, technology_id) {
    try {
        const response = await fetch(API_URL + 'resources', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, url, technology_id }),
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchResources();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function updateResource(id, name, url, technology_id) {
    try {
        const response = await fetch(API_URL + 'resources/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, url, technology_id }),
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchResources();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function deleteResource(id) {
    try {
        const response = await fetch(API_URL + 'resources/' + id, {
            method: 'DELETE',
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchResources();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

// Fonctions pour CategoryController
async function fetchCategories() {
    try {
        const response = await fetch(API_URL + 'categories');
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const categories = await response.json();
        displayCategories(categories);
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function addCategory(name) {
    try {
        const response = await fetch(API_URL + 'categories', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name }),
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchCategories();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function updateCategory(id, name) {
    try {
        const response = await fetch(API_URL + 'categories/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name }),
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchCategories();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

async function deleteCategory(id) {
    try {
        const response = await fetch(API_URL + 'categories/' + id, {
            method: 'DELETE',
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const result = await response.json();
        alert(result.message);
        await fetchCategories();
    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
    }
}

// Fonctions pour afficher les données dans l'interface utilisateur
function displayTechnologies(technologies) {
    const technologiesTableBody = document.getElementById('technologies-table').getElementsByTagName('tbody')[0];
    technologiesTableBody.innerHTML = '';  // Clear the table body
    technologies.forEach(technology => {
        const row = technologiesTableBody.insertRow();
        const cellId = row.insertCell(0);
        const cellName = row.insertCell(1);
        const cellLogo = row.insertCell(2);
        const cellCategoryId = row.insertCell(3);
        const cellActions = row.insertCell(4);

        cellId.textContent = technology.id;
        cellName.textContent = technology.name;
        cellLogo.innerHTML = `<img src="${technology.logo}" alt="${technology.name}" style="width:50px;height:50px;">`;
        cellCategoryId.textContent = technology.category_id;
        cellActions.innerHTML = '<button class="btn btn-danger btn-sm" onclick="deleteTechnology(' + technology.id + ')">Supprimer</button>';
    });
}

function displayResources(resources) {
    const resourceList = document.getElementById('resource-list');
    resourceList.innerHTML = '';  // Clear the list
    resources.forEach(resource => {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item';
        listItem.textContent = resource.name + ' (' + resource.url + ')';
        resourceList.appendChild(listItem);
    });
}

function displayCategories(categories) {
    const categoryList = document.getElementById('category-list');
    categoryList.innerHTML = '';  // Clear the list
    categories.forEach(category => {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item';
        listItem.textContent = category.name;
        categoryList.appendChild(listItem);
    });
}

function populateTechnologyDropdown() {
    fetchTechnologies()
        .then(technologies => {
            const technologyDropdown = document.getElementById('technology-id');
            technologyDropdown.innerHTML = '';  // Clear the dropdown
            technologies.forEach(technology => {
                const option = document.createElement('option');
                option.value = technology.id;
                option.textContent = technology.name;
                technologyDropdown.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching technologies:', error);
        });
}

// Event listeners for form submissions
document.getElementById('add-resource-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const name = document.getElementById('resource-name').value;
    const url = document.getElementById('resource-url').value;
    const technology_id = document.getElementById('technology-id').value;
    addResource(name, url, technology_id);
});

window.addEventListener('load', () => {
    console.log('Page loaded, fetching data...');
    fetchTechnologies();
    fetchCategories();
    fetchResources();
    populateTechnologyDropdown();
});