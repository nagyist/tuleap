/*
 * Copyright (c) Enalean, 2023 - present. All Rights Reserved.
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

import type { ResultAsync } from "neverthrow";
import type { Fault } from "@tuleap/fault";
import type { PullRequest } from "@tuleap/plugin-pullrequest-rest-api-types";
import { uri, getAllJSON } from "@tuleap/fetch-result";

type PullRequestCollection = {
    readonly collection: PullRequest[];
};

export const fetchAllPullRequests = (
    repository_id: number,
): ResultAsync<readonly PullRequest[], Fault> => {
    return getAllJSON<PullRequest, PullRequestCollection>(
        uri`/api/v1/git/${repository_id}/pull_requests`,
        {
            params: {
                limit: 50,
            },
            getCollectionCallback: (payload) => payload.collection,
        },
    );
};