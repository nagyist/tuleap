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
    <div class="timetracking-widget">
        <widget-reading-mode v-if="personal_store.reading_mode" />
        <widget-writing-mode v-else />
        <widget-artifact-table />
    </div>
</template>

<script setup lang="ts">
import { usePersonalTimetrackingWidgetStore } from "../store/root";
import WidgetArtifactTable from "./WidgetArtifactTable.vue";
import WidgetReadingMode from "./WidgetReadingMode.vue";
import WidgetWritingMode from "./WidgetWritingMode.vue";

const props = defineProps<{
    user_id: number;
    user_locale: string;
}>();

const personal_store = usePersonalTimetrackingWidgetStore();
personal_store.initUserId(props.user_id);
personal_store.initUserLocale(props.user_locale);
</script>
