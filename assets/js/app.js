import '../css/app.scss';

import {Alert} from "./alert";
import {Collapse, Dropdown, Tooltip} from "bootstrap";
import {ajaxFetch} from "./functions/request";

document.addEventListener('DOMContentLoaded', async () => {
    const app = new App();

    await app.fetchUserState();
    await app.toastFlashes();
});

class App {
    /**
     * @var {HTMLElement}
     */
    addAssetButton;

    /**
     * @var {HTMLElement}
     */
    syncButton;

    /**
     * @var {HTMLElement}
     */
    secretModeButton;

    /**
     * @var {HTMLElement}
     */
    secretModeButtonIcon;

    /**
     * @var {HTMLElement}
     */
    syncButtonIcon;

    /**
     * @var {HTMLElement}
     */
    syncButtonSpinner;

    /**
     * @var {Boolean}
     */
    isSyncButtonDisabled;

    /**
     * @var {Array}
     */
    flashes;

    constructor() {
        this.addAssetButton = document.querySelector('.add-asset-button');
        this.secretModeButton = document.querySelector('.secret-mode-button');
        this.syncButton = document.querySelector('.sync-button');
        this.secretModeButtonIcon = this.secretModeButton.querySelector('i');
        this.flashes = JSON.parse(document.querySelector('.flashes').dataset.flashes);

        if (!this.syncButton) {
            this.syncButton = document.querySelector('.disabled-sync-button');
        }

        this.isSyncButtonDisabled = this.syncButton.classList.contains('disabled-sync-button');

        this.syncButtonIcon = this.syncButton.querySelector('i');
        this.syncButtonSpinner = this.syncButton.querySelector('.spinner');

        if (!this.isSyncButtonDisabled) {
            this.syncButton.addEventListener('click', async e => {
                await this.sync();
            });
        }

        this.addAssetButton.addEventListener('click', e => {
            window.location.href = '/wallet/list';
        });

        this.secretModeButton.addEventListener('click', async e => {
            await this.toggleSecretMode();
        });

        new Tooltip(this.syncButton);
    }

    async sync() {
        this.syncButtonIcon.classList.add('d-none');
        this.syncButtonSpinner.classList.remove('d-none');

        const response = await ajaxFetch('/api/users/me/sync', 'PUT');

        if (!response.ok) {
            this.resetSyncButtonState();
            await Alert.toast('Une erreur est survenue', 'error');
            return;
        }

        this.resetSyncButtonState();

        await Alert.toast('Vos comptes sont en cours de synchronisation !');
    }

    async toggleSecretMode() {
        const isSecretModeEnabled = document.body.classList.contains('secret-mode');

        if (isSecretModeEnabled) {
            document.body.classList.remove('secret-mode');
            this.secretModeButtonIcon.classList.add('fa-eye');
            this.secretModeButtonIcon.classList.remove('fa-eye-slash');
        } else {
            document.body.classList.add('secret-mode');
            this.secretModeButtonIcon.classList.remove('fa-eye');
            this.secretModeButtonIcon.classList.add('fa-eye-slash');
        }

        await ajaxFetch('/api/user/secretmode', 'PATCH', {
            'secretMode': !isSecretModeEnabled
        });
    }

    resetSyncButtonState() {
        this.syncButtonIcon.classList.remove('d-none');
        this.syncButtonSpinner.classList.add('d-none');
    }

    async fetchUserState() {
        await ajaxFetch('/api/users/me', 'GET');
    }

    async toastFlashes() {
        const successFlashes = this.flashes.success;

        if (undefined === successFlashes) {
            return;
        }

        await Promise.all(successFlashes.map(async (flash) => {
            await Alert.toast(flash);
        }));
    }
}