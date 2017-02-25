<?php

/*
 * This file is part of the She Interlang package
 *
 * Copyright (c) 2017 Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@devtia.com>
 */

namespace Desarrolla2\MenuBundle\Twig;

use Desarrolla2\MenuBundle\Menu\MenuInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuExtension extends \Twig_Extension
{
    /** @var \Twig_Environment */
    private $twig;

    /** @var Router */
    private $router;

    /** @var string */
    private $route = false;

    private $selected = false;

    /**
     * @param Router $router
     */
    public function __construct(\Twig_Environment $twig, Router $router, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->router = $router;
        $request = $requestStack->getCurrentRequest();
        if ($request) {
            $this->route = $request->get('_route');
        }
    }

    public function render($class, $template)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf('Class "%s" not exist', $class)
            );
        }

        $builder = new $class();

        if (!$builder instanceof MenuInterface) {
            throw new \InvalidArgumentException(
                sprintf('Class "%s" not implements MenuInterface', $class)
            );
        }

        $menu = $builder->getMenu();
        $menu = $this->prepareMenu($menu);

        return $this->twig->render(
            sprintf('MenuBundle:Menu:%s.html.twig', $template),
            ['menu' => $menu]
        );
    }

    /**
     * @param array $menu
     *
     * @return array
     */
    protected function prepareMenu($menu)
    {
        $this->selected = false;
        $required = ['class', 'items'];
        foreach ($required as $r) {
            if (!isset($menu[$r])) {
                $menu[$r] = false;
            }
        }

        $arrays = ['items'];
        foreach ($arrays as $a) {
            if (!is_array($menu[$a])) {
                $menu[$a] = [];
            }
        }

        foreach ($menu['items'] as $i => $j) {
            $menu['items'][$i] = $this->prepareItem($j);
            if (count($menu['items'][$i]['items'])) {
                foreach ($menu['items'][$i]['items'] as $x => $y) {
                    $menu['items'][$i]['items'][$x] = $this->prepareItem($y);
                    if ($menu['items'][$i]['items'][$x]['selected']) {
                        $menu['items'][$i]['selected'] = true;
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    protected function prepareItem($item)
    {
        $required = ['class', 'anchorClass', 'route', 'icon', 'name', 'items', 'active', 'credentials'];
        foreach ($required as $r) {
            if (!isset($item[$r])) {
                $item[$r] = false;
            }
        }

        $arrays = ['items', 'active'];
        foreach ($arrays as $a) {
            if (!is_array($item[$a])) {
                $item[$a] = [];
            }
        }

        $item['link'] = '#';
        if ($item['route']) {
            $item['link'] = $this->router->generate($item['route']);
        }

        $item['selected'] = $this->isSelected($item);

        return $item;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    private function isSelected($item)
    {
        if ($this->selected) {
            return false;
        }

        if ($this->route == $item['route']) {
            $this->selected = true;

            return true;
        }
        if (in_array($this->route, $item['active'])) {
            $this->selected = true;

            return true;
        }
        foreach ($item['active'] as $active) {
            if (preg_match(sprintf('#%s#', $active), $this->route) === 1) {
                $this->selected = true;

                return true;
            }
        }

        return false;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'renderMenu', [$this, 'render'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_twig_menu_extension';
    }
}