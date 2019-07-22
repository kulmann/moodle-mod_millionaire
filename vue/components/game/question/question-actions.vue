<template lang="pug">
    #millionaire-question_actions
        .uk-heading-divider.uk-margin-small-bottom
        .uk-grid.uk-grid-small(uk-grid)
            .uk-width-expand
                button.btn.btn-default(@click="quitGame", :disabled="isQuitGameDisabled")
                    v-icon(name="hand-holding-usd").uk-margin-small-right
                    span {{ strings.game_btn_quit }}
            .uk-width-auto
                button.btn.btn-default(@click="showNextLevel", :disabled="isNextLevelDisabled")
                    v-icon(name="arrow-circle-right").uk-margin-small-right
                    span {{ strings.game_btn_continue }}
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import {MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN} from "../../../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'question',
                'gameMode',
                'levels'
            ]),
            isQuitGameDisabled() {
                if (this.gameMode === MODE_QUESTION_SHOWN && this.question.index > 0) {
                    return false;
                }
                if (this.gameSession.continue_on_failure) {
                    // always allowed to submit your final score if continue_on_failure===true.
                    return false;
                }
                return !(this.gameMode === MODE_QUESTION_ANSWERED && this.question.correct);
            },
            isNextLevelDisabled() {
                return this.gameMode !== MODE_QUESTION_ANSWERED;
            },
        },
        methods: {
            ...mapActions([
                'closeGameSession',
                'showQuestionForLevel',
            ]),
            quitGame() {
                this.closeGameSession();
            },
            showNextLevel() {
                let nextIndex = this.question.index + 1;
                if(this.levels.length > nextIndex) {
                    this.showQuestionForLevel(nextIndex);
                }
            }
        },
    }
</script>

<style scoped>
</style>
