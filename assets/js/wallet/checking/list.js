import {Chart, registerables} from "chart.js";
import {$} from "../../functions/dom";
import {ajaxFetch} from "../../functions/request";

document.addEventListener('DOMContentLoaded', async () => {
    Chart.register(...registerables);

    const checkingList = new CheckingList();

    await checkingList.fetchCheckingAccounts();
});

class CheckingList {
    /**
     * @var {HTMLElement}
     */
    checkingAccountsList;

    constructor() {
        this.checkingAccountsList = $('.checking-accounts-list');
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
        const response = await ajaxFetch('/api/users/me/views/wallet/checking', 'GET');

        const json = await response.json();

        this.checkingAccountsList.innerHTML = json.result;

        this.listenCheckingAccounts();
    }
}