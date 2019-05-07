<template lang="pug">
    #millionaire-game_screen
        h2 {{ strings.game_screen_title }}
        vk-grid
            div.uk-flex-left.uk-width-expand
                p questions go here
            div.uk-flex-right
                p jokers go here
                levels
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import levels from './levels';
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        name: "game-screen",
        computed: mapState(['strings']),
        components: {
            VkGrid,
            levels
        },
        methods: {
            ...mapActions([
                'fetchGameSession',
                'fetchLevels'
            ])
        },
        created () {
            this.fetchGameSession();
            this.fetchLevels();
        }
    }
</script>

<style scoped>
</style>
