<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrderItem;
use App\Util\Cart;
use App\Util\Interfaces\CartInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    const PER_PAGE = 50;

    /**
     * @param Request $request
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/cart", name="cart")
     */
    public function index(Request $request, Session $session)
    {
        $cart = $this->getCart($session);
        $totalPrice = $cart->getTotalPrice();
        $cart = $cart->getCart();

        $paginator = $this->get('knp_paginator');
        $cart = $paginator->paginate(
            $cart,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render(
            'cart/index.html.twig',
            array(
                'cart_paginated' => $cart,
                'total_price' => $totalPrice,
            )
        );
    }

    /**
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/cart/order", name="cart_order")
     */
    public function cart_order(Session $session) {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('', []));
        }

        $cart = $this->getCart($session)->getCart();
        if (empty($cart)) {
            return $this->redirect($this->generateUrl('book', []));
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $orders = new Orders();
        $orders->setUserId($user->getId());
        $orders->setStatus(Orders::STATUS_NEW);

        $em = $this->getDoctrine()->getManager();
        $em->persist($orders);

        foreach($cart as $row) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($orders)
                        ->setName($row['item']->getName())
                        ->setPrice($row['item']->getPrice())
                        ->setCount($row['count']);
            $em->persist($orderItem);
        }

        $em->flush();

        $session->set('cart', NULL);
        return $this->redirect($this->generateUrl('book', []));
    }

    /**
     * @param $bookId
     * @param $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/cart/add/{bookId}/{page}", defaults={"page" = 1}, requirements={"bookId": "\d+"}, name="cart_add")
     */
    public function add($bookId, $page, Session $session)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('App:Book')->find($bookId);
        if (!$book) {
//            throw $this->createNotFoundException(
//                'No project work found for id '.$bookId
//            );
            return $this->redirect($this->generateUrl('book', array('page'=>$page)));
        }

        $cart = $this->getCart($session);
        $cart->add($book);
        $session->set('cart', $cart);

        return $this->redirect($this->generateUrl('book', array('page'=>$page)));
    }

    /**
     * @param Session $session
     * @return Cart
     */
    private function getCart(Session $session)
    {
        $cart = $session->get('cart');
        if (!($cart instanceof CartInterface)) {
            $cart = new Cart();
        }
        return $cart;
    }

    /**
     *
     * @Route("/cart/remove/{bookId}/{page}", defaults={"page" = 1}, requirements={"bookId": "\d+"}, name="cart_remove")
     */
    public function remove($bookId, $page, Session $session)
    {
        $cart = $this->getCart($session);
        $cart->remove($bookId);
        $session->set('cart', $cart);

        return $this->redirect($this->generateUrl('cart', array('page'=>$page)));
    }
}
