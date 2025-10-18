const catsGrid = document.getElementById('catsGrid');
const searchInput = document.getElementById('searchInput');
const sexFilter = document.getElementById('sexFilter');
const noResults = document.getElementById('noResults');

let cats = [];

async function carregarCats() {
  try {
    const response = await fetch('getgatos.php');
    cats = await response.json();
    renderCats(cats);
  } catch (error) {
    console.error('Erro ao carregar gatos:', error);
  }
}

function renderCats(filteredCats) {
  catsGrid.innerHTML = '';

  if (filteredCats.length === 0) {
    noResults.style.display = 'block';
    return;
  }

  noResults.style.display = 'none';


  filteredCats.forEach(cat => {
    const card = document.createElement('div');
    card.className = 'cat-card';
    card.innerHTML = `
    <div class="cat-image-container">
      <img src="${cat.image}" alt="${cat.name} - gatinha para adoção" class="cat-image">
      ${cat.special ? '<span class="special-badge">Especial</span>' : ''}

    </div>
    <div class="cat-content">
      <div class="cat-header">
        <h3 class="cat-name">${cat.name}</h3>
        <span class="cat-gender">${cat.sex}</span>
      </div>
      <div class="cat-info">
        <span>📅 ${cat.age}</span>
      </div>
      <p class="cat-description">${cat.description}</p>
      <div class="cat-actions">
        <a href="adotar.html?catName=${encodeURIComponent(cat.name)}" class="btn btn-primary btn-full">Saiba Mais</a>
        <button class="btn btn-icon">♡</button>
      </div>
    </div>
  `;
    catsGrid.appendChild(card);
  });
}

function filterCats() {
  const searchTerm = searchInput.value.toLowerCase();
  const sexValue = sexFilter.value;

  const filtered = cats.filter(cat => {
    const matchesSearch = cat.name.toLowerCase().includes(searchTerm);
    const matchesSex = sexValue === 'todos' || cat.sex === sexValue;
    return matchesSearch && matchesSex;
  });

  renderCats(filtered);
}

searchInput.addEventListener('input', filterCats);
sexFilter.addEventListener('change', filterCats);

carregarCats();
