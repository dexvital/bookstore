<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrderItem;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    const PER_PAGE = 50;

    /**
     * @Route("/cart", name="cart")
     */
    public function index(Request $request, Session $session)
    {
        $cart = $session->get('cart');

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
            )
        );
    }

    /**
     * @Route("/cart/order", name="cart_order")
     */
    public function cart_order(Session $session) {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('', []));
        }

        $cart = $session->get('cart');
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
     * @param Request $request
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

        $cart = $session->get('cart');

        $cart[$bookId]['item'] = $book;
        $cart[$bookId]['count'] = 1 + ($cart[$bookId]['count'] ?? 0);

        $session->set('cart', $cart);

        return $this->redirect($this->generateUrl('book', array('page'=>$page)));
    }

    /**
     *
     * @Route("/cart/remove/{bookId}/{page}", defaults={"page" = 1}, requirements={"bookId": "\d+"}, name="cart_remove")
     */
    public function remove($bookId, $page, Session $session)
    {
        $cart = $session->get('cart');

        unset($cart[$bookId]);

        $session->set('cart', $cart);

        return $this->redirect($this->generateUrl('cart', array('page'=>$page)));
    }
}
