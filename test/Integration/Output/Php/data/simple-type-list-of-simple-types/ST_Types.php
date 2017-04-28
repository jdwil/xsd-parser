<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\SimpleType\ST_Type;

class ST_Types
{
    /**
     * @var ST_Type
     */
    protected $items;

    /**
     * ST_Types constructor
     * @param ST_Type[] $items
     */
    public function __construct(ST_Type ...$items)
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
