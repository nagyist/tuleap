<!--
  - Copyright (c) Enalean, 2018 - Present. All Rights Reserved.
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
    <div class="colorpicker-row-no-color" v-on:click="updateColor">
        <span
            class="colorpicker-circular-color colorpicker-circular-no-color"
            v-bind:title="$gettext('No color')"
            v-bind:class="{ 'colorpicker-no-color-selected fa fa-check': is_selected }"
        ></span>
        <span class="colorpicker-row-no-color-label">{{ $gettext("No color") }}</span>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useGettext } from "vue3-gettext";

const props = defineProps<{ selected_color: string }>();

const { $gettext } = useGettext();

const is_selected = computed((): boolean => props.selected_color === "");

const emit = defineEmits<{
    (e: "color-update", value: string): void;
}>();

function updateColor(): void {
    emit("color-update", "");
}
</script>
