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
            .uk-width-auto(v-if="jokersVisible")
                button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker('feedback')", :disabled="isJokerUsedUp('feedback')").uk-margin-small-left
                    v-icon(name="comment-dots")
                button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker('crowd')", :disabled="isJokerUsedUp('crowd')").uk-margin-small-left.uk-margin-small-right
                    v-icon(name="users")
                button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker('chance')", :disabled="isJokerUsedUp('chance')").uk-margin-small-right
                    v-icon(name="percent")
</template>

<script>
    import {mapActions, mapState} from 'vuex';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'question'
            ]),
            jokersVisible() {
                return true;
            }
        },
        methods: {
            ...mapActions([]),
            restart() {
                console.log("TODO: restart game");
            },
            stats() {
                console.log("TODO: show stats");
            },
            game() {
                console.log("TODO: show game");
            },
            isJokerUsedUp(type) {
                return type === 'crowd';
            },
            submitJoker(type) {
                if (this.isJokerUsedUp(type)) {
                    return;
                }
                console.log("joker type " + type + " chosen");
            }
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
