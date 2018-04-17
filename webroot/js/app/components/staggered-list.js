/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <staggered-list> component used for lists with staggered animation
 *
 */

const NAME = 'staggered';

Vue.component('staggered-list', {
    template: `
        <transition-group appear
            name="${NAME}"
            v-on:enter=enter>
            <slot></slot>
        </transition-group>`,

        props: {
            stagger: {
                type: String,
                default: () => 50,
            },
        },

        methods: {
            // beforeEnter(el) {
            //     el.classList.add(`${NAME}-enter-active`);
            // },

            enter(el, done) {
                el.classList.remove(`${NAME}-enter`);
                const delay = this.getDelay(el);
                setTimeout(() => {
                    done();
                    // el.classList.add(`${NAME}-enter`);
                }, delay);
            },

            // afterEnter(el) {
            //     el.classList.remove(`${NAME}-enter-to`);
            //     const delay = this.getDelay(el);
            //     setTimeout(() => {
            //         // el.classList.add(`${NAME}-enter`);
            //         el.classList.add(`${NAME}-enter-to`);
            //     }, delay);
            // },

            getDelay(el) {
                return el.dataset && el.dataset.index * this.stagger || 1;
            }
        }
});
