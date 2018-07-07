<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index()
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    /**
     * @param $bookId
     * @param $page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/cart/add/{bookId}/{page}", defaults={"page" = 1}, requirements={"bookId": "\d+"}, name="cart_add")
     */
    public function add($bookId, $page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('App:Book')->find($bookId);
        if (!$book) {
            throw $this->createNotFoundException(
                'No project work found for id '.$bookId
            );
            return $this->redirect($this->generateUrl('book', array('page'=>$page)));
        }

        $em->flush();
        return $this->redirect($this->generateUrl('book', array('page'=>$page)));
    }
}
