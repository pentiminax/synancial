const TableSort = require('tablesort');

document.addEventListener('DOMContentLoaded', async () => {
    const market = new Market();
    await market.loadMarketAccounts();
    market.initializeCustomSort();
    market.initializeDataTables();
});

class Market {
    /**
     * @var {HTMLElement}
     */
    marketArea;

    constructor() {
        this.marketArea = document.querySelector('.market-area');
    }

    initializeCustomSort() {
        TableSort.extend('number', function (item) {
            return Number(item);
        }, function (a, b) {
            return a - b;
        });
    }

    initializeDataTables() {
        const tables = document.querySelectorAll('table.market-table');

        tables.forEach(table => {
            new TableSort(table);
        })
    }

    async loadMarketAccounts() {
        const response = await fetch('/api/users/me/views/wallet/market', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();

        this.marketArea.innerHTML = json.result;
    }
}