<template lang="pug">
    #millionaire-game_topbar
        .uk-grid.uk-grid-collapse.top-bar(uk-grid).uk-flex-middle
            .uk-width-expand
                button.uk-button.uk-button-default.uk-button-small.uk-margin-small-left(@click="restart")
                    v-icon(name="redo").uk-margin-small-right
                    span {{ strings.game_btn_restart }}
                button.uk-button.uk-button-default.uk-button-small.uk-margin-small-left(@click="stats")
                    v-icon(name="chart-line").uk-margin-small-right
                    span {{ strings.game_btn_stats }}
</template>

<script>
    import {mapActions, mapMutations, mapState} from 'vuex';
    import {MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, MODE_STATS} from "../constants";

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'question'
            ]),
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
