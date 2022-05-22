import Swal from "sweetalert2";

export class Alert {
    /**
     *
     * @param title {String}
     * @param icon {SweetAlertIcon}
     * @param position {SweetAlertPosition}
     * @returns {Promise<void>}
     * @constructor
     */
    static async toast(title, icon= 'success', position = 'bottom-right') {
        const Toast = Swal.mixin({
            toast: true,
            position: position,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        await Toast.fire({
            icon: icon,
            title: title
        });
    }
}