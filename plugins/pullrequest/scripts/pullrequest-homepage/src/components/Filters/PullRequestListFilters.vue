<!--
  - Copyright (c) Enalean, 2024 - present. All Rights Reserved.
  -
  - This file is a part of Tuleap.
  -
  - Tuleap is free software; you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation; either version 2 of the License, or
  - (at your option) any later version.
  -
  - Tuleap is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div class="tlp-framed-vertically">
        <tuleap-selectors-dropdown
            v-bind:button_text="$gettext('Add filter')"
            v-bind:selectors_entries="selectors_entries"
        />
        <button
            class="tlp-button-outline tlp-button-primary pull-request-homepage-remove-all-filters"
            v-on:click="filters_store.clearAllFilters()"
            v-bind:disabled="filters_store.getFilters().value.length === 0"
            data-test="clear-all-list-filters"
        >
            {{ $gettext("Clear filters") }}
        </button>
        <div class="pull-requests-homepage-filters">
            <span
                class="tlp-badge-primary tlp-badge-outline pull-request-homepage-filter-badge"
                v-for="filter in filters_store.getFilters().value"
                v-bind:key="filter.id"
                data-test="list-filter-badge"
            >
                {{ filter.label }}
                <button
                    type="button"
                    class="pull-request-homepage-remove-filter"
                    v-on:click="filters_store.deleteFilter(filter)"
                    v-bind:title="$gettext('Delete this filter')"
                    data-test="list-filter-badge-delete-button"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useGettext } from "vue3-gettext";
import "@tuleap/plugin-pullrequest-selectors-dropdown";
import { strictInject } from "@tuleap/vue-strict-inject";
import { DISPLAY_TULEAP_API_ERROR, REPOSITORY_ID } from "../../injection-symbols";
import { AuthorSelectorEntry } from "./Author/AuthorSelectorEntry";
import type { StoreListFilters } from "./ListFiltersStore";

const { $gettext } = useGettext();

const repository_id = strictInject(REPOSITORY_ID);
const displayTuleapAPIFault = strictInject(DISPLAY_TULEAP_API_ERROR);

const props = defineProps<{
    filters_store: StoreListFilters;
}>();

const selectors_entries = [
    AuthorSelectorEntry($gettext, displayTuleapAPIFault, props.filters_store, repository_id),
];
</script>

<style lang="scss">
.pull-request-autocompleter-avatar {
    display: flex;
    gap: 5px;
    align-items: center;
}

.pull-request-homepage-remove-all-filters {
    margin: 0 0 0 var(--tlp-small-spacing);
}

.pull-requests-homepage-filters {
    display: flex;
    flex-direction: row;
    gap: var(--tlp-small-spacing);
    margin: var(--tlp-medium-spacing) 0 0 0;
}

.pull-request-homepage-filter-badge {
    display: flex;
    align-items: center;
}

.pull-request-homepage-remove-filter {
    margin: 0 0 0 4px;
    padding: 0;
    border: unset;
    background: unset;
    color: unset;
    text-align: unset;
    cursor: pointer;

    &:hover {
        opacity: 0.5;
    }

    &:focus {
        box-shadow: var(--tlp-shadow-focus);
    }
}
</style>