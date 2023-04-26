<script>

export default {

    props: {
        attrs: {
            type: Object,
            required: true,
        },
        el: {
            type: HTMLInputElement,
        },
    },

    async mounted() {
        const child = document.createElement('code');
        child.classList.add('mt-05')
        child.textContent = 'i';
        child.setAttribute('title', JSON.stringify(this.attrs));
        this.el.parentElement.appendChild(child);
    },

}
</script>
<style>
code {
    display: block;
    border: dotted yellow 1px;
    background-color: black;
    color: yellow;
    text-align: center;
    height: 16px;
    width: 18px;
    line-height: normal;
    font-size: small;
}
</style>
