/*
 * Copyright (c) Enalean, 2025 - present. All Rights Reserved.
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

import { ref } from "vue";
import type { ArtidocSection } from "@/helpers/artidoc-section.type";
import type { ReactiveStoredArtidocSection } from "@/sections/SectionsCollection";
import { injectInternalId } from "@/helpers/inject-internal-id";

export const ReactiveStoredArtidocSectionStub = {
    fromSection: (artidoc_section: ArtidocSection): ReactiveStoredArtidocSection =>
        ref(injectInternalId(artidoc_section)),
    fromCollection: (collection: ArtidocSection[]): ReactiveStoredArtidocSection[] =>
        collection.map((artidoc_section) => ref(injectInternalId(artidoc_section))),
};
