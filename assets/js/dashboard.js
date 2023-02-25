import {Chart, registerables} from "chart.js/auto";
import {$} from "./functions/dom";
import {ajaxFetch} from "./functions/request";
import {getDashboardData} from "./functions/api";

document.addEventListener('DOMContentLoaded', async () => {
    const dashboard = new Dashboard();

    Chart.register(...registerables);

    await dashboard.initialize();
});

class Dashboard {
    allocationChart;
    checkingShare;

    /** @type {DashboardData} */
    data;

    marketShare;
    savingsShare;
    totalAmount;
    netWorth;
    financialAssets;

    async fetchFortuneoNews() {
        const response = await ajaxFetch('/api/fortuneo/news');
        const json = await response.json();

        return json.result;
    }

    async initialize() {
        this.data = await getDashboardData();

        $('.total-wealth').innerHTML = `<span data-secret-mode="true">${this.data.total.amount.toFixed()} €</span>`;
        $('.net-worth').innerHTML = `<span data-secret-mode="true">${this.data.total.netWorth.toFixed()} €</span>`;
        $('.financial-assets').innerHTML = `<span data-secret-mode="true">${this.data.total.financialAssets.toFixed()} €</span>`;

        const news = await this.fetchFortuneoNews();

        this.processFortuneoNews(news);

        await this.loadAllocationChart();
    }

    async loadAllocationChart() {
        const data = {
            labels: this.data.allocationChart.labels,
            datasets: [{
                backgroundColor: [
                    '#338AFF',
                    '#B233FF',
                    '#09A72B',
                    '#4040B5',
                ],
                data: [
                    this.data.distribution.checking.share.toFixed(0),
                    this.data.distribution.market.share.toFixed(0),
                    this.data.distribution.savings.share.toFixed(0),
                    this.data.distribution.crowdlendings.share.toFixed(0)
                ],
                label: 'Dataset',
            }]
        };

        console.log(data);
        new Chart(
            document.querySelector('#allocationChart'), {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "right"
                        },
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

    /**
     * @param {Array} news
     */
    processFortuneoNews(news) {
        if (!news) return;

        const ul = $('.fortuneo-news-list');

        ul.querySelector('.placeholder-glow').remove();

        news.forEach(article => {
            const li = document.createElement('li');
            li.classList.add('list-group-item');

            const a = document.createElement('a');
            a.classList.add('text-decoration-none');
            a.innerText = article.title;
            a.href = article.link;
            a.target = '_blank';

            li.appendChild(a);

            ul.appendChild(li);
        });
    }
}