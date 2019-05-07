<template lang="pug">
    #millionaire-levels
        v-list
            v-subheader Levels
            v-list-tile(v-for="level in sortedLevels" :key="level.position")
                v-list-tile-content {{ level.position + 1 }}
                v-list-tile-content &bull;
                v-list-tile-content(v-if="level.name") {{ level.name }}
                v-list-tile-content(v-else) {{ level.score | formatCurrency(level.currency) }}
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import _ from 'lodash';

    export default {
        name: "levels",
        computed: {
            ...mapState(['strings', 'levels']),
            sortedLevels () {
                return _.reverse(this.levels);
            }
        },
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
