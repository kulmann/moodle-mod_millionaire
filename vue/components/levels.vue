<template lang="pug">
    #millionaire-levels
        ul
            li(v-for="level in levels")
                span {{ level.position + 1 }}
                span &bull;
                span {{ level.currency }}
                span {{ level.name }}
</template>

<script>
    import {mapState, mapActions} from 'vuex';

    export default {
        name: "levels",
        computed: mapState(['strings', 'levels']),
        methods: {
            ...mapActions([
                'fetchLevels'
            ])
        },
        created() {
            this.fetchLevels();
        },
    }
</script>

<style scoped>
</style>
