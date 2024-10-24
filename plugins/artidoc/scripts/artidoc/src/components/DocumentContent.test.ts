/*
 * Copyright (c) Enalean, 2024 - Present. All Rights Reserved.
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

import { beforeEach, describe, expect, it } from "vitest";
import { ref } from "vue";
import { flushPromises, shallowMount } from "@vue/test-utils";
import DocumentContent from "@/components/DocumentContent.vue";
import ArtifactSectionFactory from "@/helpers/artifact-section.factory";
import SectionContainer from "@/components/section/SectionContainer.vue";
import type { SectionsStore } from "@/stores/useSectionsStore";
import { InjectedSectionsStoreStub } from "@/helpers/stubs/InjectSectionsStoreStub";
import { CAN_USER_EDIT_DOCUMENT } from "@/can-user-edit-document-injection-key";
import AddNewSectionButton from "./AddNewSectionButton.vue";
import PendingArtifactSectionFactory from "@/helpers/pending-artifact-section.factory";
import { SECTIONS_STORE } from "@/stores/sections-store-injection-key";
import type { ArtidocSection } from "@/helpers/artidoc-section.type";
import { EDITOR_CHOICE } from "@/helpers/editor-choice";
import EditorToolbar from "@/components/toolbar/EditorToolbar.vue";

describe("DocumentContent", () => {
    let sections_store: SectionsStore;
    let section_1: ArtidocSection, section_2: ArtidocSection, section_3: ArtidocSection;

    beforeEach(() => {
        const default_artifact_section = ArtifactSectionFactory.create();
        const default_pending_artifact_section = PendingArtifactSectionFactory.create();
        section_1 = ArtifactSectionFactory.override({
            title: { ...default_artifact_section.title, value: "Title 1" },
            artifact: { ...default_artifact_section.artifact, id: 1 },
        });
        section_2 = ArtifactSectionFactory.override({
            title: { ...default_artifact_section.title, value: "Title 2" },
            artifact: { ...default_artifact_section.artifact, id: 2 },
        });
        section_3 = PendingArtifactSectionFactory.override({
            title: { ...default_pending_artifact_section.title, value: "Title 3" },
        });

        sections_store = InjectedSectionsStoreStub.withLoadedSections([
            section_1,
            section_2,
            section_3,
        ]);
    });

    it("should display the two sections", () => {
        const list = shallowMount(DocumentContent, {
            global: {
                provide: {
                    [SECTIONS_STORE.valueOf()]: sections_store,
                    [CAN_USER_EDIT_DOCUMENT.valueOf()]: false,
                    [CAN_USER_EDIT_DOCUMENT.valueOf()]: false,
                    [EDITOR_CHOICE.valueOf()]: { is_prose_mirror: ref(false) },
                },
            },
        }).find("ol");
        expect(list.findAllComponents(SectionContainer)).toHaveLength(3);
    });

    it("sections should have an id for anchor feature except pending artifact section", () => {
        const list = shallowMount(DocumentContent, {
            global: {
                provide: {
                    [SECTIONS_STORE.valueOf()]: sections_store,
                    [CAN_USER_EDIT_DOCUMENT.valueOf()]: false,
                    [EDITOR_CHOICE.valueOf()]: { is_prose_mirror: ref(false) },
                },
            },
        }).find("ol");
        const sections = list.findAll("li");
        expect(sections[0].attributes().id).toBe(`section-${section_1.id}`);
        expect(sections[1].attributes().id).toBe(`section-${section_2.id}`);
        expect(sections[2].attributes().id).toBe(`section-${section_3.id}`);
    });

    it("should not display add new section button if user cannot edit the document", () => {
        expect(
            shallowMount(DocumentContent, {
                global: {
                    provide: {
                        [SECTIONS_STORE.valueOf()]: sections_store,
                        [CAN_USER_EDIT_DOCUMENT.valueOf()]: false,
                        [EDITOR_CHOICE.valueOf()]: { is_prose_mirror: ref(false) },
                    },
                },
            }).findAllComponents(AddNewSectionButton),
        ).toHaveLength(0);
    });

    it("should display n+1 add new section button if user can edit the document", () => {
        expect(
            shallowMount(DocumentContent, {
                global: {
                    provide: {
                        [SECTIONS_STORE.valueOf()]: sections_store,
                        [CAN_USER_EDIT_DOCUMENT.valueOf()]: true,
                        [EDITOR_CHOICE.valueOf()]: { is_prose_mirror: ref(false) },
                    },
                },
            }).findAllComponents(AddNewSectionButton),
        ).toHaveLength(4);
    });

    it("should display the mono-toolbar only when the current editor type used is prose-mirror and the user can edit the document", async () => {
        const is_prose_mirror = ref(false);
        const can_user_edit_document = true;
        const wrapper = shallowMount(DocumentContent, {
            global: {
                provide: {
                    [SECTIONS_STORE.valueOf()]: sections_store,
                    [CAN_USER_EDIT_DOCUMENT.valueOf()]: can_user_edit_document,
                    [EDITOR_CHOICE.valueOf()]: { is_prose_mirror },
                },
            },
        });

        expect(wrapper.findComponent(EditorToolbar).exists()).toBe(false);

        is_prose_mirror.value = true;
        await flushPromises();

        expect(wrapper.findComponent(EditorToolbar).exists()).toBe(true);
    });
});
