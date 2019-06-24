<template lang="pug">
    #millionaire-admin_screen
        .uk-clearfix
        loadingAlert(v-if="!initialized", message="Loading Settings").uk-text-center
        template(v-else)
            topbar
            failureAlert(v-if="!game.mdl_user_teacher", :message="strings.admin_not_allowed", icon="exclamation-circle").uk-text-center
            template(v-else)
                levels.uk-margin-small-top
</template>

<script>
    import mixins from '../../mixins';
    import {mapState} from 'vuex';
    import levels from './levels';
    import loadingAlert from '../helper/loading-alert';
    import topbar from '../topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";
    import FailureAlert from "../helper/failure-alert";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'initialized',
                'strings',
                'game',
            ]),
        },
        components: {
            FailureAlert,
            levels,
            loadingAlert,
            topbar,
            VkGrid
        },
    }
</script>

<style scoped>
</style>
