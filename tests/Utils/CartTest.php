<?php
namespace App\Tests\Utils;

use App\Entity\Book;
use App\Util\Cart;
use PHPUnit\Framework\TestCase;


class CartTest extends TestCase
{

    public function testTotalPrice()
    {
        $book = new Book();
        $book->setId(3);
        $book->setName('Test');
        $book->setPrice(10);

        $cart = new Cart();
        $cart->add($book);
        $cart->add($book);

        $result = $cart->getTotalPrice();

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(20, $result);
    }
}