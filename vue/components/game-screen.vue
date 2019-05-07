<template lang="pug">
    #millionaire-game_screen
        v-container(fluid, grid-list-sm)
            v-layout(row, wrap)
                v-flex(d-flex, xs12)
                    h3 {{strings.game_screen_title}}
            v-layout(row, wrap)
                v-flex(d-flex, xs8)
                    p questions go here
                v-flex(d-flex, xs4)
                    v-layout(row, wrap)
                        v-flex(d-flex, xs12)
                            p jokers
                        v-flex(d-flex, xs12)
                            levels
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import levels from './levels';

    export default {
        name: "game-screen",
        computed: mapState(['strings']),
        components: {
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
