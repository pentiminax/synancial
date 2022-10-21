const {initializeCustomSort, initializeTableSort} = require("../../functions/table_sort");
const {ajaxFetch} = require("../../functions/request");
const {$} = require("../../functions/dom");
const {enableTooltips} = require("../../functions/bootstrap");

document.addEventListener('DOMContentLoaded', async () => {
    const marketList = new MarketList();
    await marketList.initialize();
    initializeCustomSort();
    initializeTableSort('table.market-table');
});

class MarketList {
    async initialize() {
        const response = await ajaxFetch('/api/users/me/views/wallet/market', 'GET');

        const json = await response.json();

        $('.market-area').innerHTML = json.result['investments'];
    }
}