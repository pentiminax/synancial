document.addEventListener('DOMContentLoaded', async() => {
    const wallet = new Wallet();
    await wallet.fetchWalletView();
});

class Wallet {
    /**
     * @var {HTMLElement}
     */
    assetsCardBody;

    /**
     * @var {HTMLElement}
     */
    liabilitiesCardBody;

    constructor() {
        this.assetsCardBody = document.querySelector('.assets-card-body');
        this.liabilitiesCardBody = document.querySelector('.liabilities-card-body');
    }

    async fetchWalletView() {
        const response = await fetch('/api/users/me/views/wallet', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: 'GET'
        });

        const json = await response.json();

        this.assetsCardBody.innerHTML = json.result.assets;
        this.liabilitiesCardBody.innerHTML = json.result.liabilities;
    }
}