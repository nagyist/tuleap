<?php
/**
 * Copyright (c) Enalean, 2020 - present. All Rights Reserved.
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

declare(strict_types=1);

namespace Tuleap\Tracker\Admin;

use PHPUnit\Framework\MockObject\MockObject;
use ReferenceManager;
use TrackerFactory;
use Tuleap\Tracker\TrackerIsInvalidException;

#[\PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles]
final class TrackerGeneralSettingsCheckerTest extends \Tuleap\Test\PHPUnit\TestCase
{
    private TrackerGeneralSettingsChecker $checker;
    private ReferenceManager&MockObject $reference_manager;
    private TrackerFactory&MockObject $tracker_factory;

    protected function setUp(): void
    {
        $this->tracker_factory   = $this->createMock(\TrackerFactory::class);
        $this->reference_manager = $this->createMock(\ReferenceManager::class);
        $this->checker           = new TrackerGeneralSettingsChecker($this->tracker_factory, $this->reference_manager);
    }

    public function testItThrowAnExceptionWhenNewTrackerLengthIsInvalid(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'Bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'bugs-with-a-very-very-long-shortname';

        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('Tracker shortname length must be inferior to 25 characters.');
        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItDoesNotThrowAnExceptionWhenOldTrackerLengthWasInvalid(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'Bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'bugs_renamed';

        $this->tracker_factory->method('isNameExists')->willReturn(false);
        $this->tracker_factory->method('isShortNameExists')->willReturn(false);

        $this->reference_manager->method('checkKeyword')->willReturn(true);
        $this->reference_manager->method('_isKeywordExists')->willReturn(false);

        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );

        $this->addToAssertionCount(1);
    }

    public function testItThrowsAnExceptionWhenPublicNameIsNotProvided(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = '';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'bugs-renamed';

        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('Name, color, and short name are required.');
        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionWhenShortNameIsNotProvided(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'Bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = '';

        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('Name, color, and short name are required.');
        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionWhenColorIsNotProvided(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'Bugs';
        $validated_tracker_color = '';
        $validated_short_name    = 'bugs';

        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('Name, color, and short name are required.');
        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionIfPublicNameAlreadyExists(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'New bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'bugs';

        $this->tracker_factory->method('isNameExists')->willReturn(true);
        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('The tracker name New bugs is already used. Please use another one.');

        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionWhenShortNameIsInvalid(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'New bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = '+++bugs+++';

        $this->tracker_factory->method('isNameExists')->willReturn(false);
        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage(
            'Invalid short name: +++bugs+++. Please use only alphanumerical characters or an unreserved reference.'
        );

        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionIfShortNameAlreadyExists(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'New bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'new_bugs';

        $this->tracker_factory->method('isNameExists')->willReturn(false);
        $this->tracker_factory->method('isShortNameExists')->willReturn(true);
        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('The tracker short name new_bugs is already used. Please use another one.');

        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionWhenReferenceKeywordIsInvalid(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'New bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'new-bugs';

        $this->tracker_factory->method('isNameExists')->willReturn(false);
        $this->tracker_factory->method('isShortNameExists')->willReturn(false);

        $this->reference_manager->method('checkKeyword')->willReturn(false);
        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage(
            'Invalid short name: new-bugs. Please use only alphanumerical characters or an unreserved reference.'
        );

        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }

    public function testItThrowsAnExceptionWhenReferenceKeywordAlreadyExists(): void
    {
        $project_id              = 101;
        $previous_shortname      = 'bugs';
        $previous_public_name    = 'Bugs';
        $validated_public_name   = 'New bugs';
        $validated_tracker_color = 'inca-silver';
        $validated_short_name    = 'new_bugs';

        $this->tracker_factory->method('isNameExists')->willReturn(false);
        $this->tracker_factory->method('isShortNameExists')->willReturn(false);

        $this->reference_manager->method('checkKeyword')->willReturn(true);
        $this->reference_manager->method('_isKeywordExists')->willReturn(true);
        $this->expectException(TrackerIsInvalidException::class);
        $this->expectExceptionMessage('The tracker short name new_bugs is already used. Please use another one.');

        $this->checker->check(
            $project_id,
            $previous_shortname,
            $previous_public_name,
            $validated_public_name,
            $validated_tracker_color,
            $validated_short_name
        );
    }
}
