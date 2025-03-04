<?php
/**
 * Copyright (c) Enalean, 2019 - Present. All Rights Reserved.
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
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

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
#[\PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles]
final class PluginInfoTest extends \Tuleap\Test\PHPUnit\TestCase
{
    public function testPluginDescriptor(): void
    {
        $pi = new PluginInfo(new \Plugin(null));
        $pd = $pi->getPluginDescriptor();
        $this->assertInstanceOf(\PluginDescriptor::class, $pd);
        $this->assertEquals('', $pd->getFullName());
        $this->assertEquals('', $pd->getDescription());
        $pi->setPluginDescriptor(new PluginDescriptor('TestPlugin', 'A simple plugin, just for unit testing'));

        $pd = $pi->getPluginDescriptor();
        $this->assertEquals('TestPlugin', $pd->getFullName());
        $this->assertEquals('A simple plugin, just for unit testing', $pd->getDescription());
    }
}
