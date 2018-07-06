<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBookData implements FixtureInterface, ContainerAwareInterface
{

    private $container;

    public function load(ObjectManager $manager)
    {
        for($i = 1; $i < 100; $i++) {
            $book = new Book();
            $book->setName('Book'.$i);
            $book->setPrice((10.00 + $i/100));
            $book->setDate(new \DateTime());

            $manager->persist($book);
        }
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}