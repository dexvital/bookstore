<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\BookType;
use App\Entity\Book;
use Symfony\Component\Form\FormError;


class BookController extends Controller
{
    const PER_PAGE = 50;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="book")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT b 
                                      FROM App:Book b
                                      ORDER BY b.id DESC');
        $books = $query->getResult();

        $paginator = $this->get('knp_paginator');
        $books = $paginator->paginate(
            $books,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render(
            'book/index.html.twig',
            array(
                'books_paginated' => $books,
            )
        );
    }

    /**
     * @Route("/book/edit/{bookId}/{page}", defaults={"bookId" = 0, "page" = 1}, requirements={"bookId": "\d+"}, name="book_edit")
     */
    public function edit($bookId, $page, Request $request)
    {
        if (empty($bookId)) {
            $postData = $request->get('book');
            if (isset($postData['id'])) {
                $bookId = intval($postData['id']);
            }
        }

        if (empty($bookId)) {
            $book = new Book();
        } else {
            $book = $this->getDoctrine()
                ->getRepository('App:Book')
                ->find($bookId);
            if (empty($book)){
                throw $this->createNotFoundException(
                    'No book work found for id '.$bookId
                );
            }
        }

        $bookForm = $this->createForm(BookType::class, $book);
        $bookForm->get('page')->setData($page);

        $bookForm->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $book->setDate(new \DateTime());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            try {
                $em->flush();
                return $this->redirect($this->generateUrl('book', array('page'=>$bookForm->get('page')->getData())));
            } catch (\Exception $e) {

            }
        }

        return $this->render(
            'book/edit.html.twig',
            array(
                'book_form' => $bookForm->createView()
            )
        );
    }

    /**
     * @Route("/book/delete/{bookId}/{page}", defaults={"page" = 1}, requirements={"bookId": "\d+"}, name="book_delete")
     */
    public function delete($bookId, $page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('App:Book')->find($bookId);
        if (!$book) {
            throw $this->createNotFoundException(
                'No project work found for id '.$bookId
            );
        }

        $em->remove($book);
        $em->flush();
        return $this->redirect($this->generateUrl('book', array('page'=>$page)));
    }
}