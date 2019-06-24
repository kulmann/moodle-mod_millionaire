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
                        tr.uk-text-nowrap(v-for="level in sortedLevels", :key="level.position")
                            td.uk-table-shrink.uk-text-center.uk-text-middle
                                b {{ level.position + 1 }}
                            td.uk-table-shrink.uk-preserve-width.uk-text-center.uk-text-middle
                                v-icon(v-if="isSafeSpot(level)", name="regular/star")
                            td.uk-table-auto.uk-text-right.uk-text-middle
                                span(v-if="level.name") {{ level.name }}
                                span(v-else) {{ level.score | formatCurrency(level.currency) }}
                            td.uk-table-shrink.uk-text-middle TODO: #questions
                            td.actions.uk-table-shrink.uk-preserve-width
                                button.uk-button.uk-button-small.uk-button-default(@click="editLevel(level)")
                                    v-icon(name="regular/edit")
                                button.uk-button.uk-button-small.uk-button-default(@click="moveLevelDown(level)")
                                    v-icon(name="arrow-down")
                                button.uk-button.uk-button-small.uk-button-default(@click="moveLevelUp(level)")
                                    v-icon(name="arrow-up")
                                button.uk-button.uk-button-small.uk-button-default()
                                    v-icon(name="trash")
                levelBtnAdd(@createLevel="createLevel")
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import _ from 'lodash';
    import mixins from '../../mixins';
    import {MFormModal} from '../../mform';
    import infoAlert from '../helper/info-alert';
    import levelBtnAdd from './level-btn-add';

    export default {
        mixins: [mixins],
        data() {
            return {
                activeLevelId: null,
                modal: null,
            }
        },
        computed: {
            ...mapState([
                'contextID',
                'strings',
                'levels',
            ]),
            sortedLevels() {
                return _.reverse(this.levels);
            }
        },
        methods: {
            ...mapActions([
                'fetchLevels'
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
            moveLevelUp(level) {

            },
            moveLevelDown(level) {

            },
        },
        components: {
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
