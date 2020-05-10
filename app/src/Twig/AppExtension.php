<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Ramsey\Uuid\UuidInterface;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

final class AppExtension extends AbstractExtension
{
    private bool $debug = false;
    private MenuRepository $menuRepository;
    private Environment $twig;

    /**
     * Default constructor
     */
    public function __construct(Environment $twig, MenuRepository $menuRepository, bool $debug = false)
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
            new TwigFunction('app_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new TwigFunction('app_menu_of', [$this, 'renderMenuOf'], ['is_safe' => ['html']]),
            new TwigFunction('app_load_menu', [$this, 'loadMenu'], ['is_safe' => ['html']]),
            new TwigFunction('app_load_menu_top', [$this, 'loadMenuTop'], ['is_safe' => ['html']]),
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
     * Renders the menu with only the current trail opened.
     */
    public function renderMenuOf(?UuidInterface $pageId = null)
    {
        return $this->renderPartial('partial/menu.html.twig', 'menu', [
            'menu' => $this->loadMenu(),
            'current' => $pageId,
            'only_active' => true,
        ]);
    }

    /**
     * Render menu
     */
    public function renderMenu($menu = null, ?UuidInterface $pageId = null): string
    {
        if (null === $menu) {
            $menu = $this->loadMenu();
        } else if (!$menu instanceof $menu) {
            throw new \InvalidArgumentException("Not a menu");
        }

        return $this->renderPartial('partial/menu.html.twig', 'menu', [
            'current' => $pageId,
            'menu' => $menu,
        ]);
    }
}
