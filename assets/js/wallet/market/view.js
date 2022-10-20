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

        const distributionChart = new Chart(
            document.querySelector('.distribution-chart'), {
                type: 'doughnut',
                data: data,
                options: {
                    cutout: 80,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        legendPlugin: {
                            container: '.distribution-chart-legend'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.label} - ${context.formattedValue} %`;
                                }
                            }
                        }
                    }
                },
                plugins: [this.legendPlugin]
            }
        );
    }

    legendPlugin = {
        id: 'legendPlugin',
        /**
         * @param {Chart} chart
         * @param args
         * @param options
         */
        afterUpdate(chart, args, options) {
            /** @type HTMLUListElement */
            const ul = document.querySelector(options.container);

            while (ul.firstChild) {
                ul.firstChild.remove();
            }

            chart.legend.legendItems.forEach(item => {
                const li = document.createElement('li');
                li.style.alignItems = 'center';
                li.style.cursor = 'pointer';
                li.style.display = 'flex';
                li.style.flexDirection = 'row';
                li.style.marginLeft = '10px';
                li.onclick = () => {
                    chart.toggleDataVisibility(item.index);
                    chart.update();
                }

                const boxSpan = document.createElement('span');
                boxSpan.style.background = item.fillStyle;
                boxSpan.style.borderColor = item.strokeStyle;
                boxSpan.style.borderWidth = item.lineWidth + 'px';
                boxSpan.style.display = 'inline-block';
                boxSpan.style.height = '20px';
                boxSpan.style.marginRight = '10px';
                boxSpan.style.width = '20px';

                const textContainer = document.createElement('p');
                textContainer.style.color = item.fontColor;
                textContainer.style.margin = 0;
                textContainer.style.padding = 0;
                textContainer.style.textDecoration = item.hidden ? 'line-through' : '';

                const text = document.createTextNode(item.text);
                textContainer.appendChild(text);

                li.appendChild(boxSpan);
                li.appendChild(textContainer);
                ul.appendChild(li);
            });
        }
    }

    processCardTitle(value) {
        return `<h2 class="card-title fw-bold text-primary">${value}</h2>`;
    }
}