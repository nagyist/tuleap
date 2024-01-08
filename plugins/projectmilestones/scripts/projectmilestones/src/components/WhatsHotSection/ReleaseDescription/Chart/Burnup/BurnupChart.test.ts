/*
 * Copyright (c) Enalean, 2020 - present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

import type { MilestoneData } from "../../../../../type";
import type { ShallowMountOptions, Wrapper } from "@vue/test-utils";
import { shallowMount } from "@vue/test-utils";
import { createReleaseWidgetLocalVue } from "../../../../../helpers/local-vue-for-test";
import BurnupChart from "./BurnupChart.vue";

const component_options: ShallowMountOptions<BurnupChart> = {};
let release_data: MilestoneData;
import { createTestingPinia } from "@pinia/testing";
import { defineStore } from "pinia";

describe("BurnupChart", () => {
    async function getPersonalWidgetInstance(): Promise<Wrapper<BurnupChart>> {
        const useStore = defineStore("root", {
            state: () => ({}),
        });
        const pinia = createTestingPinia();
        useStore(pinia);

        component_options.localVue = await createReleaseWidgetLocalVue();

        return shallowMount(BurnupChart, component_options);
    }

    beforeEach(() => {
        release_data = {
            id: 2,
        } as MilestoneData;

        component_options.propsData = {
            release_data,
            burnup_data: null,
        };

        getPersonalWidgetInstance();
    });

    it("When component is renderer, Then there is a svg element with id of release", async () => {
        const wrapper = await getPersonalWidgetInstance();
        expect(wrapper.element).toMatchSnapshot();
    });
});