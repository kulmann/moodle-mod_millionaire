<template lang="pug">
    #millionaire-game_topbar
        .uk-grid.uk-grid-collapse.top-bar(uk-grid).uk-flex-middle
            .uk-width-expand
                button.uk-button.uk-button-default.uk-button-small.uk-margin-small-left(@click="restart")
                    v-icon(name="redo").uk-margin-small-right
                    span {{ strings.game_btn_restart }}
                button.uk-button.uk-button-default.uk-button-small.uk-margin-small-left(@click="stats", v-if="statsButtonVisible")
                    v-icon(name="chart-line").uk-margin-small-right
                    span {{ strings.game_btn_stats }}
                button.uk-button.uk-button-default.uk-button-small.uk-margin-small-left(@click="game", v-if="gameButtonVisible")
                    v-icon(name="gamepad").uk-margin-small-right
                    span {{ strings.game_btn_game }}
            .uk-width-auto
                button.uk-button.uk-button-default.uk-button-small.uk-margin-small-right(@click="help")
                    v-icon(name="regular/question-circle")
</template>

<script>
    import {mapActions, mapMutations, mapState} from 'vuex';
    import {MODE_HELP, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, MODE_STATS} from "../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameMode',
                'gameSession',
                'question'
            ]),
            statsButtonVisible() {
                return this.gameMode !== MODE_STATS;
            },
            gameButtonVisible() {
                let modes = [MODE_STATS, MODE_HELP];
                return _.includes(modes, this.gameMode);
            }
        },
        methods: {
            ...mapActions([
                'createGameSession'
            ]),
            ...mapMutations([
                'setGameMode'
            ]),
            restart() {
                this.createGameSession();
            },
            stats() {
                this.setGameMode(MODE_STATS);
            },
            game() {
                if (this.question.finished) {
                    this.setGameMode(MODE_QUESTION_ANSWERED);
                } else {
                    this.setGameMode(MODE_QUESTION_SHOWN)
                }
            },
            help() {
                this.setGameMode(MODE_HELP);
            },
        },
    }
</script>

<style scoped>
    .top-bar {
        background-color: #f8f8f8;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        border: 1px solid #ccc;
        height: 50px;
    }
</style>
