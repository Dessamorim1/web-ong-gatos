const CLASSE_VISIVEL = 'modal-visivel';

function initModalListeners() {
    const linksAbrir = document.querySelectorAll('a[href*="-modal"]');

    linksAbrir.forEach(linkAbrir => {
        linkAbrir.removeEventListener('click', openModalHandler);
        linkAbrir.addEventListener('click', openModalHandler);
    });

    function openModalHandler(e) {
        e.preventDefault();
        const modalId = this.getAttribute('href').substring(1);
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add(CLASSE_VISIVEL);
        } else {
            console.error(`Modal com ID #${modalId} não encontrado.`);
        }
    }

    document.querySelectorAll('.modal').forEach(modal => {
        const linkFechar = modal.querySelector('.modal-close');

        if (linkFechar) {
            linkFechar.addEventListener('click', e => {
                e.preventDefault();
                modal.classList.remove(CLASSE_VISIVEL);
            });
        }

        modal.addEventListener('click', e => {
            if (e.target.classList.contains('modal')) {
                modal.classList.remove(CLASSE_VISIVEL);
            }
        });
    });
}

function loadHTMLComponent(file, elementId) {
    return new Promise((resolve, reject) => {
        const targetElement = document.getElementById(elementId);

        if (!targetElement) {
            console.error(`Elemento com ID #${elementId} não encontrado.`);
            return reject(new Error('Placeholder não encontrado.'));
        }

        fetch(file)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ' + response.status + ' ao carregar: ' + file);
                }
                return response.text();
            })
            .then(html => {
                targetElement.innerHTML = html;
                resolve();
            })
            .catch(error => {
                console.error('Falha ao carregar o componente:', error);
                reject(error);
            });
    });
}

// Função para exibir cupom via SweetAlert
function showCoupon() {
    Swal.fire({
        title: '🎉 Cupom na loja PETZ!',
        html: `
      Você ganhou um cupom exclusivo: <strong>LARBOLAPELOS</strong><br>
      Aproveite <strong>10% de desconto</strong> em produtos na loja PETZ!
    `,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Aproveite Agora',
        cancelButtonText: 'Fechar',
    }).then((result) => {
        if (result.isConfirmed) {
            window.open('https://www.petz.com.br/parceiro/LARBOLAPELOS', '_blank');
        }
    });
}

// Inicialização do DOM
document.addEventListener('DOMContentLoaded', function () {
    loadHTMLComponent('includes/header.html', 'header-placeholder')
        .catch(() => console.log('Continuando, mesmo sem header.'));
    loadHTMLComponent('includes/footer.html', 'footer-placeholder')
        .then(() => {
            initModalListeners();
        })
        .catch(() => console.log('Continuando, mesmo sem footer.'));

    // Associa os botões à função do cupom
    const couponButtons = document.querySelectorAll('.heart-btn'); // ou outra classe
    couponButtons.forEach(button => {
        button.addEventListener('click', showCoupon);
    });

    if (window.location.hash.endsWith('-modal')) {
        history.replaceState(null, null, ' ');
    }
});


document.addEventListener('DOMContentLoaded', function () {
    loadHTMLComponent('includes/header.html', 'header-placeholder')
        .catch(() => console.log('Continuando, mesmo sem header.'));
    loadHTMLComponent('includes/footer.html', 'footer-placeholder')
        .then(() => {
            initModalListeners();
        })
        .catch(() => console.log('Continuando, mesmo sem footer.'));

    const couponButtons = document.querySelectorAll('.heart-btn'); // ou outra classe
    couponButtons.forEach(button => {
        button.addEventListener('click', showCoupon);
    });

    if (window.location.hash.endsWith('-modal')) { history.replaceState(null, null, ' '); }

});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

function scrollToAnchorAfterHeader(id) {
    const element = document.getElementById(id);
    if (!element) return;

    const header = document.querySelector('#header-placeholder');

    // Se o header ainda não está carregado, tenta de novo em 50ms
    if (!header || header.offsetHeight === 0) {
        setTimeout(() => scrollToAnchorAfterHeader(id), 50);
        return;
    }

    const headerHeight = header.offsetHeight; // pega a altura real
    const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;

    window.scrollTo({
        top: elementPosition - headerHeight,
        behavior: 'smooth'
    });
}

window.addEventListener('load', () => {
    if (window.location.hash) {
        const id = window.location.hash.substring(1);
        scrollToAnchorAfterHeader(id);
    }
});

window.addEventListener('DOMContentLoaded', () => {
    if (successMessage) {
        Swal.fire({
            title: successMessage,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }

    if (errorMessage) {
        Swal.fire({
            title: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
});

function copyText(text, button) {
    navigator.clipboard.writeText(text).then(function () {
        // Alterar o botão temporariamente
        const originalText = button.innerHTML;
        button.innerHTML = '✓';
        button.style.color = '#10b981';

        setTimeout(function () {
            button.innerHTML = originalText;
            button.style.color = '';
        }, 2000);

        Swal.fire({
            title: 'Copiado!',
            text: 'O texto foi copiado para a área de transferência.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            width: '450px',
            padding: 'rem'
        });

    }).catch(function () {
        Swal.fire({
            title: 'Erro',
            text: 'Não foi possível copiar. Por favor, copie manualmente: ' + text,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function alterarStatus(id, novoStatus) {
    // Substitui o confirm pelo Swal
    Swal.fire({
        title: `Deseja alterar o status para "${novoStatus}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch('update-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&status=${encodeURIComponent(novoStatus)}`
        })
            .then(res => res.text())
            .then(res => {
                if (res.trim() === 'success') {
                    Swal.fire({
                        title: 'Status atualizado com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => location.reload()); // recarrega após fechar o alerta
                } else {
                    Swal.fire({
                        title: 'Erro ao atualizar status.',
                        text: res,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.log(res);
                }
            })
            .catch(err => {
                console.error('Erro:', err);
                Swal.fire({
                    title: 'Falha na comunicação com o servidor.',
                    text: err,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    });
}

function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("adotantesTable");
    const trs = table.getElementsByTagName("tr");

    for (let i = 1; i < trs.length; i++) { // começa de 1 pra pular o header
        const tds = trs[i].getElementsByTagName("td");
        let found = false;
        for (let j = 0; j < tds.length - 1; j++) { // -1 pra ignorar a coluna de ações
            if (tds[j].textContent.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        trs[i].style.display = found ? "" : "none";
    }
}

