<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/books', name: 'list_books')]
    public function list(BookRepository $Repo): Response
    {

        $booksNoPublished=$Repo->findBy(['published' => false]);
        $books=$Repo->findBy(['published' => true]);
        $nbBooksPublished= count($books);
        $nbBooksNoPublished=count($booksNoPublished);
        
        return $this->render('book/list.html.twig', [
            'books' => $books,'nbBooksPublished'=>$nbBooksPublished,'nbBooksNoPublished'=>$nbBooksNoPublished
        ]);
    }
    #[Route('/allbooks', name: 'all_list_books')]
    public function allList(BookRepository $repo): Response
    {

        $booksNoPublished=$repo->findBy(['published' => false]);
        $booksPublished=$repo->findBy(['published' => true]);
        $allBooks=$repo->findAll();
        $nbBooksPublished= count($booksPublished);
        $nbBooksNoPublished=count($booksNoPublished);
        
        return $this->render('book/listOfAllBooks.html.twig', [
            'books' => $allBooks,'nbBooksPublished'=>$nbBooksPublished,'nbBooksNoPublished'=>$nbBooksNoPublished
        ]);
    }
    #[Route('/books/create', name: 'create_book')]
    public function add(Request $request,EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setPublished(true);
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author=$book->getAuthor();
            $author->setNbBooks($author->getNbBooks()+1);
            $em->persist($book,$author);
            $em->flush();

            return $this->redirectToRoute('list_books');
        }

        return $this->render('book/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('books/update/{ref}', name: 'update_book')]
    public function update(Request $request, BookRepository $repo,$ref,EntityManagerInterface $em): Response
    {
        
        
        $book = $repo->find($ref);
        $author=$book->getAuthor();
        $author->setNbBooks($author->getNbBooks()-1);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $author=$book->getAuthor();
            $author->setNbBooks($author->getNbBooks()+1);
            $em->flush();
            return $this->redirectToRoute('list_books');
        }

        return $this->render('book/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('book/delete/{ref}', name: 'delete_book')]
    public function delete(BookRepository $repo,$ref,EntityManagerInterface $em): RedirectResponse
    {
        
        $book = $repo->find($ref);
        $author=$book->getAuthor();
        
        $em->remove($book);
        $author->setNbBooks($author->getNbBooks()-1);
        $em->flush();
            return $this->redirectToRoute('list_books');
    }
    #[Route('book/show/{ref}', name: 'show_book')]
    public function showBook($ref, BookRepository $repo):Response
    {
        $book=$repo->find($ref);
        
        return $this->render('book/showBook.html.twig', [
           'book'=>$book ,
        ]);
    }
}
