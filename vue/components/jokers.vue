<template lang="pug">
    .joker-container.uk-flex.uk-flex-center.uk-flex-middle
        button.uk-button.uk-button-primary.uk-button-small(@click="selectJoker(feedback)", :disabled="isJokerDisabled(feedback)").uk-margin-small-left
            v-icon(name="regular/lightbulb")
        button.uk-button.uk-button-primary.uk-button-small(@click="selectJoker(audience)", :disabled="isJokerDisabled(audience)").uk-margin-small-left.uk-margin-small-right
            v-icon(name="users")
        button.uk-button.uk-button-primary.uk-button-small(@click="selectJoker(chance)", :disabled="isJokerDisabled(chance)").uk-margin-small-right
            v-icon(name="percent")
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import {
        JOKER_CHANCE,
        JOKER_AUDIENCE,
        JOKER_FEEDBACK,
        MODE_GAME_FINISHED,
        MODE_QUESTION_ANSWERED,
        MODE_QUESTION_SHOWN,
        VALID_JOKERS
    } from "../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'usedJokers',
                'question',
                'gameMode',
            ]),
            feedback() {
                return JOKER_FEEDBACK;
            },
            audience() {
                return JOKER_AUDIENCE;
            },
            chance() {
                return JOKER_CHANCE;
            }
        },
        methods: {
            ...mapActions([
                'submitJoker'
            ]),
            isInGame() {
                let modes = [MODE_QUESTION_SHOWN, MODE_QUESTION_ANSWERED, MODE_GAME_FINISHED];
                return _.includes(modes, this.gameMode);
            },
            isJokerValid(type) {
                return _.includes(VALID_JOKERS, type);
            },
            isJokerDisabled(type) {
                if (!this.isInGame() || !this.isJokerValid(type)) {
                    return true;
                }
                let joker = _.find(this.usedJokers, function (joker) {
                    return joker.joker_type === type;
                });
                return !!joker;
            },
            selectJoker(type) {
                if (this.isJokerDisabled(type)) {
                    return;
                }
                let args = {
                    gamesessionid: this.gameSession.id,
                    questionid: this.question.id,
                    jokertype: type,
                };
                this.submitJoker(args);
            }
        },
    }
</script>

<style scoped>
    .joker-container {
        background-color: #f8f8f8;
        border: 1px solid #ccc;
        height: 50px;
    }
</style>
