import {Chart, registerables} from "chart.js";
import {$} from "./functions/dom";
import {ajaxFetch} from "./functions/request";

document.addEventListener('DOMContentLoaded', async () => {
    const dashboard = new Dashboard();

    Chart.register(...registerables);

    await dashboard.initialize();
});

class Dashboard {
    checkingShare;
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
        const response = await ajaxFetch('/api/users/me/views/dashboard');

        const json = await response.json();

        if (!json.result) {
            return;
        }

        const distribution = json.result.distribution;

        this.checkingShare = distribution.checking.share;
        this.marketShare = distribution.market.share;
        this.savingsShare = distribution.savings.share;
        this.totalAmount = json.result.total.amount;
        this.netWorth = json.result.total.netWorth;
        this.financialAssets = json.result.total.financialAssets;

        $('.total-wealth').innerHTML = `<span data-secret-mode="true">${this.totalAmount.toFixed()} €</span>`;
        $('.net-worth').innerHTML = `<span data-secret-mode="true">${this.netWorth.toFixed()} €</span>`;
        $('.financial-assets').innerHTML = `<span data-secret-mode="true">${this.financialAssets.toFixed()} €</span>`;

        const news = await this.fetchFortuneoNews();

        this.processFortuneoNews(news);

        await this.loadAllocationChart();
    }

    async loadAllocationChart() {
        if (null === this.checkingShare || null === this.marketShare || null === this.savingsShare) {
            return;
        }

        const data = {
            labels: [
                'Compte bancaires',
                "Comptes d'investissements",
                "Livrets"
            ],
            datasets: [{
                backgroundColor: [
                    '#338AFF',
                    '#B233FF',
                    '#09A72B'
                ],
                data: [this.checkingShare.toFixed(), this.marketShare.toFixed(0), this.savingsShare.toFixed(0)],
                label: 'Dataset',
            }]
        };

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