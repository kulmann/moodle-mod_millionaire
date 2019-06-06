<template lang="pug">
    #millionaire-stats
        p Show stats here.
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings'
            ]),
        },
    }
</script>
