<?php
/**
 * Created by PhpStorm.
 * User: vitalij
 * Date: 18.10.7
 * Time: 12:09
 */

namespace App\Util\Interfaces;


interface CartInterface
{
    public function getCart(): array;
    public function add(CartItemInterface $item): bool;
    public function remove(int $itemId): bool;

}