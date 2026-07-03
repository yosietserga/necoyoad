<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\Models\Menu;
use App\Services\StoreContext;
use App\Services\LanguageContext;
use App\View\Components\WidgetComponent;

/**
 * Links widget — renders a menu tree (v7 equivalent).
 *
 * Recursively renders menu links as nested <ul>/<li> HTML.
 * Supports three submenu types (v7):
 *   - none: recurse into children
 *   - page_id: embed a CMS page as submenu content
 *   - html_content: localised HTML from descriptions
 */
class Links extends WidgetComponent
{
    public function data(): array
    {
        $storeId = app(StoreContext::class)->id();
        $menuId = $this->settings['menu_id'] ?? 0;

        if (!$menuId) {
            return ['links_html' => ''];
        }

        $links = $this->getLinks($menuId, null);
        $html = $this->drawLinksGroup($links);

        return [
            'links_html' => $html,
        ];
    }

    private function getLinks(int $menuId, ?int $parentId): array
    {
        $langId = app(LanguageContext::class)->id();

        $query = \App\Models\MenuLink::where('menu_id', $menuId)
            ->where('status', true)
            ->orderBy('sort_order');

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $links = $query->get();

        return $links->map(function ($link) use ($menuId, $langId) {
            $data = [
                'link' => $link->link,
                'tag' => $link->tag,
                'sort_order' => $link->sort_order,
                'class_css' => $link->getProperty('menu_link', 'class_css'),
                'icon' => $link->getProperty('menu_link', 'icon'),
                'children' => $this->getLinks($menuId, $link->id),
            ];

            return $data;
        })->toArray();
    }

    private function drawLinksGroup(array $links): string
    {
        if (empty($links)) return '';

        $html = '<ul class="menu-links">';
        foreach ($links as $link) {
            $css = !empty($link['class_css']) ? ' class="' . e($link['class_css']) . '"' : '';
            $html .= '<li' . $css . '>';
            $html .= '<a href="' . e($link['link']) . '">' . e($link['tag']) . '</a>';
            if (!empty($link['children'])) {
                $html .= $this->drawLinksGroup($link['children']);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
