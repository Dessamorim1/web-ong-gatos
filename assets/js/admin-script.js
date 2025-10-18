function renderPetList() {
    fetch('admin-pets.php?acao=listar')
        .then(response => response.json())
        .then(pets => {
            const tableBody = document.getElementById('pet-list-body');
            tableBody.innerHTML = '';

            if (pets.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nenhum pet cadastrado.</td></tr>';
                return;
            }

            pets.forEach(pet => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${pet.gato_id}</td>
                    <td>${pet.nome}</td>
                    <td>${pet.genero} / ${pet.idade} anos</td>
                    <td>
                        <button class="btn-edit" onclick="editPet(${pet.gato_id})">Editar</button>
                        <button class="btn-delete" onclick="deletePet(${pet.gato_id})">Excluir</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        });
}

window.deletePet = function (id) {
    if (!confirm("Tem certeza que deseja EXCLUIR este pet?")) return;

    fetch(`admin-pets.php?acao=excluir&id=${id}`)
        .then(response => response.text())
        .then(msg => {
            document.getElementById('pet-form-message').textContent = msg;
            document.getElementById('pet-form-message').style.color = 'orange';
            renderPetList();
            cancelEdit();
        });
}

window.editPet = function (id) {
    fetch(`admin-pets.php?acao=buscar&id=${id}`)
        .then(response => response.json())
        .then(pet => {
            document.getElementById('pet-id').value = pet.gato_id;
            document.getElementById('pet-name').value = pet.nome;
            document.getElementById('pet-gender').value = pet.genero;
            document.getElementById('pet-age').value = pet.idade;
            document.getElementById('pet-description').value = pet.descricao;
            document.getElementById('pet-image').value = pet.url_foto_principal;
            document.getElementById('pet-special').checked = pet.disponivel_adocao == 1;

            document.getElementById('pet-submit-btn').textContent = 'Salvar Edição';
            document.getElementById('pet-cancel-btn').style.display = 'inline-block';
            document.getElementById('pet-form-message').textContent = 'Você está editando o pet ' + pet.nome;
            document.getElementById('pet-form-message').style.color = 'blue';
        });
}

window.handlePetFormSubmit = function (event) {
    event.preventDefault();

    const formData = new FormData(document.getElementById('pet-form'));

    fetch('admin-pets.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(msg => {
            document.getElementById('pet-form-message').textContent = msg;
            document.getElementById('pet-form-message').style.color = 'green';
            renderPetList();
            cancelEdit();
        });
}

window.cancelEdit = function () {
    document.getElementById('pet-form').reset();
    document.getElementById('pet-id').value = '';
    document.getElementById('pet-submit-btn').textContent = 'Adicionar Pet';
    document.getElementById('pet-cancel-btn').style.display = 'none';
    document.getElementById('pet-form-message').textContent = '';
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('pet-form')) {
        document.getElementById('pet-form').addEventListener('submit', handlePetFormSubmit);
        document.getElementById('pet-cancel-btn').addEventListener('click', cancelEdit);
        renderPetList();
    }

    const showPasswordCheckbox = document.getElementById('show-password');
    const passwordInput = document.getElementById('password');
    if (showPasswordCheckbox && passwordInput) {
        showPasswordCheckbox.addEventListener('change', () => {
            passwordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
        });
    }
});

function logoutAdmin() {
    Swal.fire({
        title: "Tem certeza que deseja sair do painel administrativo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, sair",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "logout.php"; 
        }
    });
}

window.logoutAdmin = logoutAdmin;

