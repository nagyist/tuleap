<?php
/**
 * Copyright (c) Enalean, 2023-Present. All Rights Reserved.
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

namespace Tuleap\Tracker\Artifact\ChangesetValue\ArtifactLink;

use Tuleap\Test\PHPUnit\TestCase;
use Tuleap\Tracker\Test\Builders\ArtifactTestBuilder;
use Tuleap\Tracker\Test\Stub\ReverseLinkStub;

#[\PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles]
final class AddReverseLinksCommandTest extends TestCase
{
    public function testItBuildsFromParts(): void
    {
        $target_artifact = ArtifactTestBuilder::anArtifact(481)->build();
        $links_to_add    = new CollectionOfReverseLinks([
            ReverseLinkStub::withType(492, '_is_child'),
            ReverseLinkStub::withNoType(862),
        ]);

        $command = AddReverseLinksCommand::fromParts($target_artifact, $links_to_add);

        self::assertSame($target_artifact, $command->getTargetArtifact());
        self::assertSame($links_to_add, $command->getLinksToAdd());
    }
}
