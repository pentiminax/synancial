import {Chart, registerables} from "chart.js";
const {initializeCustomSort, initializeTableSort} = require("../../functions/table_sort");
const {ajaxFetch} = require("../../functions/request");
const {$} = require("../../functions/dom");

document.addEventListener('DOMContentLoaded', async () => {
    const marketList = new MarketView();

    Chart.register(...registerables);

    await marketList.initialize();
    await marketList.loadDistributionChart();

    initializeCustomSort();
    initializeTableSort('table.market-table');
});

class MarketView {
    /** @type Number */
    accountId;

    /** @type String */
    accountType;

    /** @type Array */
    distribution;

    constructor() {
        const applicationData = $('.application-data');
        this.accountId = Number(applicationData.dataset.accountId);
        this.accountType = applicationData.dataset.accountType;
    }

    async initialize() {
        const response = await ajaxFetch(`/api/users/me/views/wallet/market/${this.accountId}`, 'GET');
        const json = await response.json();
        const totalValue = json.result['totalValue'];

        this.distribution = json.result['distribution'];

        $('.market-area').innerHTML = json.result['investments'];

        if ('market' !== this.accountType) {
            return;
        }

        const totalAnnualDividend = json.result['totalAnnualDividend'];
        const totalDividendYield = Number((totalAnnualDividend / totalValue) * 100).toFixed(2);

        $('.total-value').innerHTML = this.processCardTitle(`~ ${json.result['totalValue']} €`);
        $('.total-annual-dividend').innerHTML = this.processCardTitle(`~ ${json.result['totalAnnualDividend']} €`);
        $('.total-dividend-yield').innerHTML = this.processCardTitle(`${totalDividendYield} %`);

        $('.number-of-assets').innerHTML = json.result['numberOfAssets'];
    }

    async loadDistributionChart() {
        const backgroundColors = [];

        for (let i = 0; i < this.distribution['labels'].length; i++) {
            const backgroundColor = Math.floor(Math.random() * 16777215).toString(16);
            backgroundColors.push(`#${backgroundColor}`);
        }

        const data = {
            labels: this.distribution['labels'],
            datasets: [{
                backgroundColor: backgroundColors,
                data: this.distribution['datasets']['data'],
                label: 'Dataset',
            }]
        };

        new Chart(
            document.querySelector('#distributionChart'), {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.label} - ${context.formattedValue} %`;
                                }
                            }
                        }
                    }
                }
            }
        );
    }

    processCardTitle(value) {
        return `<h2 class="card-title fw-bold text-primary">${value}</h2>`;
    }
}