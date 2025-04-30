<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

class MenuHelper
{
    public static function renderMenu(array $items): string
    {
        $html = '<ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">';
        foreach ($items as $item) {
            $html .= self::renderMenuItem($item);
        }
        $html .= '</ul>';
        return $html;
    }

    protected static function renderMenuItem(array $item): string
    {
        $hasChildren = isset($item['children']) && is_array($item['children']);
        $isRoute = $item['is_route'] ?? true;

        $url = $isRoute
            ? (isset($item['route']) ? route($item['route']) : '#')
            : (isset($item['route']) ? $item['route'] : '#');

        $isActive = self::isActive($item);

        $html = '<li class="' . ($hasChildren ? 'has-sub nav-item ' : 'nav-item ') . ($isActive ? 'active' : '') . '">';
        $html .= '<a href="' . $url . '">';

        // Adding the main icon if exists
        if (isset($item['icon'])) {
            $html .= '<img src="' . asset('assets/img/svg/' . $item['icon']) . '" alt="">';
        }

        // Menu title and the arrow icon
        $html .= '<span class="menu-title">' . __($item['title']) . '</span>';

        // Add arrow icon for submenus
        

        $html .= '</a>';

        // Render children items if they exist
        if ($hasChildren) {
            $html .= '<ul class="menu-content">';
            foreach ($item['children'] as $child) {
                $html .= self::renderMenuItem($child);
            }
            $html .= '</ul>';
        }

        $html .= '</li>';
        return $html;
    }


    protected static function isActive(array $item): bool
    {
        if (!isset($item['route'])) return false;

        if (($item['is_route'] ?? true) && Route::currentRouteName() === $item['route']) {
            return true;
        }

        if (!($item['is_route'] ?? true) && Request::is(ltrim($item['route'], '/'))) {
            return true;
        }

        if (isset($item['children']) && is_array($item['children'])) {
            foreach ($item['children'] as $child) {
                if (self::isActive($child)) return true;
            }
        }

        return false;
    }
}
