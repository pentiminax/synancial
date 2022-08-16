const {initializeCustomSort, initializeTableSort} = require("../../functions/table_sort");
const {ajaxFetch} = require("../../functions/request");

document.addEventListener('DOMContentLoaded', async () => {
    const marketList = new MarketList();
    await marketList.loadMarketAccounts();
    initializeCustomSort();
    initializeTableSort('table.market-table');
});

class MarketList {
    /**
     * @var {HTMLElement}
     */
    marketArea;

    constructor() {
        this.marketArea = document.querySelector('.market-area');
    }

    async loadMarketAccounts() {
        const response = await ajaxFetch('/api/users/me/views/wallet/market', 'GET');

        const json = await response.json();

        this.marketArea.innerHTML = json.result;
    }
}