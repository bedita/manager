<template>
    <div class="media-item">
        <template v-if="objectType === 'audio'">
            <audio
                id="media-item-container"
                width="100%"
                controls
            >
                <source :src="url">
                {{ msg.audioTagNotSupported }}
            </audio>
        </template>
        <template v-if="objectType === 'videos'">
            <video
                id="media-item-container"
                :type="mime"
                width="100%"
                controls
            >
                <source :src="url">
                {{ msg.videoTagNotSupported }}
            </video>
        </template>
        <template v-if="objectType === 'images'">
            <template v-if="thumb == thumbCodes.notAcceptable">
                <p>{{ msg.notAcceptable }}</p>
            </template>
            <template v-if="thumb == thumbCodes.notAvailable">
                <p>{{ msg.notAvailable }}</p>
                <p>
                    <a
                        :href="url"
                        :title="msg.viewOriginal"
                        target="_blank"
                    >
                        {{ msg.viewOriginal }}
                    </a>
                </p>
            </template>
            <template v-if="thumb == thumbCodes.notReady">
                <p>{{ msg.notReady }}</p>
            </template>
            <template v-if="thumb == thumbCodes.noUrl">
                <p>
                    <a
                        :href="url"
                        :title="msg.viewOriginal"
                        target="_blank"
                    >
                        {{ msg.viewOriginal }}
                    </a>
                </p>
            </template>
            <template v-else>
                <figure class="thumb">
                    <a
                        :href="url"
                        :title="msg.openImage"
                        target="_blank"
                    >
                        <img
                            id="imageThumb"
                            alt=""
                            style="min-width:500px;"
                            :src="previewImage()"
                        >
                    </a>
                </figure>
            </template>
        </template>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'MediaItem',

    props: {
        captions: {
            type: Array,
            required: true,
        },
        languages: {
            type: Object,
            default: () => ({}),
        },
        objectType: {
            type: String,
            required: true,
        },
        mime: {
            type: String,
            required: true,
        },
        thumb: {
            type: String,
            required: true,
        },
        thumbCodes: {
            type: Object,
            required: true,
        },
        url: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            msg: {
                audioTagNotSupported: t`Sorry, your browser does not support embedded <code>audio</code> element`,
                notAcceptable: t`Cannot produce a thumbnail for this file`,
                notAvailable: t`The thumbnail is not available`,
                notReady: t`The thumbnail is not ready`,
                openImage: t`Open image`,
                videoTagNotSupported: t`Sorry, your browser does not support embedded <code>video</code> element`,
                viewOriginal: t`View original`,
            },
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.addTracks();
        });
    },

    methods: {
        addTracks() {
            const container = document.getElementById('media-item-container');
            if (!container) {
                return;
            }
            this.captions.forEach(caption => {
                const track = document.createElement('track');
                track.kind = 'subtitles';
                track.label = this.languages[caption.lang];
                track.srclang = caption.lang;
                track.default = false;
                const blob = new Blob([caption.caption_text], { type: 'text/vtt' });
                track.src = URL.createObjectURL(blob);
                container.appendChild(track);
            });
            this.$forceUpdate();
        },
        previewImage() {
            return this.$helpers.updatePreviewImage(this.file, 'title', this.thumb);
        },
    }
}
</script>
