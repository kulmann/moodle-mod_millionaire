<template lang="pug">
    #millionaire-game_screen
        h2 {{ strings.game_screen_title }}
        topbar
        vk-grid.uk-margin-small-top
            div.uk-flex-left.uk-width-expand
                intro(v-if="introVisible")
                question(v-if="questionVisible")
                stats(v-if="statsVisible")
            div.uk-flex-right
                levels
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import intro from './intro';
    import levels from './levels';
    import question from './question';
    import stats from './stats';
    import topbar from './topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";
    import {MODE_INTRO, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, MODE_STATS} from "../constants";

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameMode',
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
            }
        },
        methods: {
            ...mapActions([
                'fetchGameSession'
            ]),
        },
        created () {
            this.fetchGameSession();
        },
        components: {
            intro,
            levels,
            question,
            stats,
            topbar,
            VkGrid
        }
    }
</script>

<style scoped>
</style>
