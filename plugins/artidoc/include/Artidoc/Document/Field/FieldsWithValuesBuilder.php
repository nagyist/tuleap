<?php
/**
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
 */

declare(strict_types=1);

namespace Tuleap\Artidoc\Document\Field;

use Override;
use Tracker_Artifact_Changeset;
use Tracker_Artifact_ChangesetValue_List;
use Tracker_Artifact_ChangesetValue_Numeric;
use Tracker_Artifact_ChangesetValue_String;
use Tracker_FormElement_Field_List;
use Tracker_FormElement_Field_Numeric;
use Tuleap\Artidoc\Document\Field\ArtifactLink\BuildArtifactLinkFieldWithValue;
use Tuleap\Artidoc\Document\Field\List\BuildListFieldWithValue;
use Tuleap\Artidoc\Document\Field\Numeric\BuildNumericFieldWithValue;
use Tuleap\Artidoc\Domain\Document\Section\Field\FieldWithValue\StringFieldWithValue;
use Tuleap\Tracker\Artifact\Changeset\ArtifactLink\ArtifactLinkChangesetValue;
use Tuleap\Tracker\FormElement\Field\ArtifactLink\ArtifactLinkField;
use Tuleap\Tracker\FormElement\Field\String\StringField;

final readonly class FieldsWithValuesBuilder implements GetFieldsWithValues
{
    public function __construct(
        private ConfiguredFieldCollection $field_collection,
        private BuildListFieldWithValue $build_list_field_with_value,
        private BuildArtifactLinkFieldWithValue $build_artlink_field_with_value,
        private BuildNumericFieldWithValue $build_numeric_field_with_value,
    ) {
    }

    #[Override]
    public function getFieldsWithValues(Tracker_Artifact_Changeset $changeset): array
    {
        $fields  = [];
        $tracker = $changeset->getTracker();
        foreach ($this->field_collection->getFields($tracker) as $configured_field) {
            $changeset_value = $changeset->getValue($configured_field->field);

            if ($configured_field->field instanceof StringField) {
                assert($changeset_value === null || $changeset_value instanceof Tracker_Artifact_ChangesetValue_String);
                $fields[] = new StringFieldWithValue(
                    $configured_field->field->getLabel(),
                    $configured_field->display_type,
                    $changeset_value?->getValue() ?? '',
                );
            }

            if ($configured_field->field instanceof Tracker_FormElement_Field_List) {
                assert($changeset_value === null || $changeset_value instanceof Tracker_Artifact_ChangesetValue_List);
                $fields[] = $this->build_list_field_with_value->buildListFieldWithValue($configured_field, $changeset_value);
            }

            if ($configured_field->field instanceof ArtifactLinkField) {
                assert($changeset_value === null || $changeset_value instanceof ArtifactLinkChangesetValue);
                $fields[] = $this->build_artlink_field_with_value->buildArtifactLinkFieldWithValue($configured_field, $changeset_value);
            }

            if ($configured_field->field instanceof Tracker_FormElement_Field_Numeric) {
                assert($changeset_value === null || $changeset_value instanceof Tracker_Artifact_ChangesetValue_Numeric);
                $fields[] = $this->build_numeric_field_with_value->buildNumericFieldWithValue($configured_field, $changeset->getArtifact(), $changeset_value);
            }
        }

        return $fields;
    }
}
