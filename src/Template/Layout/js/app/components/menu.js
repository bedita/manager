/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Element/menu/menu.twig
 *
 * <menu> component
 *
 */

export default {
    data() {
        return {
            isLookUpByIdPopupVisibile: false,
            id: '',
        };
    },
    methods: {
        captureEnter(e) {
            if (!this.id) {
                return;
            }
            if (e.keyCode === 13) {
                this.go();
            }
        },
        go() {
            let url = BEDITA.base + "/view/" + this.id;
            window.location.href = url;
        },
    },
}
