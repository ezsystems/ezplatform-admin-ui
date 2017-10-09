<?php

namespace EzSystems\EzPlatformAdminUi\Menu;

class Provider
{
    protected $registry;

    /** @var Menu */
    private $current;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function setCurrent($current)
    {
        $this->current = $this->registry->getMenu($current);
    }

    public function getItems(): array
    {
        return $this->sortItems($this->current->getItems());
    }

    /**
     * @param Item[] $items
     *
     * @return array
     */
    protected function sortItems(array $items): array
    {
        // TODO: sort children
//        foreach ($items as &$item) {
//            if (!empty($item->getItems())) {
//                $item = $this->sortItems($item->getItems());
//            }
//        }

        uasort($items, function (Item $a, Item $b) {
            return $a->getPriority() <=> $b->getPriority();
        });


        return $items;
    }
}
