<?php

/**
 * @BeforeMethods({"init"})
 */
class PipeFilterMapReduceBench
{
    public function __construct()
    {
        $this->pipe = \LazyLists\pipe(
            \LazyLists\filter(static function($item) {
                return $item->number % 5 === 0;
            }),
            \LazyLists\map(static function($item) {
                return $item->number;
            }),
            \LazyLists\reduce(static function($sum, $number) {
                return $sum + $number;
            }, 0)
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
        $divisibleBy5 = \array_filter($items, static function($item) {
            return $item->number % 5 === 0;
        });
        $numbers = \array_map(static function($item) {
            return $item->number;
        }, $divisibleBy5);
        $sum = \array_reduce($numbers, static function($sum, $number) {
            return $sum + $number;
        }, 0);
    }
    /**
     * @Revs(400)
     * @Iterations(5)
     */
    public function benchLazy()
    {
        $items = $this->items;
        $pipe = $this->pipe;
        $sum = $pipe($items);
    }
}