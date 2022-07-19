import {Chart, registerables} from "chart.js";

let checkingView;

document.addEventListener('DOMContentLoaded', async () => {
    Chart.register(...registerables);

    const accountId = document.querySelector('.checking-view-data').dataset.accountId;

    checkingView = new CheckingView(accountId);

    await checkingView.loadUserTimeSeries();
    checkingView.loadCheckingViewLineChart();
    checkingView.loadCheckingViewBarChart();
});

document.addEventListener('scroll', () => {
    const {scrollTop, scrollHeight, clientHeight} = document.documentElement;

    if (scrollTop + clientHeight >= scrollHeight - 5) {
        checkingView.currentPage++;
        checkingView.loadTransactions();
    }
}, {
    passive: true
})

class CheckingView {
    accountId;
    currentPage = 0;
    limit = 10;
    transactions;


    /**
     * @type {HTMLElement}
     */
    loader;

    /**
     * @var {Array}
     */
    timeserieBarValues = [];

    /**
     * @var {Array}
     */
    timeserieBarLabels = [];

    /**
     * @var {Array}
     */
    timeserieLineValues = [];

    /**
     * @var {Array}
     */
    timeserieLineLabels = [];

    yBarChartMax;

    yBarChartMin;

    constructor(accountId) {
        this.loader = document.querySelector('.loader')
        this.accountId = accountId;
        this.listenSelectOperationType();
        this.listenSelectWording();
    }

    listenSelectWording() {
        const selectWording = document.querySelector('.select-wording');

        if (!selectWording) {
            return;
        }

        selectWording.addEventListener('change', e => {
            const value = e.target.value.toUpperCase();

            if (undefined === this.transactions) {
                this.transactions = document.querySelectorAll('.transaction');
            }

            if ('ALL' === value) {
                this.transactions.forEach(transaction => {
                    transaction.classList.remove('d-none');
                });
                return;
            }

            this.transactions.forEach(transaction => {
                const transactionWordings = transaction.querySelector('.card-body .card-title').dataset.wording.toUpperCase();

                transactionWordings.includes(value) ? transaction.classList.remove('d-none') : transaction.classList.add('d-none');
            });
        });
    }

    listenSelectOperationType() {
        const selectOperationType = document.querySelector('.select-operation-type');

        if (!selectOperationType) {
            return;
        }

        selectOperationType.addEventListener('change', e => {
            const value = e.target.value.toUpperCase();

            if (undefined === this.transactions) {
                this.transactions = document.querySelectorAll('.transaction');
            }

            if ('ALL' === value) {
                this.transactions.forEach(transaction => {
                    transaction.classList.remove('d-none');
                });
                return;
            }

            this.transactions.forEach(transaction => {
                const transactionTextClasses = transaction.querySelector('.card-body .card-text span').classList;

                if ('CREDIT' === value) {
                    transactionTextClasses.contains('text-success')
                        ? transaction.classList.remove('d-none')
                        : transaction.classList.add('d-none');
                    return;
                }

                if ('DEBIT' === value) {
                    transactionTextClasses.contains('text-danger')
                        ? transaction.classList.remove('d-none')
                        : transaction.classList.add('d-none');
                }
            });
        });
    }

    async loadUserTimeSeries() {
        const response = await fetch(`/api/users/me/timeseries/${this.accountId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();
        const result = json.result;
        const timeseriesLine = result.line;
        const timeseriesBar = result.bar;

        this.yBarChartMax = result.max;
        this.yBarChartMin = result.min;

        timeseriesLine.forEach(timeserie => {
            this.timeserieLineValues.push(Number(timeserie.value).toFixed());
        });

        timeseriesLine.forEach(timeserie => {
            this.timeserieLineLabels.push(timeserie.date);
        });

        timeseriesBar.forEach(timeserie => {
            this.timeserieBarValues.push(Number(timeserie.value).toFixed());
        });

        timeseriesBar.forEach(timeserie => {
            this.timeserieBarLabels.push(timeserie.date);
        });
    }

    loadCheckingViewLineChart() {
        const data = {
            labels: this.timeserieLineLabels,
            datasets: [{
                borderColor: '#0d6efd',
                lineTension: 0.3,
                data: this.timeserieLineValues,
            }]
        };

        new Chart(
            document.querySelector('#checkingViewLineChart'), {
                type: 'line',
                data: data,
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                        }
                    }
                }
            }
        );
    }

    loadCheckingViewBarChart() {
        const data = {
            labels: this.timeserieBarLabels,
            datasets: [{
                backgroundColor: '#0d6efd',
                barThickness: 10,
                borderColor: '#0d6efd',
                lineTension: 0.3,
                data: this.timeserieBarValues
            }]
        };

        new Chart(
            document.querySelector('#checkingViewBarChart'), {
                type: 'bar',
                data: data,
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            max: this.yBarChartMax,
                            min: this.yBarChartMin,
                        }
                    }
                }
            }
        );
    }

    loadTransactions() {
        this.showLoader();

        setTimeout(async () => {
            const response = await this.getTransactions(this.currentPage, this.limit);
        }, 500);

        this.hideLoader();

    }

    async getTransactions(page, limit) {
        const response = await fetch('');

        if (!response.ok) {
            return;
        }

        return await response.json();
    }

    showLoader() {
        this.loader.classList.remove('d-none');
    }

    hideLoader() {
        this.loader.classList.add('d-none');
    }
}