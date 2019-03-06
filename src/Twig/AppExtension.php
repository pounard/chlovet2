<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Twig\Environment;

final class AppExtension extends \Twig_Extension
{
    private $debug = false;
    private $menuRepository;
    private $twig;

    /**
     * Default constructor
     */
    public function __construct(Environment $twig, MenuRepository $menuRepository, ?bool $debug)
    {
        $this->debug = $debug;
        $this->menuRepository = $menuRepository;
        $this->twig = $twig;
    }

    /**
     * Render a single block of this page
     */
    private function renderPartial(string $template, string $name, array $context = []): string
    {
        return $this->twig->load($template)->renderBlock($name, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('app_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('app_load_menu', [$this, 'loadMenu'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('app_load_menu_top', [$this, 'loadMenuTop'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Moad complete menu
     */
    public function loadMenu(): Menu
    {
        return $this->menuRepository->loadTree();
    }

    /**
     * Load only top-level items of menu
     */
    public function loadMenuTop(): Menu
    {
        return $this->menuRepository->loadTree(1);
    }

    /**
     * Render menu
     */
    public function renderMenu($menu = null): string
    {
        if (null === $menu) {
            $menu = $this->loadMenu();
        } else if (!$menu instanceof $menu) {
            throw new \InvalidArgumentException("Not a menu");
        }

        return $this->renderPartial('partial/menu.html.twig', 'menu', ['menu' => $menu]);
    }
}
