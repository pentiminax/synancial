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

        const totalValue = json.result['totalValue'];
        const totalAnnualDividend = json.result['totalAnnualDividend'];
        const totalDividendYield = Number((totalAnnualDividend / totalValue) * 100).toFixed(2);

        $('.market-area').innerHTML = json.result['investments'];
        $('.total-value').innerHTML = this.processCardTitle(`~ ${json.result['totalValue']} €`);
        $('.total-annual-dividend').innerHTML = this.processCardTitle(`~ ${json.result['totalAnnualDividend']} €`);
        $('.total-dividend-yield').innerHTML = this.processCardTitle(`${totalDividendYield} %`);
    }
    
    processCardTitle(value) {
        return `<h2 class="card-title fw-bold text-primary">${value}</h2>`;
    }
}