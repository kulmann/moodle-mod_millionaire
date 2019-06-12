<template lang="pug">
    .uk-alert.uk-alert-danger(uk-alert)
        p
            v-icon(v-if="icon", :name="icon").uk-margin-small-right
            span {{ message }}
</template>

<script>
    export default {
        props: {
            icon: String,
            message: String
        }
    }
</script>
