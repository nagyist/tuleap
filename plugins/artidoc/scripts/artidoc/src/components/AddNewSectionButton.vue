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
  -
  -->

<template>
    <div class="tlp-dropdown artidoc-add-new-section-container">
        <button
            type="button"
            class="tlp-button-primary artidoc-add-new-section-solo-button"
            v-bind:title="add_new_section_label"
            ref="trigger_element"
            data-test="artidoc-add-new-section-trigger"
        >
            <i class="fa-solid fa-plus" role="img"></i>
        </button>
        <div
            ref="dropdown_element"
            role="menu"
            class="tlp-dropdown-menu artidoc-add-new-section-submenu"
        >
            <button
                type="button"
                class="tlp-dropdown-menu-item"
                v-on:click="addNewSection"
                data-test="add-new-section"
            >
                {{ add_new_section_label }}
            </button>
            <button
                type="button"
                class="tlp-dropdown-menu-item"
                v-on:click="addExistingSection"
                data-test="add-existing-section"
            >
                {{ add_existing_section_label }}
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useGettext } from "vue3-gettext";
import { strictInject } from "@tuleap/vue-strict-inject";
import { OPEN_CONFIGURATION_MODAL_BUS } from "@/stores/useOpenConfigurationModalBusStore";
import { OPEN_ADD_EXISTING_SECTION_MODAL_BUS } from "@/composables/useOpenAddExistingSectionModalBus";
import { isTrackerWithSubmittableSection, CONFIGURATION_STORE } from "@/stores/configuration-store";
import type { PositionForSection, SectionsStore } from "@/stores/useSectionsStore";
import type { ArtidocSection, PendingArtifactSection } from "@/helpers/artidoc-section.type";
import PendingArtifactSectionFactory from "@/helpers/pending-artifact-section.factory";
import { computed, ref, onMounted, onUnmounted } from "vue";
import type { Dropdown } from "@tuleap/tlp-dropdown";
import { createDropdown } from "@tuleap/tlp-dropdown";

const props = defineProps<{
    position: PositionForSection;
    insert_section_callback: SectionsStore["insertSection"];
}>();

const configuration_store = strictInject(CONFIGURATION_STORE);

const { $gettext } = useGettext();

const add_new_section_label = $gettext("Add new section");
const add_existing_section_label = $gettext("Add existing section");

const configuration_bus = strictInject(OPEN_CONFIGURATION_MODAL_BUS);
const add_existing_section_bus = strictInject(OPEN_ADD_EXISTING_SECTION_MODAL_BUS);

const trigger_element = ref<HTMLElement>();
const dropdown_element = ref<HTMLElement>();
let dropdown: Dropdown | null = null;

const is_tracker_with_submittable_section = computed(
    () =>
        configuration_store.selected_tracker.value &&
        isTrackerWithSubmittableSection(configuration_store.selected_tracker.value),
);

onMounted(() => {
    if (trigger_element.value && dropdown_element.value) {
        dropdown = createDropdown(trigger_element.value, {
            trigger: "click",
            dropdown_menu: dropdown_element.value,
            keyboard: true,
        });
    }
});

onUnmounted(() => {
    if (dropdown) {
        dropdown.destroy();
    }
});

function addExistingSection(): void {
    add_existing_section_bus.openModal(props.position, (section: ArtidocSection): void => {
        props.insert_section_callback(section, props.position);
    });
}

function addNewSection(): void {
    if (!is_tracker_with_submittable_section.value) {
        openConfigurationModalBeforeInsertingNewSection();
        return;
    }

    insertNewSection();
}

function openConfigurationModalBeforeInsertingNewSection(): void {
    configuration_bus.openModal(insertNewSection);
}

function insertNewSection(): void {
    if (!configuration_store.selected_tracker.value) {
        return;
    }

    if (!isTrackerWithSubmittableSection(configuration_store.selected_tracker.value)) {
        return;
    }

    const section: PendingArtifactSection = PendingArtifactSectionFactory.overrideFromTracker(
        configuration_store.selected_tracker.value,
    );

    props.insert_section_callback(section, props.position);
}
</script>

<style scoped lang="scss">
@use "@/themes/includes/whitespace";
@use "@/themes/includes/size";

$half: calc(#{size.$add-section-button-container-height} * 0.5);
$half-minus-one-px: calc(#{$half} - 1px);
$half-plus-one-px: calc(#{$half} + 1px);

.artidoc-add-new-section-container {
    --add-new-section-button-background-color: var(--tlp-neutral-light-color);
    --add-new-section-button-text-color: var(--tlp-typo-default-text-color);

    display: flex;
    justify-content: center;
    margin: 0 whitespace.$section-right-padding 0 whitespace.$section-left-padding;
    padding: whitespace.$add-section-button-container-vertical-padding 0;
    transition: background-color ease-in-out 150ms;
    background: linear-gradient(
        0deg,
        var(--tlp-white-color) 0,
        var(--tlp-white-color) $half-minus-one-px,
        var(--add-new-section-button-background-color) $half,
        var(--tlp-white-color) $half-plus-one-px,
        var(--tlp-white-color) 100%
    );

    &:has(button:hover, button:focus-within) {
        --add-new-section-button-background-color: var(--tlp-main-color);
        --add-new-section-button-text-color: var(--tlp-white-color);
    }

    @media print {
        display: none;
    }
}

.artidoc-add-new-section-solo-button {
    width: size.$add-section-button-size;
    height: size.$add-section-button-size;
    padding: 0;
    transition: all ease-in-out 150ms;
    border-radius: 50%;
    border-color: var(--add-new-section-button-background-color);
    background: var(--add-new-section-button-background-color);
    color: var(--add-new-section-button-text-color);

    &:focus-within,
    &:hover {
        border-color: var(--add-new-section-button-background-color);
        background: var(--add-new-section-button-background-color);
        color: var(--add-new-section-button-text-color);
    }
}

.artidoc-add-new-section-submenu {
    transform: translate(-8px, 8px);
}
</style>
