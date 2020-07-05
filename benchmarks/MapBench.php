<?php

/**
 * @BeforeMethods({"init"})
 */
class MapBench
{
    public function init()
    {
        $this->items = self::getItems();
    }

    private static function getItems()
    {
        $numberOfItems = 10000;
        $items = [];
        for($i = 0; $i < $numberOfItems; $i++) {
            $object = new stdClass;
            $object->number = $i;
            $items[] = $object;
        }
        return $items;
    }
    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchNative()
    {
        $items = $this->items;
        $numbers = \array_map(static function($item) {
            return $item->number;
        }, $items);
    }
    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchLazy()
    {
        $items = $this->items;
        $numbers = \LazyLists\map(static function($item) {
            return $item->number;
        }, $items);
    }
}