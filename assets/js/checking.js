import {Chart, registerables} from "chart.js";

document.addEventListener('DOMContentLoaded', async () => {
    Chart.register(...registerables);

    const checking = new Checking();

    await checking.fetchCheckingAccounts();
});

class Checking {
    /**
     * @var {HTMLElement}
     */
    checkingAccountsList;

    constructor() {
        this.checkingAccountsList = document.querySelector('.checking-accounts-list');
    }

    listenCheckingAccounts() {
        const checkingAccounts = document.querySelectorAll('.checking-account');

        if (!checkingAccounts) {
            return;
        }

        checkingAccounts.forEach(account => {
            account.addEventListener('click', (e) => {
                const accountId = account.dataset.id;
                window.location.href = `/wallet/checking/${accountId}`;
            });
        });
    }

    async fetchCheckingAccounts() {
        const response = await fetch('/api/users/me/views/wallet/checking', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();

        this.checkingAccountsList.innerHTML = json.result;

        this.listenCheckingAccounts();
    }
}