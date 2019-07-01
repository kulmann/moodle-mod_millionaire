<template lang="pug">
    #millionaire-admin_screen
        .uk-clearfix
        loadingAlert(v-if="!initialized", message="Loading Settings").uk-text-center
        template(v-else)
            topbar
            failureAlert(v-if="!game.mdl_user_teacher", :message="strings.admin_not_allowed", icon="exclamation-circle").uk-text-center
            template(v-else)
                levelEdit(v-if="showLevelEdit", :level="levelForEditing").uk-margin-small-top
                levels(v-else-if="showLevelList").uk-margin-small-top
</template>

<script>
    import mixins from '../../mixins';
    import {mapState} from 'vuex';
    import levels from './levels';
    import loadingAlert from '../helper/loading-alert';
    import topbar from '../topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";
    import FailureAlert from "../helper/failure-alert";
    import levelEdit from "./level-edit";
    import _ from 'lodash';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'initialized',
                'strings',
                'game',
                'levels',
            ]),
            routePath () {
                return this.$route.path;
            },
            showLevelEdit() {
                return this.$route.name === 'admin-level-edit';
            },
            levelForEditing() {
                // try to find the given levelId in our loaded levels.
                if (this.$route.params.hasOwnProperty('levelId') && !_.isUndefined(this.$route.params.levelId)) {
                    let levelId = parseInt(this.$route.params.levelId);
                    return _.find(this.levels, function (level) {
                        return level.id === levelId;
                    });
                }
                // None found. Returning null will (correctly) result in creating a new level.
                return null;
            },
            showLevelList() {
                return this.$route.name === 'admin-level-list'
            },
        },
        components: {
            levelEdit,
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
