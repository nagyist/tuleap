<!--
  - Copyright (c) Enalean, 2025 - Present. All Rights Reserved.
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
    <div class="tlp-alert-danger">
        {{ error_message }}
    </div>
</template>

<script setup lang="ts">
defineProps<{
    error_message: string;
}>();
</script>

<style scoped lang="scss">
.tlp-alert-danger {
    z-index: 1;
    grid-column: 1 / -1;
    margin: 0;
    padding: var(--tlp-small-spacing) var(--tlp-small-spacing) var(--tlp-small-spacing) 42px;

    &::before {
        top: calc(var(--tlp-small-spacing) + 1px);
    }
}
</style>
