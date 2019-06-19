<template lang="pug">
    #millionaire-admin_screen
        .uk-clearfix
        loadingAlert(v-if="!initialized", message="Loading Game").uk-text-center
        template(v-else)
            topbar
            p Yoyo!
</template>

<script>
    import mixins from '../../mixins';
    import {mapState} from 'vuex';
    import loadingAlert from '../helper/loading-alert';
    import topbar from '../topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'initialized',
                'strings',
            ]),
        },
        components: {
            loadingAlert,
            topbar,
            VkGrid
        }
    }
</script>

<style scoped>
</style>
