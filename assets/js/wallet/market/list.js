const {initializeCustomSort, initializeTableSort} = require("../../functions/table_sort");
const {ajaxFetch} = require("../../functions/request");
const {$} = require("../../functions/dom");

document.addEventListener('DOMContentLoaded', async () => {
    const marketList = new MarketList();
    await marketList.loadMarketAccounts();
    initializeCustomSort();
    initializeTableSort('table.market-table');
});

class MarketList {
    async loadMarketAccounts() {
        const response = await ajaxFetch('/api/users/me/views/wallet/market', 'GET');

        const json = await response.json();

        $('.market-area').innerHTML = json.result['investments'];
    }
}