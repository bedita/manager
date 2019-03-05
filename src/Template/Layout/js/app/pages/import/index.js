/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Import/index.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */
import ImportJobs from 'app/components/import-jobs';

export default {
    components: {
        ImportJobs,
    },

    data() {
        return {
            fileName: '',
        };
    },

    computed: {
    },

    methods: {
        onFileChanged(e) {
            this.fileName = e.target.files[0].name;
        }
    }
}
