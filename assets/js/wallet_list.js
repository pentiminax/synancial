document.addEventListener('DOMContentLoaded', async () => {
    const walletList = new WalletList();
});

class WalletList {
    constructor() {
        this.listenConnectorSyncButtons();
    }

    listenConnectorSyncButtons() {
        const connectorSyncButtons = document.querySelectorAll('.connector-sync-button');

        connectorSyncButtons.forEach(button => {
           button.addEventListener('click', (e) => {
               const uuid = e.target.dataset.uuid;
               window.open(`/wallet/add/${uuid}`, '_blank').focus();
           })
        });
    }
}