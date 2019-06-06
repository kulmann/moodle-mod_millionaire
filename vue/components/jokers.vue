<template lang="pug">
    #millionaire-game_jokers
        button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker('feedback')", :disabled="isJokerUsedUp('feedback')").uk-margin-small-left
            v-icon(name="comment-dots")
        button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker('crowd')", :disabled="isJokerUsedUp('crowd')").uk-margin-small-left.uk-margin-small-right
            v-icon(name="users")
        button.uk-button.uk-button-primary.uk-button-small(@click="submitJoker('chance')", :disabled="isJokerUsedUp('chance')").uk-margin-small-right
            v-icon(name="percent")
</template>

<script>
    import {mapState} from 'vuex';

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
            isJokerUsedUp(type) {
                return type === 'crowd';
            },
            submitJoker(type) {
                if (this.isJokerUsedUp(type)) {
                    return;
                }
                console.log("TODO: implement joker type " + type);
            }
        },
    }
</script>
