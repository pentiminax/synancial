import TomSelect from "tom-select";

document.addEventListener('DOMContentLoaded', () => {
    new AssetsAddCrowdlending();
});

class AssetsAddCrowdlending {
    /** @type {HTMLFormElement} */
    formCrowdlendingAdd = document.querySelector('#form_crowdlending_add');

    /** @type {TomSelect} */
    selectPlatform;

    constructor() {
        this.selectPlatform = new TomSelect('#platform', {
            create: true,
            onOptionAdd: (value) => {
                const init = {
                    body: JSON.stringify({
                        name: value
                    }),
                    method: 'POST'
                };

                fetch('/crowdlending_platform', init)
                    .then(response => response.json())
                    .then(json => {
                        this.selectPlatform.updateOption(value,{ text: value, value: String(json['result']) });
                        this.selectPlatform.refreshItems();
                    });
            },
        });
    }
}