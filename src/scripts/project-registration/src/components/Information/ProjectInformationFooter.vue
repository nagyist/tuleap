<!--
  - Copyright (c) Enalean, 2019 - present. All Rights Reserved.
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
  - along with Tuleap. If not, see http://www.gnu.org/licenses/.
  -
  -->

<template>
    <div class="project-registration-button-container">
        <div class="project-registration-content">
            <div>
                <router-link
                    to="/new"
                    v-on:click="resetProjectCreationError"
                    class="project-registration-back-button"
                    data-test="project-registration-back-button"
                >
                    <i class="fas fa-long-arrow-alt-left"></i>
                    <span class="project-registration-back-button-text">{{
                        $gettext("Back")
                    }}</span>
                </router-link>
                <button
                    type="submit"
                    class="tlp-button-primary tlp-button-large tlp-form-element-disabled project-registration-next-button"
                    data-test="project-registration-next-button"
                    v-bind:disabled="root_store.is_creating_project && !root_store.has_error"
                >
                    <span>{{ $gettext("Start my project") }}</span>
                    <i v-bind:class="get_icon" data-test="project-submission-icon" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useStore } from "../../stores/root";

const root_store = useStore();

const get_icon = computed((): string => {
    if (!root_store.is_creating_project) {
        return "fa tlp-button-icon-right fa-arrow-circle-o-right";
    }

    return "fa tlp-button-icon-right fa-spin fa-circle-o-notch";
});

function resetProjectCreationError(): void {
    root_store.resetProjectCreationError();
}
</script>
