<?php
/**
 * Copyright (c) Enalean, 2024-Present. All Rights Reserved.
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

declare(strict_types=1);

namespace Tuleap\Tracker\Test\Stub\Webhook;

use Tuleap\Tracker\Webhook\ArtifactPayload;
use Tuleap\Tracker\Webhook\ArtifactPayloadBuilder;

final readonly class ArtifactPayloadBuilderStub extends ArtifactPayloadBuilder
{
    private function __construct(
        private ArtifactPayload $payload,
    ) {
    }

    public static function withEmptyPayload(): self
    {
        return new self(new ArtifactPayload([]));
    }

    #[\Override]
    public function buildPayload(\Tracker_Artifact_Changeset $last_changeset): ArtifactPayload
    {
        return $this->payload;
    }
}
