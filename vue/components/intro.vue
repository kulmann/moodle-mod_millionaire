<template lang="pug">
    #millionaire-intro
        p Blabla intro text. Start game by clicking on the first level or hit the button "new game" at the top bar.
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
