import {$} from "../../functions/dom";
import {ajaxFetch} from "../../functions/request";

document.addEventListener('DOMContentLoaded', async () => {
    const loansList = new LoansList();

    await loansList.fetchLoansAccounts();
});

class LoansList {
    /**
     * @type {HTMLElement}
     */
    loansAccountList;

    constructor() {
        this.loansAccountList = $('.loans-accounts-list');
    }

    async fetchLoansAccounts() {
        const response = await ajaxFetch('/api/users/me/views/wallet/loans', 'GET');

        const json = await response.json();

        this.loansAccountList.innerHTML = json.result;
    }
}