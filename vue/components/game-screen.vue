<template>
    <div class="game-screen">
        <h3>{{strings.game_screen_title}}</h3>
        <p>
            todo: create levels + joker side nav, create question are, create top bar with score etc
        </p>
    </div>
</template>

<script>
    import { mapState } from 'vuex';

    export default {
        name: "game-screen",
        data () {
            return {

            };
        },
        computed: mapState(['strings', 'levels']),
        methods: {

        },
        created () {
            this.$store.dispatch('fetchLevels');
            this.checkRoute(this.$route);
        },
        beforeRouteUpdate(to, from, next) {
            this.checkRoute(to);
            next();
        },
    }
</script>

<style scoped>
</style>
