<?php

/**
 * @BeforeMethods({"init"})
 */
class PipeMapFilterTakeBench
{
    public function __construct()
    {
        $this->pipe = \LazyLists\pipe(
            \LazyLists\map(static function($item) {
                return $item->number;
            }),
            \LazyLists\filter(static function($number) {
                return $number % 5 === 0;
            }),
            \LazyLists\take(50)
        );
    }
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
     * @Revs(400)
     * @Iterations(5)
     */
    public function benchNative()
    {
        $items = $this->items;
        $numbers = \array_map(static function($item) {
            return $item->number;
        }, $items);
        $divisibleBy5 = \array_filter($numbers, static function($number) {
            return $number % 5 === 0;
        });
        $first50 = \array_slice($divisibleBy5, 0, 50);
    }
    /**
     * @Revs(400)
     * @Iterations(5)
     */
    public function benchLazy()
    {
        $items = $this->items;
        $pipe = $this->pipe;
        $first50 = $pipe($items);
    }
}