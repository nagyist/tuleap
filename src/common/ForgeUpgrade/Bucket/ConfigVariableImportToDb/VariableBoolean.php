<?php
/*
 * Copyright (c) Enalean, 2025-Present. All Rights Reserved.
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
 *
 */

declare(strict_types=1);

namespace Tuleap\ForgeUpgrade\Bucket\ConfigVariableImportToDb;

/**
 * @psalm-immutable
 */
final readonly class VariableBoolean implements Variable
{
    private function __construct(private string $name_in_file, private string $name_in_db, private bool $default_value)
    {
    }

    public static function withSameName(string $name, bool $default_value): self
    {
        return new self($name, $name, $default_value);
    }

    public static function withNewName(string $name_in_file, string $name_in_db, bool $default_value): self
    {
        return new self($name_in_file, $name_in_db, $default_value);
    }

    public function getNameInFile(): string
    {
        return $this->name_in_file;
    }

    public function getNameInDb(): string
    {
        return $this->name_in_db;
    }

    public function getValueAsString(mixed $value): string
    {
        return match ($value) {
            true, '1', 1 => '1',
            false, '0', 0 => '0',
            default => $this->default_value ? '1' : '0',
        };
    }
}
