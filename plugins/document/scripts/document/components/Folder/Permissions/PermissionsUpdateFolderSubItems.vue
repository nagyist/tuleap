<!--
  - Copyright (c) Enalean, 2019-Present. All Rights Reserved.
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
    <div class="tlp-form-element" v-if="is_item_a_folder">
        <label class="tlp-label tlp-checkbox">
            <input
                type="checkbox"
                v-on:input="onInput"
                data-test="checkbox-apply-permissions-on-children"
                v-bind:checked="value"
            />
            {{ $gettext("Apply same permissions to all sub-items of this folder") }}
        </label>
    </div>
</template>
<script setup lang="ts">
import { isFolder } from "../../../helpers/type-check-helper";
import type { Folder } from "../../../type";
import { computed } from "vue";
import emitter from "../../../helpers/emitter";

const props = defineProps<{ item: Folder; value: boolean }>();

function onInput($event: Event): void {
    const event_target = $event.target;

    if (event_target instanceof HTMLInputElement) {
        emitter.emit("update-apply-permissions-on-children", {
            do_permissions_apply_on_children: event_target.checked,
        });
    }
}

const is_item_a_folder = computed((): boolean => {
    return isFolder(props.item);
});
</script>
