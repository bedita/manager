<template>
    <div class="placeholderParams">
        <span>Bearing</span>
        <span>Pitch</span>
        <span>Zoom</span>
        <div><input type="number" @change="changeParams" v-model="bearing" placeholder="bearing" /></div><!-- [min: -180, max: +180] -->
        <div><input type="number" @change="changeParams" v-model="pitch" placeholder="pitch" /></div><!-- [0-60] -->
        <div><input type="number" @change="changeParams" v-model="zoom" placeholder="zoom" /></div><!-- [2-20] -->
    </div>
</template>
<script>
import { EventBus } from 'app/components/event-bus';

export default {
    name: 'PlaceholderParams',
    props: {
        field: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        text: {
            type: String,
            required: true,
        },
        value: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            bearing: null,
            pitch: null,
            zoom: null,
            newValue: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            const decoded = this.decoded(this.value);
            if (decoded === 'undefined') {
                this.newValue = btoa('undefined');

                return;
            }
            try {
                const params = JSON.parse(decoded);
                this.bearing = params?.bearing || null;
                this.pitch = params?.pitch || null;
                this.zoom = params?.zoom || null;
            } catch(e) {
                console.error(e, decoded, this.value);
            }
        });
    },
    methods: {
        changeParams() {
            this.oldValue = this.newValue ?? this.value;
            this.newValue = btoa(JSON.stringify({
                bearing: this.bearing || null,
                pitch: this.pitch || null,
                zoom: this.zoom || null,
            }));
            EventBus.send('replace-placeholder', {
                id: this.id,
                field: this.field,
                oldValue: this.oldValue,
                newValue: this.newValue,
            });
            this.oldValue = this.newValue;
        },
        decoded(item) {
            return atob(item);
        },
    },
};
</script>
<style>
div.placeholderParams {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    text-align: center;
    align-items: center;
    gap: 8px;
    margin: 4px 0;
}
</style>
