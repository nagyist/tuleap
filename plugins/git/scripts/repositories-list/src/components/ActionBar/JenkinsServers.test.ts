/**
 * Copyright (c) Enalean 2021 -  Present. All Rights Reserved.
 *
 *  This file is a part of Tuleap.
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
 *
 */

import { shallowMount } from "@vue/test-utils";
import JenkinsServer from "./JenkinsServers.vue";
import { getGlobalTestOptions } from "../../helpers/global-options-for-tests";

jest.mock("@tuleap/tlp-popovers"); // ResizeObserver is not defined

describe("JenkinsServer", () => {
    it("displays the badge and a basic popover", () => {
        const wrapper = shallowMount(JenkinsServer, {
            global: { ...getGlobalTestOptions({}) },
            props: {
                servers: [{ id: 1, url: "https://example.com" }],
            },
        });

        expect(wrapper).toMatchSnapshot();
    });
});
