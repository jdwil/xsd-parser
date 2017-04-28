<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

class ST_Ints
{
    /**
     * @var int[]
     */
    protected $items;

    /**
     * ST_Ints constructor
     * @param int[] $items
     */
    public function __construct(int ...$items)
    {
        $this->items = $items;
    }

    /**
     * @returns array
     */
    public function all(): array
    {
        return $this->items;
    }
}
