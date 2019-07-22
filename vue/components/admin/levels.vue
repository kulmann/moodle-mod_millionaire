<template lang="pug">
    .uk-card.uk-card-default
        .uk-card-header
            h3 {{ strings.admin_levels_title }}
        .uk-card-body
            template(v-if="levels.length === 0")
                infoAlert(:message="strings.admin_levels_none")
                btnAdd(@click="createLevel")
            template(v-else)
                p {{ strings.admin_levels_intro }}
                btnAdd(@click="createLevel")
                table.uk-table.uk-table-small.uk-table-striped
                    tbody
                        template(v-for="level in sortedLevels")
                            tr.uk-text-nowrap(:key="level.position")
                                td.uk-table-shrink.uk-text-center.uk-text-middle
                                    b {{ level.position + 1 }}
                                td.uk-table-shrink.uk-preserve-width.uk-text-center.uk-text-middle
                                    v-icon(v-if="isSafeSpot(level)", name="regular/star")
                                td.uk-table-auto.uk-text-right.uk-text-middle {{ level.title }}
                                td.actions.uk-table-shrink.uk-preserve-width
                                    button.btn.btn-default(@click="editLevel(level)")
                                        v-icon(name="regular/edit")
                                    button.btn.btn-default(@click="moveLevel(level, -1)", :disabled="level.position === 0")
                                        v-icon(name="arrow-down")
                                    button.btn.btn-default(@click="moveLevel(level, 1)", :disabled="level.position === (levels.length - 1)")
                                        v-icon(name="arrow-up")
                                    button.btn.btn-default(@click="deleteLevelAsk(level)")
                                        v-icon(name="trash")
                            tr(v-if="deleteConfirmationLevelId === level.id")
                                td(colspan="4")
                                    .uk-alert.uk-alert-danger(uk-alert)
                                        vk-grid
                                            .uk-width-expand
                                                v-icon(name="exclamation-circle").uk-margin-small-right
                                                span {{ strings.admin_level_delete_confirm | stringParams(level.title) }}
                                            .uk-width-auto
                                                button.btn.btn-danger.uk-margin-small-left(@click="deleteLevelConfirm(level)")
                                                    span {{ strings.admin_btn_confirm_delete }}
                btnAdd(@click="createLevel")
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import _ from 'lodash';
    import mixins from '../../mixins';
    import infoAlert from '../helper/info-alert';
    import btnAdd from './btn-add';
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        mixins: [mixins],
        data() {
            return {
                deleteConfirmationLevelId: null,
            }
        },
        computed: {
            ...mapState([
                'contextID',
                'strings',
                'levels',
            ]),
            sortedLevels() {
                return _.reverse(_.sortBy(this.levels, ['position']));
            }
        },
        methods: {
            ...mapActions([
                'changeLevelPosition',
                'deleteLevel'
            ]),
            isSafeSpot(level) {
                return level.safe_spot;
            },
            createLevel() {
                this.$router.push({name: 'admin-level-edit'});
            },
            editLevel(level) {
                this.$router.push({name: 'admin-level-edit', params: {levelId: level.id}});
            },
            moveLevel(level, delta) {
                this.changeLevelPosition({
                    levelid: level.id,
                    delta: delta,
                });
            },
            deleteLevelAsk(level) {
                this.deleteConfirmationLevelId = level.id;
            },
            deleteLevelConfirm(level) {
                this.deleteConfirmationLevelId = null;
                this.deleteLevel({
                    levelid: level.id
                });
            }
        },
        components: {
            VkGrid,
            infoAlert,
            btnAdd,
        }
    }
</script>

<style lang="scss" scoped>
    .actions {
        & > button {
            margin-left: 0;
        }

        & > button:last-child {
            margin-right: 0;
        }
    }
</style>
