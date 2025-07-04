<?php
/**
 * Copyright (c) Enalean, 2014 - Present. All Rights Reserved.
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
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

declare(strict_types=1);

namespace Tuleap\Kanban;

use Tuleap\Tracker\Tracker;

class Kanban
{
    private const PROMOTED_ITEM_PREFIX = 'kanban-';

    public function __construct(
        private readonly int $id,
        public readonly Tracker $tracker,
        public readonly bool $is_promoted,
        private readonly string $name,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTrackerId(): int
    {
        return $this->tracker->getId();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPromotedKanbanId(): string
    {
        return self::PROMOTED_ITEM_PREFIX . $this->getId();
    }
}
