<?php
/**
 * Copyright (c) Enalean, 2022-Present. All Rights Reserved.
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

namespace Tuleap\Tracker\Artifact\Changeset\Comment;

use Tuleap\Test\Builders\ProjectTestBuilder;
use Tuleap\Test\Builders\ProjectUGroupTestBuilder;
use Tuleap\Test\Builders\UserTestBuilder;
use Tuleap\Tracker\Artifact\Changeset\Comment\PrivateComment\TrackerPrivateCommentUGroupPermissionInserter;
use Tuleap\Tracker\FormElement\Field\File\CreatedFileURLMapping;
use Tuleap\Tracker\FormElement\Field\Text\TextValueValidator;
use Tuleap\Tracker\Test\Builders\ArtifactTestBuilder;
use Tuleap\Tracker\Test\Builders\TrackerTestBuilder;

#[\PHPUnit\Framework\Attributes\DisableReturnValueGenerationForTestDoubles]
final class CommentCreatorTest extends \Tuleap\Test\PHPUnit\TestCase
{
    private const CHANGESET_ID         = 3338;
    private const SUBMISSION_TIMESTAMP = 1417430951;
    private const SUBMITTER_USER_ID    = 156;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & \Tracker_Artifact_Changeset_CommentDao
     */
    private $dao;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & \ReferenceManager
     */
    private $reference_manager;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & TrackerPrivateCommentUGroupPermissionInserter
     */
    private $ugroup_inserter;
    /**
     * @var ChangesetCommentIndexer&\PHPUnit\Framework\MockObject\Stub
     */
    private $changeset_comment_indexer;
    /**
     * @var \ProjectUGroup[]
     */
    private array $user_groups_that_are_allowed_to_see;

    #[\Override]
    protected function setUp(): void
    {
        $this->dao                       = $this->createMock(\Tracker_Artifact_Changeset_CommentDao::class);
        $this->reference_manager         = $this->createMock(\ReferenceManager::class);
        $this->ugroup_inserter           = $this->createMock(TrackerPrivateCommentUGroupPermissionInserter::class);
        $this->changeset_comment_indexer = $this->createStub(ChangesetCommentIndexer::class);

        $this->user_groups_that_are_allowed_to_see = [];
    }

    /**
     * @throws \Tracker_CommentNotStoredException
     */
    private function create(CommentCreation $comment): void
    {
        $project  = ProjectTestBuilder::aProject()->withId(123)->build();
        $tracker  = TrackerTestBuilder::aTracker()
            ->withId(9)
            ->withProject($project)
            ->withShortName('chondrite')
            ->build();
        $artifact = ArtifactTestBuilder::anArtifact(78)
            ->inTracker($tracker)
            ->build();

        $creator = new CommentCreator(
            $this->dao,
            $this->reference_manager,
            $this->ugroup_inserter,
            new TextValueValidator(),
        );

        $this->changeset_comment_indexer->method('indexNewChangesetComment');

        $creator->createComment($artifact, $comment);
    }

    public function createWithTooBigComment(): void
    {
        $submitter = UserTestBuilder::aUser()->withId(self::SUBMITTER_USER_ID)->build();
        $comment   = CommentCreation::fromNewComment(
            NewComment::fromParts(
                str_repeat('a', 70000),
                CommentFormatIdentifier::TEXT,
                $submitter,
                self::SUBMISSION_TIMESTAMP,
                $this->user_groups_that_are_allowed_to_see
            ),
            self::CHANGESET_ID,
            new CreatedFileURLMapping()
        );

        $this->changeset_comment_indexer->method('indexNewChangesetComment');

        $this->create($comment);
    }

    public function createWithComment(): void
    {
        $submitter = UserTestBuilder::aUser()->withId(self::SUBMITTER_USER_ID)->build();
        $comment   = CommentCreation::fromNewComment(
            NewComment::fromParts(
                'metavoltine huggermugger',
                CommentFormatIdentifier::TEXT,
                $submitter,
                self::SUBMISSION_TIMESTAMP,
                $this->user_groups_that_are_allowed_to_see
            ),
            self::CHANGESET_ID,
            new CreatedFileURLMapping()
        );

        $this->changeset_comment_indexer->method('indexNewChangesetComment');

        $this->create($comment);
    }

    public static function generateComments(): iterable
    {
        $submitter = UserTestBuilder::aUser()->withId(self::SUBMITTER_USER_ID)->build();
        yield 'Text comment' => [CommentCreation::fromNewComment(
            NewComment::fromParts(
                'metavoltine huggermugger',
                CommentFormatIdentifier::TEXT,
                $submitter,
                self::SUBMISSION_TIMESTAMP,
                []
            ),
            self::CHANGESET_ID,
            new CreatedFileURLMapping()
        ),
        ];
        yield 'HTML comment' => [CommentCreation::fromNewComment(
            NewComment::fromParts(
                '<p>wane demipomada</p>',
                CommentFormatIdentifier::HTML,
                $submitter,
                self::SUBMISSION_TIMESTAMP,
                []
            ),
            self::CHANGESET_ID,
            new CreatedFileURLMapping()
        ),
        ];
        yield 'Markdown comment' => [CommentCreation::fromNewComment(
            NewComment::fromParts(
                '*appraising* wheedle',
                CommentFormatIdentifier::COMMONMARK,
                $submitter,
                self::SUBMISSION_TIMESTAMP,
                []
            ),
            self::CHANGESET_ID,
            new CreatedFileURLMapping()
        ),
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('generateComments')]
    public function testItSavesCommentAndExtractsCrossReferences(CommentCreation $comment): void
    {
        $this->dao->expects($this->once())
            ->method('createNewVersion')
            ->with(
                self::CHANGESET_ID,
                $comment->getBody(),
                self::SUBMITTER_USER_ID,
                self::SUBMISSION_TIMESTAMP,
                0,
                $comment->getFormat()->value
            )->willReturn(6903);
        $this->reference_manager->expects($this->once())->method('extractCrossRef');
        $this->ugroup_inserter->method('insertUGroupsOnPrivateComment');

        $this->create($comment);
    }

    public function testItThrowsIfThereIsAProblemWhenSavingTheComment(): void
    {
        $this->expectException(\Tracker_CommentNotStoredException::class);
        $this->dao->method('createNewVersion')->willReturn(false);

        $this->createWithComment();
    }

    public function testItThrowsIfTheCommentContentIsNotValid(): void
    {
        $this->expectException(CommentContentNotValidException::class);

        $this->createWithTooBigComment();
    }

    public function testItSavesUserGroupsAllowedToSeePrivateComment(): void
    {
        $this->user_groups_that_are_allowed_to_see = [
            ProjectUGroupTestBuilder::aCustomUserGroup(121)->build(),
            ProjectUGroupTestBuilder::aCustomUserGroup(181)->build(),
        ];

        $comment_id = 7905;
        $this->dao->method('createNewVersion')->willReturn($comment_id);
        $this->reference_manager->method('extractCrossRef');
        $this->ugroup_inserter->expects($this->once())
            ->method('insertUGroupsOnPrivateComment')
            ->with($comment_id, $this->user_groups_that_are_allowed_to_see);

        $this->createWithComment();
    }
}
