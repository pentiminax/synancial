import {Chart, registerables} from "chart.js";

document.addEventListener('DOMContentLoaded', async () => {
    const dashboard = new Dashboard();

    Chart.register(...registerables);

    await dashboard.fetchUserAccounts();
    await dashboard.loadAllocationChart();
});

class Dashboard {
    checkingShare;
    marketShare;
    savingsShare;

    totalAmount;
    netWorth;
    financialAssets;

    async fetchUserAccounts() {
        const response = await fetch('/api/users/me/views/dashboard', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();

        this.checkingShare = json.result.distribution.checking.share;
        this.marketShare = json.result.distribution.market.share;
        this.savingsShare = json.result.distribution.savings.share;

        this.totalAmount = json.result.total.amount;
        this.netWorth = json.result.total.netWorth;
        this.financialAssets = json.result.total.financialAssets;

        const totalWealth = document.querySelector('.total-wealth');
        const netWorth = document.querySelector('.net-worth');
        const financialAssets = document.querySelector('.financial-assets');

        totalWealth.innerHTML = `<b>${this.totalAmount.toFixed()} €</b>`;
        netWorth.innerHTML = `<b>${this.netWorth.toFixed()} €</b>`;
        financialAssets.innerHTML = `<b>${this.financialAssets.toFixed()} €</b>`;
    }

    async loadAllocationChart() {
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

        const allocationChart = new Chart(
            document.querySelector('#allocationChart'), {
                type: 'doughnut',
                data: data,
                options: {
                    cutout: 150,
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
}