// Função para preencher o formulário na edição
function editPet(id) {
    fetch('admin-pets.php?buscar=' + id)
        .then(response => response.json())
        .then(pet => {
            document.getElementById('pet-id').value = pet.gato_id;
            document.getElementById('pet-name').value = pet.nome;
            document.getElementById('pet-gender').value = pet.genero;
            document.getElementById('pet-age').value = pet.idade;
            document.getElementById('pet-description').value = pet.descricao;
            document.getElementById('imagem-antiga').value = pet.url_foto_principal;
            document.getElementById('pet-special').checked = pet.disponivel_adocao == 1;
            document.getElementById('pet-image-preview').src = pet.url_foto_principal;
        });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Deseja excluir este pet?',
        text: "Esta ação não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redireciona para o PHP que exclui o pet
            window.location.href = 'admin-pets.php?excluir=' + id;
        }
    });
}

