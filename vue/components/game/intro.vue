<template lang="pug">
    #millionaire-intro
        template(v-if="game")
            h1.uk-text-center {{ game.name }}
        p.uk-text-center
            button.btn.btn-primary(@click="startGame")
                span {{ strings.game_btn_start }}
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import mixins from '../../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'game',
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
