<template lang="pug">
    .uk-card.uk-card-default
        .uk-card-header
            h3 {{ strings.admin_levels_title }}
        .uk-card-body
            template(v-if="levels.length === 0")
                infoAlert(:message="strings.admin_levels_none")
                levelBtnAdd(@createLevel="createLevel")
            template(v-else)
                p {{ strings.admin_levels_intro }}
                levelBtnAdd(@createLevel="createLevel")
                table.uk-table.uk-table-small.uk-table-striped
                    tbody
                        template(v-for="level in sortedLevels")
                            tr.uk-text-nowrap(:key="level.position")
                                td.uk-table-shrink.uk-text-center.uk-text-middle
                                    b {{ level.position + 1 }}
                                td.uk-table-shrink.uk-preserve-width.uk-text-center.uk-text-middle
                                    v-icon(v-if="isSafeSpot(level)", name="regular/star")
                                td.uk-table-auto.uk-text-right.uk-text-middle {{ level.title }}
                                td.uk-table-shrink.uk-text-middle TODO: #questions
                                td.actions.uk-table-shrink.uk-preserve-width
                                    button.uk-button.uk-button-small.uk-button-default(@click="editLevel(level)")
                                        v-icon(name="regular/edit")
                                    button.uk-button.uk-button-small.uk-button-default(@click="moveLevel(level, -1)", :disabled="level.position === 0")
                                        v-icon(name="arrow-down")
                                    button.uk-button.uk-button-small.uk-button-default(@click="moveLevel(level, 1)", :disabled="level.position === (levels.length - 1)")
                                        v-icon(name="arrow-up")
                                    button.uk-button.uk-button-small.uk-button-default(@click="deleteLevelAsk(level)")
                                        v-icon(name="trash")
                            tr(v-if="deleteConfirmationLevelId === level.id")
                                td(colspan="5")
                                    .uk-alert.uk-alert-danger(uk-alert)
                                        vk-grid
                                            .uk-width-expand
                                                v-icon(name="exclamation-circle").uk-margin-small-right
                                                span {{ strings.admin_level_delete_confirm | stringParams(level.title) }}
                                            .uk-width-auto
                                                button.uk-button.uk-button-danger.uk-button-small.uk-margin-small-left(@click="deleteLevelConfirm(level)")
                                                    span {{ strings.admin_btn_confirm_delete }}
                levelBtnAdd(@createLevel="createLevel")
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import _ from 'lodash';
    import mixins from '../../mixins';
    import {MFormModal} from '../../mform';
    import infoAlert from '../helper/info-alert';
    import levelBtnAdd from './level-btn-add';
    import failureAlert from "../helper/failure-alert";
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        mixins: [mixins],
        data() {
            return {
                activeLevelId: null,
                modal: null,
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
                'fetchLevels',
                'changeLevelPosition',
                'deleteLevel'
            ]),
            isSafeSpot(level) {
                return level.safe_spot;
            },
            async createLevel() {
                this.editLevel(null);
            },
            async editLevel(level) {
                let title = '';
                let args = {};
                if (level) {
                    title = this.strings.admin_level_title_edit;
                    args.levelid = level.id;
                } else {
                    title = this.strings.admin_level_title_add;
                }
                this.modal = new MFormModal('level_edit', title, this.contextID, args);
                try {
                    await this.modal.show();
                    const success = await this.modal.finished;
                    if (success) {
                        this.fetchLevels();
                    }
                } catch (e) {
                    // This happens when the modal is destroyed on a page change. Ignore.
                } finally {
                    this.modal = null;
                }
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
            failureAlert,
            infoAlert,
            levelBtnAdd,
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
