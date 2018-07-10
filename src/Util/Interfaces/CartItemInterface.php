<?php
/**
 * Created by PhpStorm.
 * User: vitalij
 * Date: 18.10.7
 * Time: 12:16
 */

namespace App\Util\Interfaces;


interface CartItemInterface
{
    public function getId();
    public function getPrice();
}