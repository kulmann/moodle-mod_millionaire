<template lang="pug">
    #millionaire-game_screen
        .uk-clearfix
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
    import mixins from '../mixins';
    import {mapActions, mapMutations, mapState} from 'vuex';
    import help from './help';
    import intro from './intro';
    import jokers from './jokers';
    import levels from './levels';
    import question from './question';
    import stats from './stats';
    import topbar from './topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";
    import {MODE_HELP, MODE_INTRO, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, MODE_STATS} from "../constants";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
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
        methods: {
            ...mapActions([
                'fetchGameSession',
                'showQuestionForLevel',
            ]),
            ...mapMutations([
                'setGameMode'
            ]),
        },
        created() {
            this.fetchGameSession().then(() => {
                let highestSeenLevel = this.findHighestSeenLevel(this.levels);
                if (highestSeenLevel.seen) {
                    // the game was obviously already started. So fetch the last seen question.
                    this.showQuestionForLevel(highestSeenLevel.position).then(() => {
                        if (this.question.finished) {
                            this.setGameMode(MODE_QUESTION_ANSWERED);
                        } else {
                            this.setGameMode(MODE_QUESTION_SHOWN);
                        }
                    });
                }
            });
        },
        components: {
            help,
            intro,
            jokers,
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
