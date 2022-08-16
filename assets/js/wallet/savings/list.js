const {initializeCustomSort, initializeTableSort} = require("../../functions/table_sort");

document.addEventListener('DOMContentLoaded', async () => {
    const savingsList = new SavingsList();
    await savingsList.fetchSavingsAccounts();
    initializeCustomSort();
    initializeTableSort('table.savings-table');
});

class SavingsList {
    /**
     * {HTMLElement}
     */
    savingsAccountsList;

    constructor() {
        this.savingsAccountsList = document.querySelector('.saving-accounts-list');
    }

    async fetchSavingsAccounts() {
        const response = await fetch('/api/users/me/views/wallet/savings', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();

        this.savingsAccountsList.innerHTML = json.result;
        this.listenSavingsAccounts();
    }

    listenSavingsAccounts() {
        const savingsAccount = document.querySelectorAll('.savings-account');

        if (!savingsAccount) {
            return;
        }

        savingsAccount.forEach(account => {
            account.addEventListener('click', (e) => {
                const accountId = account.dataset.id;
                window.location.href = `/wallet/savings/${accountId}`;
            });
        });
    }
}