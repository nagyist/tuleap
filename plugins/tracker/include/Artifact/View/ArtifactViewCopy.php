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
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Tuleap\Tracker\Artifact\View;

final readonly class ArtifactViewCopy extends ArtifactViewEdit
{
    /** @see ArtifactViewEdit::getURL() */
    public function getURL(): string
    {
        return TRACKER_BASE_URL . '/?' . http_build_query(
            [
                'aid' => $this->artifact->getId(),
                'func' => 'copy-artifact',
            ]
        );
    }

    /** @see ArtifactViewEdit::getTitle() */
    public function getTitle(): string
    {
        return dgettext('tuleap-tracker', 'Artifact');
    }

    /** @see ArtifactViewEdit::fetch() */
    public function fetch(): string
    {
        self::fetchEditViewJSCode();

        $html  = '';
        $html .= '<div class="tracker_artifact">';
        $html .= $this->renderer->fetchFieldsForCopy($this->artifact);
        $html .= '</div>';
        return $html;
    }
}
