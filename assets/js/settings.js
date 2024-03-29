import Swal from 'sweetalert2'
import {ajaxFetch} from "./functions/request";

document.addEventListener('DOMContentLoaded', async () => {
    const settings = new Settings();
});

class Settings {
    constructor() {
        const tabSyncEl = document.querySelector('button[data-bs-target="#nav-sync"]');
        const validateAccountForm = document.querySelector('#validateAccountForm');

        tabSyncEl.addEventListener('shown.bs.tab', async () => {
            await this.loadUserConnections();
        });

        validateAccountForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const response = await ajaxFetch('/api/users/me', 'PUT', new FormData(e.target));

            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            await Toast.fire({
                icon: 'success',
                title: "C'est tout bon !",
                timer: 1000
            });

            location.reload();
        })
    }

    async loadUserConnections() {
        const response = await fetch('/api/users/me/connections', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();

        const navSync = document.querySelector('#nav-sync');

        navSync.innerHTML = json.result;

        document.querySelectorAll('.edit-connection').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.editConnection(e.target.dataset['name']);
            });
        });

        document.querySelectorAll('.delete-connection').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.deleteConnection(e.target.dataset['connectionId']);
            });
        });
    }

    deleteConnection(id) {
        const init = {
            method: 'DELETE'
        };

        fetch(`/api/connections/${id}`, init)
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: "Compte supprimé avec succès !",
                        timer: 1000
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Une erreur est survenue !",
                        timer: 1000
                    });
                }
            });
    }

    editConnection(name) {
        window.location.href = `/api/users/me/manage/connection?name=${name}`;
    }
}