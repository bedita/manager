<template>
    <transition-group
        appear
        name="${NAME}"
        @enter="enter"
        @after-enter="afterEnter"
    >
        <slot />
    </transition-group>
</template>
<script>
const NAME = 'staggered';

export default {
    name: 'StaggeredList',
    props: {
        stagger: {
            type: Number,
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
</script>
