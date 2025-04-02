<template>
    <div class="field-geo-coordinates">
        <label>{{ msgLongitude }} (min: -180, max: 180)</label>
        <input
            :placeholder="msgLongitude"
            type="number"
            v-model="longitude"
            @keyup="validate()"
            @change="change()"
        >
        <label>{{ msgLatitude }} (min: -90, max: 90)</label>
        <input
            :placeholder="msgLatitude"
            type="number"
            v-model="latitude"
            @keyup="validate()"
            @change="change()"
        >
        <input
            :id="id"
            :name="name"
            :data-ref="reference"
            type="hidden"
            v-model="coordinates"
        >
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'FieldGeoCoordinates',
    props: {
        id: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        reference: {
            type: String,
            default: ''
        },
        value: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            coordinates: this.value,
            latitude: '',
            longitude: '',
            msgLatitude: t`Latitude`,
            msgLongitude: t`Longitude`,
        };
    },
    mounted() {
        this.$nextTick(() => {
            const v = this.value || '';
            const [latitude, longitude] = v
                .replace('POINT(', '')
                .replace(')', '')
                .split(' ');
            this.latitude = latitude;
            this.longitude = longitude;
        });
    },
    methods: {
        change() {
            this.validate();
            this.$emit('change', this.coordinates);
        },
        validate() {
            if (this.latitude) {
                this.latitude = this.latitude.replace(/[^\d.-]+/g, '');
                // latitude: a number between -90 and 90
                if (this.latitude < -90) {
                    this.latitude = -90;
                }
                if (this.latitude > 90) {
                    this.latitude = 90;
                }
            }
            if (this.longitude) {
                this.longitude = this.longitude.replace(/[^\d.-]+/g, '');
                // longitude: a number between -180 and 180
                if (this.longitude < -180) {
                    this.longitude = -180;
                }
                if (this.longitude > 180) {
                    this.longitude = 180;
                }
            }
            if (this.latitude && this.longitude) {
                this.coordinates = `POINT(${this.latitude} ${this.longitude})`;
            } else {
                this.coordinates = '';
            }
        },
    },
}
</script>
<style scoped>
.field-geo-coordinates {
    display: flex;
    flex-direction: column;
}
.field-geo-coordinates label {
    margin-bottom: 0.5rem;
}
.field-geo-coordinates input {
    width: 200px;
    margin-bottom: 1rem;
}
</style>
