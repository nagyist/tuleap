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

import type { Fault } from "@tuleap/fault";
import type { ResultAsync } from "neverthrow";
import type { StoredArtidocSection } from "@/sections/SectionsCollection";
import { getAllSections } from "@/helpers/rest-querier";
import type { ArtidocSection } from "@/helpers/artidoc-section.type";
import { CreateStoredSections } from "@/sections/states/CreateStoredSections";

export type LoadSections = {
    loadSections(): ResultAsync<StoredArtidocSection[], Fault>;
};

export const getSectionsLoader = (artidoc_id: number): LoadSections => ({
    loadSections(): ResultAsync<StoredArtidocSection[], Fault> {
        return getAllSections(artidoc_id).map((artidoc_sections: readonly ArtidocSection[]) =>
            CreateStoredSections.fromArtidocSectionsCollection(artidoc_sections),
        );
    },
});
