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

export interface SemiStructuredContent {
    readonly title_as_html: string;
    readonly accent_color: string;
    readonly body_as_html: string;
}

export function isSemiStructuredContent(
    content: string | SemiStructuredContent,
): content is SemiStructuredContent {
    return typeof content !== "string";
}

export interface ElementWithCrossrefHref {
    element: HTMLElement;
    getHref: () => string;
}

export interface Tooltip {
    destroy: () => void;
}
