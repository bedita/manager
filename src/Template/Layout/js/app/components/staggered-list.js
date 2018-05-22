/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <staggered-list> component used for lists with staggered animation
 *
 */

const NAME = 'staggered';

export default {
    template: `
        <transition-group appear
            name="${NAME}"
            v-on:enter="enter"
            v-on:after-enter="afterEnter">
            <slot></slot>
        </transition-group>`,

    props: {
        stagger: {
            type: String,
            default: () => 50,
        },
    },

    methods: {
        enter(el, done) {
            el.classList.remove(`${NAME}-enter-to`);
            el.classList.add(`${NAME}-enter`);
            const delay = this.getDelay(el);
            setTimeout(() => {
                this.$nextTick(() => {
                    el.classList.add(`${NAME}-enter`);
                    el.classList.remove(`${NAME}-enter-to`);
                    el.classList.remove(`${NAME}-enter-active`);
                });

                done();
            }, delay);
        },

        afterEnter(el) {
            this.$nextTick(() => {
                el.classList.remove(`${NAME}-enter`);
                el.classList.remove(`${NAME}-enter-to`);
            });
        },

        getDelay(el) {
            return el.dataset && el.dataset.index * this.stagger + 5;
        }
    }
}
