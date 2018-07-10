<?php

namespace App\Util;


use App\Util\Interfaces\CartInterface;
use App\Util\Interfaces\CartItemInterface;

class Cart implements CartInterface
{

    /**
     * @var array
     */
    private $items = [];

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getCart(): array
    {
        return $this->items;
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        $total = 0;
        foreach($this->items as $item) {
            $total += $item['count']*$item['item']->getPrice();
        }
        return $total;
    }

    /**
     * @param CartItemInterface $item
     * @return bool
     */
    public function add(CartItemInterface $item): bool
    {
        $this->items[$item->getId()]['item'] = $item;
        $this->items[$item->getId()]['count'] = 1 + ($this->items[$item->getId()]['count'] ?? 0);
        return true;
    }

    /**
     * @param $itemId
     * @return bool
     */
    public function remove(int $itemId): bool
    {
        if (!isset($this->items[$itemId])) {
            return false;
        }

        if ($this->items[$itemId]['count'] > 1) {
            $this->items[$itemId]['count']--;
        } else {
            unset($this->items[$itemId]);
        }
        return true;
    }

}