<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;

class ST_Ints implements SimpleTypeInterface
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
