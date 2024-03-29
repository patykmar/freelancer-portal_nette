<?php

/**
 * Navigation
 *
 * @author Jan Marek
 * @license MIT
 */

namespace App\Components\Navigation;

use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;

class Navigation extends Control
{
    /** @var NavigationNode */
    private $homepage;

    /** @var NavigationNode */
    private $current;

    /** @var bool */
    private $useHomepage = false;

    /** @var string */
    private $menuTemplate;

    /** @var string */
    private $breadcrumbsTemplate;

    /**
     * Set node as current
     * @param NavigationNode $node
     */
    public function setCurrentNode(NavigationNode $node)
    {
        if (isset($this->current)) {
            $this->current->isCurrent = false;
        }
        $node->isCurrent = true;
        $this->current = $node;
    }

    /**
     * Add navigation node as a child
     * @param string $label
     * @param string $url
     * @return NavigationNode
     */
    public function add($label, $url)
    {
        return $this->getComponent('homepage')->add($label, $url);
    }

    /**
     * Setup homepage
     * @param string $label
     * @param string $url
     * @return IComponent
     */
    public function setupHomepage($label, $url)
    {
        $homepage = $this->getComponent('homepage');
        $homepage->label = $label;
        $homepage->url = $url;
        $this->useHomepage = true;
        return $homepage;
    }

    /**
     * Homepage factory
     * @param string $name
     */
    protected function createComponentHomepage($name)
    {
        new NavigationNode($this, $name);
    }

    /**
     * Render menu
     * @param bool $renderChildren
     * @param NavigationNode $base
     * @param bool $renderHomepage
     */
    public function renderMenu($renderChildren = TRUE, $base = null, $renderHomepage = TRUE)
    {
        $template = $this->createTemplate()
            ->setFile($this->menuTemplate ?: __DIR__ . '/menu.latte');
        $template->homepage = $base ? $base : $this->getComponent('homepage');
        $template->useHomepage = $this->useHomepage && $renderHomepage;
        $template->renderChildren = $renderChildren;
        $template->children = $this->getComponent('homepage')->getComponents();
        $template->render();
    }

    /**
     * Render full menu
     */
    public function render()
    {
        $this->renderMenu();
    }

    /**
     * Render main menu
     */
    public function renderMainMenu()
    {
        $this->renderMenu(false);
    }

    /**
     * Render breadcrumbs
     */
    public function renderBreadcrumbs()
    {
        if (empty($this->current)) {
            return;
        }

        $items = array();
        $node = $this->current;

        while ($node instanceof NavigationNode) {
            $parent = $node->getParent();
            if (!$this->useHomepage && !($parent instanceof NavigationNode)) {
                break;
            }

            array_unshift($items, $node);
            $node = $parent;
        }

        $template = $this->createTemplate()
            ->setFile($this->breadcrumbsTemplate ?: __DIR__ . '/breadcrumbs.latte');

        $template->items = $items;
        $template->render();
    }

    /**
     * @param string $breadcrumbsTemplate
     */
    public function setBreadcrumbsTemplate($breadcrumbsTemplate)
    {
        $this->breadcrumbsTemplate = $breadcrumbsTemplate;
    }

    /**
     * @param string $menuTemplate
     */
    public function setMenuTemplate($menuTemplate)
    {
        $this->menuTemplate = $menuTemplate;
    }

    /**
     * @return NavigationNode
     */
    public function getCurrentNode()
    {
        return $this->current;
    }
}
