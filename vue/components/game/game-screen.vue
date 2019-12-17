<template lang="pug">
    #millionaire-game_screen
        .uk-clearfix
        loadingAlert(v-if="!initialized", message="Loading Game").uk-text-center
        template(v-else)
            topbar
            vk-grid.uk-margin-small-top
                div.uk-flex-left.uk-width-expand
                    intro(v-if="introVisible")
                    question(v-if="questionVisible")
                    stats(v-if="statsVisible")
                    help(v-if="helpVisible")
                div.uk-flex-right
                    jokers.uk-margin-small-bottom
                    levels
</template>

<script>
    import mixins from '../../mixins';
    import {mapState} from 'vuex';
    import help from './help';
    import intro from './intro';
    import jokers from './joker/jokers';
    import levels from './levels';
    import loadingAlert from '../helper/loading-alert';
    import question from './question/question';
    import stats from './stats';
    import topbar from '../topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";
    import {MODE_HELP, MODE_INTRO, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, MODE_STATS} from "../../constants";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'initialized',
                'strings',
                'gameMode',
                'gameSession',
                'levels',
                'question',
            ]),
            introVisible() {
                return this.gameMode === MODE_INTRO;
            },
            questionVisible() {
                let allowedModes = [MODE_QUESTION_SHOWN, MODE_QUESTION_ANSWERED];
                return _.includes(allowedModes, this.gameMode);
            },
            statsVisible() {
                return this.gameMode === MODE_STATS;
            },
            helpVisible() {
                return this.gameMode === MODE_HELP;
            }
        },
        components: {
            help,
            intro,
            jokers,
            levels,
            loadingAlert,
            question,
            stats,
            topbar,
            VkGrid
        }
    }
</script>

<style scoped>
</style>
