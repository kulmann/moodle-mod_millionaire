<template lang="pug">
    #millionaire-question_actions
        .uk-heading-divider.uk-margin-small-bottom
        .uk-grid.uk-grid-small(uk-grid)
            .uk-width-expand
                button.uk-button.uk-button-default.uk-button-small(v-if="quitGameVisible", @click="quitGame", :disabled="quitGameDisabled")
                    v-icon(name="hand-holding-usd").uk-margin-small-right
                    span {{ strings.game_btn_quit }}
            .uk-width-auto
                button.uk-button.uk-button-default.uk-button-small(v-if="nextLevelVisible", @click="startLevel", :disabled="nextLevelDisabled")
                    v-icon(name="arrow-circle-right").uk-margin-small-right
                    span {{ strings.game_btn_continue }}
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import {MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN} from "../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'question',
                'gameMode'
            ]),
            quitGameVisible() {
                return true;
            },
            quitGameDisabled() {
                return false;
            },
            nextLevelVisible() {
                let allowedModes = [MODE_QUESTION_SHOWN, MODE_QUESTION_ANSWERED];
                return _.includes(allowedModes, this.gameMode);
            },
            nextLevelDisabled() {
                return this.gameMode === MODE_QUESTION_ANSWERED;
            },
        },
        methods: {
            ...mapActions([
                'startNextLevel',
            ]),
            quitGame() {

            },
            startLevel() {
                this.startNextLevel();
            }
        },
    }
</script>

<style scoped>
</style>
