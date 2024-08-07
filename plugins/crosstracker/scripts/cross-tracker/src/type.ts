/**
 * Copyright (c) Enalean, 2021 - Present. All Rights Reserved.
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

import type {
    TrackerProjectRepresentation,
    TrackerResponseWithProject,
} from "@tuleap/plugin-tracker-rest-api-types";

export type InvalidTracker = {
    readonly id: number;
    readonly label: string;
    readonly project: { readonly label: string };
};
export type TrackerInfo = Pick<TrackerResponseWithProject, "id" | "label">;
export type ProjectInfo = Pick<TrackerProjectRepresentation, "id" | "uri" | "label">;

export type SelectedTracker = {
    readonly tracker_id: number;
};

export type TrackerAndProject = {
    readonly project: Pick<ProjectInfo, "id" | "label">;
    readonly tracker: TrackerInfo;
};

export type TrackerToUpdate = {
    readonly tracker_id: number;
    readonly tracker_label: string;
    readonly project_label: string;
};

export type Report = {
    readonly trackers: ReadonlyArray<TrackerAndProject>;
    readonly expert_query: string;
    readonly invalid_trackers: ReadonlyArray<InvalidTracker>;
    readonly expert_mode: boolean;
};

export type ArtifactsCollection = {
    readonly artifacts: ReadonlyArray<Artifact>;
    readonly total: number;
};

export type Artifact = {
    readonly id: number;
    readonly title: string;
    readonly badge: {
        readonly uri: string;
        readonly cross_ref: string;
        readonly color: string;
    };
    formatted_last_update_date?: string;
    readonly last_update_date: string;
    readonly status: string;
    readonly submitted_by: User;
    readonly assigned_to: ReadonlyArray<User>;
    readonly project: TrackerProjectRepresentation;
};

export type User = {
    readonly id: number;
    readonly display_name: string;
    readonly user_url: string;
};
