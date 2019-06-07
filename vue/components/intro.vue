<template lang="pug">
    #millionaire-intro
        .uk-alert.uk-alert-warning
            p(v-html="strings.game_intro_message")
        p.uk-text-center
            button.uk-button.uk-button-primary(@click="startGame")
                span {{ strings.game_btn_start }}
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import mixins from '../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings'
            ]),
        },
        methods: {
            ...mapActions([
                'showQuestionForLevel'
            ]),
            startGame() {
                this.showQuestionForLevel(0);
            }
        }
    }
</script>
