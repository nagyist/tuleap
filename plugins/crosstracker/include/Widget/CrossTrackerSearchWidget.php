<?php
/**
 * Copyright (c) Enalean, 2017-Present. All Rights Reserved.
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

namespace Tuleap\CrossTracker\Widget;

use Codendi_Request;
use HTTPRequest;
use LogicException;
use Project;
use SimpleXMLElement;
use TemplateRendererFactory;
use Tuleap\Config\ConfigKeyInt;
use Tuleap\Config\FeatureFlagConfigKey;
use Tuleap\Layout\CssAssetCollection;
use Tuleap\Layout\CssAssetWithoutVariantDeclinaisons;
use Tuleap\Layout\IncludeCoreAssets;
use Tuleap\Layout\IncludeViteAssets;
use Tuleap\Layout\JavascriptAsset;
use Tuleap\Layout\JavascriptViteAsset;
use Tuleap\Project\MappingRegistry;
use Widget;
use function Psl\Json\encode;

class CrossTrackerSearchWidget extends Widget
{
    public const NAME = 'crosstrackersearch';

    #[FeatureFlagConfigKey('Support artifact links display in Cross Tracker widget')]
    #[ConfigKeyInt(0)]
    public const FEATURE_FLAG = 'cross_tracker_widget_display_artifact_link';

    public function __construct(
        private readonly WidgetInheritanceHandler $inheritance_handler,
        private readonly WidgetPermissionChecker $permission_checker,
        private readonly SearchCrossTrackerWidget $search_cross_tracker_widget,
        private readonly WidgetCrossTrackerWidgetXMLExporter $widget_XML_exporter,
        private readonly CrossTrackerWidgetCreator $cross_tracker_widget_creator,
    ) {
        parent::__construct(self::NAME);
    }

    public function loadContent($id): void
    {
        $this->content_id = $id;
    }

    public function getContent(): string
    {
        $renderer = TemplateRendererFactory::build()->getRenderer(
            __DIR__ . '/../../templates/widgets'
        );

        $request = HTTPRequest::instance();
        $user    = $request->getCurrentUser();

        $is_admin = $this->permission_checker->isUserWidgetAdmin($user, $this->content_id);

        $row = $this->search_cross_tracker_widget->searchCrossTrackerWidgetDashboardById($this->content_id);
        if ($row === null) {
            throw new LogicException(sprintf('Widget #%d could not be found.', $this->content_id));
        }

        return $renderer->renderToString(
            'cross-tracker-search-widget',
            [
                'widget_json_data' => encode(new CrossTrackerSearchWidgetPresenter(
                    $this->content_id,
                    $is_admin,
                    $user,
                    $row['dashboard_type'],
                    $this->getTitleAttributeValue(),
                    $this->getTitle(),
                )),
            ]
        );
    }

    public function getDescription(): string
    {
        return dgettext('tuleap-crosstracker', 'Search into multiple trackers and multiple projects.');
    }

    public function getIcon(): string
    {
        return 'fa-list-ul';
    }

    public function getTitle(): string
    {
        return dgettext('tuleap-crosstracker', 'Cross trackers search');
    }

    public function getCategory(): string
    {
        return dgettext('tuleap-tracker', 'Trackers');
    }

    public function isUnique(): bool
    {
        return false;
    }

    public function create(Codendi_Request $request): int|false
    {
            return $this->cross_tracker_widget_creator->createWithQueries($request)->match(
                static fn(int $widget_id) => $widget_id,
                static fn() => false
            );
    }

    public function destroy($id): void
    {
        $this->getWidgetDao()->deleteWidget($id);
    }

    public function cloneContent(
        Project $template_project,
        Project $new_project,
        $id,
        $owner_id,
        $owner_type,
        MappingRegistry $mapping_registry,
    ): int {
        return $this->inheritance_handler->handle($id);
    }

    public function getJavascriptAssets(): array
    {
        return [
            new JavascriptViteAsset($this->getAssets(), 'src/index.ts'),
            new JavascriptAsset(new IncludeCoreAssets(), 'syntax-highlight.js'),
        ];
    }

    public function getStylesheetDependencies(): CssAssetCollection
    {
        return new CssAssetCollection([
            new CssAssetWithoutVariantDeclinaisons(new IncludeCoreAssets(), 'syntax-highlight'),
        ]);
    }

    private function getAssets(): IncludeViteAssets
    {
        return new IncludeViteAssets(
            __DIR__ . '/../../scripts/cross-tracker/frontend-assets',
            '/assets/crosstracker/cross-tracker'
        );
    }

    private function getWidgetDao(): CrossTrackerWidgetDao
    {
        return new CrossTrackerWidgetDao();
    }

    public function isManagingItsOwnSection(): bool
    {
        return true;
    }

    public function hasCustomTitle(): bool
    {
        return true;
    }

    public function getPurifiedCustomTitle(): string
    {
        $renderer = TemplateRendererFactory::build()->getRenderer(
            __DIR__ . '/../../templates/widgets'
        );

        return $renderer->renderToString(
            'widget-title',
            ['title_attribute' => $this->getTitleAttributeValue()],
        );
    }

    public function exportAsXML(): ?SimpleXMLElement
    {
        return $this->widget_XML_exporter->generateXML($this->content_id)->match(
            static fn(SimpleXMLElement $widget) => $widget,
            static fn() => null
        );
    }

    private function getTitleAttributeValue(): string
    {
        return "crosstracker-$this->content_id";
    }
}
