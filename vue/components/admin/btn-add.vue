<template lang="pug">
    div(:class="alignmentClass")
        button.btn.btn-default(type="button", @click="emitClick")
            v-icon(name="plus").uk-margin-small-right
            span {{ strings.admin_btn_add }}
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../../mixins';

    export default {
        mixins: [mixins],
        props: {
            align: String
        },
        computed: {
            ...mapState([
                'strings',
            ]),
            alignmentClass() {
                if (this.align){
                    switch(this.align) {
                        case 'left': return 'uk-text-left';
                        case 'center': return 'uk-text-center';
                        default: return 'uk-text-right';
                    }
                }
                return 'uk-text-right';
            }
        },
        methods: {
            emitClick() {
                this.$emit('click');
            }
        }
    }
</script>
