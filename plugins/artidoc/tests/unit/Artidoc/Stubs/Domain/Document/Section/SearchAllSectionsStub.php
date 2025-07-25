<?php
/**
 * Copyright (c) Enalean, 2025 - Present. All Rights Reserved.
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

namespace Tuleap\Artidoc\Stubs\Domain\Document\Section;

use Tuleap\Artidoc\Domain\Document\ArtidocWithContext;
use Tuleap\Artidoc\Domain\Document\Section\RetrievedSection;
use Tuleap\Artidoc\Domain\Document\Section\SearchAllSections;

final class SearchAllSectionsStub implements SearchAllSections
{
    /**
     * @param list<RetrievedSection> $sections
     */
    private function __construct(private array $sections)
    {
    }

    public static function withoutSections(): self
    {
        return new self([]);
    }

    /**
     * @param list<RetrievedSection> $sections
     */
    public static function withSections(array $sections): self
    {
        return new self($sections);
    }

    #[\Override]
    public function searchAllSectionsOfDocument(ArtidocWithContext $artidoc): array
    {
        return $this->sections;
    }
}
