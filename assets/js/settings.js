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

        const editButtons = document.querySelectorAll('.edit-connection');

        editButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.editConnection(e.target.dataset.name);
            });
        });
    }

    editConnection(name) {
        window.location.href = `/api/users/me/manage/connection?name=${name}`;
    }
}