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
  - along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div class="project-release-timeframe">
        <span class="project-release-label"> {{ $gettext("What's hot") }}</span>
        <release-displayer
            v-for="(milestone, index) in root_store.current_milestones"
            v-bind:key="milestone.id"
            v-bind:is_open="index === 0"
            v-bind:release_data="milestone"
            v-bind:data-test="`current-milestones-test-${milestone.label}`"
            v-bind:is_past_release="false"
        />
    </div>
</template>

<script setup lang="ts">
import ReleaseDisplayer from "./ReleaseDisplayer.vue";
import { useStore } from "../../stores/root";

const root_store = useStore();
</script>
