<template lang="pug">
    #millionaire-game_screen
        h2 {{ strings.game_screen_title }}
        vk-grid
            div.uk-flex-left.uk-width-expand
                question
            div.uk-flex-right
                p jokers go here
                levels(@setCurrentLevel="setCurrentLevel")
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import levels from './levels';
    import question from './question';
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        computed: mapState(['strings']),
        components: {
            VkGrid,
            levels,
            question,
        },
        methods: {
            ...mapActions([
                'fetchGameSession',
                'fetchLevels',
                'fetchQuestion'
            ]),
            setCurrentLevel () {
                this.fetchQuestion();
            }
        },
        created () {
            this.fetchGameSession();
            this.fetchLevels();
        }
    }
</script>

<style scoped>
</style>
