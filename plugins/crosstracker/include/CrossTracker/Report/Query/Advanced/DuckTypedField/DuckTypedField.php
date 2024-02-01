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

namespace Tuleap\CrossTracker\Report\Query\Advanced\DuckTypedField;

use Tuleap\NeverThrow\Err;
use Tuleap\NeverThrow\Fault;
use Tuleap\NeverThrow\Ok;
use Tuleap\NeverThrow\Result;
use Tuleap\Tracker\FormElement\Field\RetrieveUsedFields;
use Tuleap\Tracker\FormElement\RetrieveFieldType;

/**
 * @psalm-immutable
 */
final class DuckTypedField
{
    /**
     * @param list<int> $field_ids
     */
    private function __construct(
        public readonly string $name,
        public readonly array $field_ids,
        public readonly DuckTypedFieldType $type,
    ) {
    }

    /**
     * @param int[] $tracker_ids
     * @return Ok<self>|Err<Fault>
     */
    public static function build(
        RetrieveUsedFields $retrieve_used_fields,
        RetrieveFieldType $retrieve_field_type,
        string $field_name,
        array $tracker_ids,
        \PFUser $user,
    ): Ok|Err {
        $fields_user_can_read = [];
        foreach ($tracker_ids as $tracker_id) {
            $used_field = $retrieve_used_fields->getUsedFieldByName($tracker_id, $field_name);
            if ($used_field && $used_field->userCanRead($user)) {
                $fields_user_can_read[] = DuckTypedFieldType::fromString($retrieve_field_type->getType($used_field))
                    ->map(static fn(DuckTypedFieldType $type) => new FieldIdentifierProperties($used_field->getId(), $type));
            }
        }
        if (count($fields_user_can_read) === 0) {
            return Result::err(FieldNotFoundInAnyTrackerFault::build());
        }
        $other_results = array_slice($fields_user_can_read, 1);

        return $fields_user_can_read[0]->andThen(
            static function (FieldIdentifierProperties $first_field) use ($field_name, $tracker_ids, $other_results) {
                $field_ids = [$first_field->id];
                foreach ($other_results as $other_result) {
                    if (Result::isErr($other_result)) {
                        return Result::err(FieldTypesAreIncompatibleFault::build($field_name, $tracker_ids));
                    }
                    $field_ids[] = $other_result->value->id;
                }
                return Result::ok(new self($field_name, $field_ids, $first_field->type));
            }
        );
    }
}