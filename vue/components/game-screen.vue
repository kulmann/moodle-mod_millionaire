<template lang="pug">
    #millionaire-game_screen
        h2 {{ strings.game_screen_title }}
        topbar
        vk-grid.uk-margin-small-top
            div.uk-flex-left.uk-width-expand
                question
            div.uk-flex-right
                levels
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import levels from './levels';
    import question from './question';
    import gameTopbar from './game-topbar';
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        computed: mapState(['strings']),
        methods: {
            ...mapActions([
                'fetchGameSession'
            ]),
        },
        created () {
            this.fetchGameSession();
        },
        components: {
            VkGrid,
            levels,
            question,
            'topbar': gameTopbar
        }
    }
</script>

<style scoped>
</style>
