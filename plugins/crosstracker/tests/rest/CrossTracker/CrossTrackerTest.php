<?php
/**
 *  Copyright (c) Enalean, 2017 - Present. All Rights Reserved.
 *
 *  This file is a part of Tuleap.
 *
 *  Tuleap is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  Tuleap is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\CrossTracker\REST\v1;

use REST_TestDataBuilder;
use RestBase;

final class CrossTrackerTest extends RestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->getEpicArtifactIds();
    }

    public function testGetId(): void
    {
        $response = $this->getResponse($this->request_factory->createRequest('GET', 'cross_tracker_reports/1'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertGetIdReport($response);
    }

    public function testGetIdForReadOnlyUser(): void
    {
        $response = $this->getResponse(
            $this->request_factory->createRequest('GET', 'cross_tracker_reports/1'),
            REST_TestDataBuilder::TEST_BOT_USER_NAME
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertGetIdReport($response);
    }

    private function assertGetIdReport(\Psr\Http\Message\ResponseInterface $response): void
    {
        $expected_cross_tracker = [
            'id'               => 1,
            'uri'              => 'cross_tracker_reports/1',
            'expert_query'     => '',
            'trackers'         => [
                [
                    'id'      => $this->kanban_tracker_id,
                    'uri'     => 'trackers/' . $this->kanban_tracker_id,
                    'label'   => REST_TestDataBuilder::KANBAN_TRACKER_LABEL,
                    'project' => [
                        'id'    => $this->project_private_member_id,
                        'uri'   => 'projects/' . $this->project_private_member_id,
                        'label' => REST_TestDataBuilder::PROJECT_PRIVATE_MEMBER_LABEL,
                        'icon' => '',
                    ],
                ],
            ],
            'invalid_trackers' => [],
            'report_mode'      => 'default',
        ];

        self::assertEquals(
            json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR),
            $expected_cross_tracker
        );
    }

    public function testPut()
    {
        $params   = [
            'trackers_id'  => [$this->epic_tracker_id],
            'expert_query' => '',
            'report_mode'  => 'default',
        ];
        $response = $this->getResponse($this->request_factory->createRequest('PUT', 'cross_tracker_reports/1')->withBody($this->stream_factory->createStream(json_encode($params))));

        self::assertEquals(201, $response->getStatusCode());

        $expected_cross_tracker = [
            'id'           => 1,
            'uri'          => 'cross_tracker_reports/1',
            'expert_query' => '',
            'trackers'     => [
                [
                    'id'    => $this->epic_tracker_id,
                    'uri'   => 'trackers/' . $this->epic_tracker_id,
                    'label' => REST_TestDataBuilder::EPICS_TRACKER_LABEL,
                    'project' => [
                        'id'    => $this->project_private_member_id,
                        'uri'   => 'projects/' . $this->project_private_member_id,
                        'label' => REST_TestDataBuilder::PROJECT_PRIVATE_MEMBER_LABEL,
                        'icon' => '',
                    ],
                ],
            ],
            'invalid_trackers' => [],
            'report_mode'      => 'default',
        ];

        self::assertEquals(
            json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR),
            $expected_cross_tracker
        );
    }

    public function testPutForReadOnlyUser(): void
    {
        $params   = [
            'trackers_id'  => [$this->epic_tracker_id],
            'expert_query' => '',
            'report_mode'  => 'default',
        ];
        $response = $this->getResponse(
            $this->request_factory->createRequest('PUT', 'cross_tracker_reports/1')->withBody($this->stream_factory->createStream(json_encode($params))),
            REST_TestDataBuilder::TEST_BOT_USER_NAME
        );

        self::assertEquals(403, $response->getStatusCode());
    }

    public function testGetContentId(): void
    {
        $response = $this->getResponse($this->request_factory->createRequest('GET', 'cross_tracker_reports/1/content?limit=50&offset=0'));

        self::assertEquals(200, $response->getStatusCode());
        $cross_tracker_artifacts = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertGetReport($cross_tracker_artifacts);
    }

    public function testGetContentIdForReadOnlyUser(): void
    {
        $response = $this->getResponse(
            $this->request_factory->createRequest('GET', 'cross_tracker_reports/1/content?limit=50&offset=0'),
            REST_TestDataBuilder::TEST_BOT_USER_NAME
        );

        self::assertEquals(200, $response->getStatusCode());
        $cross_tracker_artifacts = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertGetReport($cross_tracker_artifacts);
    }

    public function testGetContentIdWithQuery(): void
    {
        $query    = json_encode(
            [
                'trackers_id'  => [1],
                'expert_query' => '',
            ]
        );
        $response = $this->getResponse(
            $this->request_factory->createRequest('GET', 'cross_tracker_reports/1/content?limit=50&offset=0&query=' . urlencode($query))
        );

        self::assertEquals(200, $response->getStatusCode());
        $cross_tracker_artifacts = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertGetReport($cross_tracker_artifacts);
    }

    public function testGetReportWithoutArtifacts()
    {
        $response = $this->getResponse($this->request_factory->createRequest('GET', 'cross_tracker_reports/2/content?limit=50&offset=0'));

        self::assertEquals(200, $response->getStatusCode());
        $cross_tracker_artifacts = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(
            [],
            $cross_tracker_artifacts['artifacts']
        );
    }

    private function assertGetReport(array $cross_tracker_artifacts): void
    {
        self::assertEquals(
            5,
            count($cross_tracker_artifacts['artifacts'])
        );

        self::assertEquals($cross_tracker_artifacts['artifacts'][0]['id'], $this->epic_artifact_ids[7]);
        self::assertEquals($cross_tracker_artifacts['artifacts'][1]['id'], $this->epic_artifact_ids[6]);
        self::assertEquals($cross_tracker_artifacts['artifacts'][2]['id'], $this->epic_artifact_ids[5]);
        self::assertEquals($cross_tracker_artifacts['artifacts'][3]['id'], $this->epic_artifact_ids[4]);
        self::assertEquals($cross_tracker_artifacts['artifacts'][4]['id'], $this->epic_artifact_ids[1]);
    }
}
