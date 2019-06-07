<template lang="pug">
    #millionaire-game_jokers.uk-text-center
        button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker(feedback)", :disabled="isJokerDisabled(feedback)").uk-margin-small-left
            v-icon(name="comment-dots")
        button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker(crowd)", :disabled="isJokerDisabled(crowd)").uk-margin-small-left.uk-margin-small-right
            v-icon(name="users")
        button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker(chance)", :disabled="isJokerDisabled(chance)").uk-margin-small-right
            v-icon(name="percent")
</template>

<script>
    import {mapState} from 'vuex';
    import {
        MODE_GAME_FINISHED,
        MODE_QUESTION_ANSWERED,
        MODE_QUESTION_SHOWN,
        JOKER_FEEDBACK,
        JOKER_CROWD,
        JOKER_CHANCE,
        VALID_JOKERS
    } from "../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'question',
                'gameMode',
            ]),
            feedback() {
                return JOKER_FEEDBACK;
            },
            crowd() {
                return JOKER_CROWD;
            },
            chance() {
                return JOKER_CHANCE;
            }
        },
        methods: {
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
                return type === 'crowd';
            },
            submitJoker(type) {
                if (this.isJokerDisabled(type)) {
                    return;
                }
                console.log("TODO: implement joker type " + type);
            }
        },
    }
</script>
