<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\DateAndNbreBooksType;
use App\Form\IdSearchType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    //index
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    //Read//
    //show book by reference
    #[Route('book/show/{ref}', name: 'books_by_ref')]
    public function showBook($ref, BookRepository $repo): Response
    {

        $book = $repo->find($ref);
        return $this->render('book/showBook.html.twig', [
            'book' => $book,
        ]);
    }

    //show books between to date && category ScienceFiction
    #[Route('/bookbydate', name: 'books_by_date_&&_science_fiction')]
    public function listByDateAndScienceFiction(BookRepository $repo, Request $request): Response
    {
        $booksNoPublished = $repo->findBy(['published' => false]);
        $booksPublished = $repo->findBy(['published' => true]);
        $nbBooksPublished = count($booksPublished);
        $nbBooksNoPublished = count($booksNoPublished);

        $form = $this->createForm(IdSearchType::class);
        $form->handleRequest($request);
        $form2 = $this->createForm(DateAndNbreBooksType::class);
        $form2->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $ref = $form->getData();
            $books = $repo->findByRef($ref);
            return $this->render('book/listOfAllBooks.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
            ]);
        }
        if ($form2->isSubmitted() && $form2->isValid()) {
            $date = $form2->get('publicationDate')->getData();
            $nbreOfBooks = $form2->get('nbreOfBooks')->getData();
            $books = $repo->findAllByPublicationDateAndNbBooks($date, $nbreOfBooks);

            return $this->render('book/listOfAllBooks.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,

            ]);
        }
        
        $firstDate = new \DateTime('2014-01-01');
        $secDate = new \DateTime('2018-12-31');
        $allBooks = $repo->findByTwoDate($firstDate, $secDate);
        $category = $repo->findSumBooksWithScienceFiction('Science-Fiction');

        return $this->render('book/listOfAllBooks.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'books' => $allBooks,
            'nbBooksPublished' => $nbBooksPublished,
            'nbBooksNoPublished' => $nbBooksNoPublished,
            'nbBooksWithSpecifiqueCategory' => $category,
        ]);
    }

    //show All books By Author's Username
    #[Route('/allbooks_obau', name: 'allbooks_odred_by_authour_username')]
    public function allBooksOrdredByAuthorsUsername(BookRepository $repo, Request $request): Response
    {
        $booksNoPublished = $repo->findBy(['published' => false]);
        $booksPublished = $repo->findBy(['published' => true]);
        $nbBooksPublished = count($booksPublished);
        $nbBooksNoPublished = count($booksNoPublished);
        $form = $this->createForm(IdSearchType::class);
        $form->handleRequest($request);

        $form2 = $this->createForm(DateAndNbreBooksType::class);
        $form2->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ref = $form->getData();
            $books = $repo->findByRef($ref);
            return $this->render('book/listOfAllBooks.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
            ]);
        }
        if ($form2->isSubmitted() && $form2->isValid()) {
            $date = $form2->get('publicationDate')->getData();
            $nbreOfBooks = $form2->get('nbreOfBooks')->getData();
            $books = $repo->findAllByPublicationDateAndNbBooks($date, $nbreOfBooks);

            return $this->render('book/listOfAllBooks.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
            ]);
        }
        $allBooks = $repo->findAllByAuthorUsername();
        return $this->render('book/listOfAllBooks.html.twig', [
            'form' => $form->createView(),
            'books' => $allBooks,
            'nbBooksPublished' => $nbBooksPublished,
            'nbBooksNoPublished' => $nbBooksNoPublished,

        ]);
    }

    //show published books
    #[Route('/books', name: 'published_books')]
    public function publishedBooks(BookRepository $repo, Request $request): Response
    {
        $booksNoPublished = $repo->findBy(['published' => false]);
        $books = $repo->findBy(['published' => true]);
        $nbBooksPublished = count($books);
        $nbBooksNoPublished = count($booksNoPublished);
        $form = $this->createForm(IdSearchType::class);
        $form->handleRequest($request);
        $form2 = $this->createForm(DateAndNbreBooksType::class);
        $form2->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ref = $form->getData();
            $books = $repo->findBy($ref);
            return $this->render('book/list.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
            ]);
        }
        if ($form2->isSubmitted() && $form2->isValid()) {
            $date = $form2->get('publicationDate')->getData();
            $nbreOfBooks = $form2->get('nbreOfBooks')->getData();
            $books = $repo->findAllByPublicationDateAndNbBooks($date, $nbreOfBooks);


            return $this->render('book/list.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
            ]);
        }
        return $this->render('book/list.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'books' => $books,
            'nbBooksPublished' => $nbBooksPublished,
            'nbBooksNoPublished' => $nbBooksNoPublished,
        ]);
    }

    //show  all books
    #[Route('/allbooks', name: 'all_books')]
    public function allbooks(BookRepository $repo, Request $request): Response
    {
        $booksNoPublished = $repo->findBy(['published' => false]);
        $booksPublished = $repo->findBy(['published' => true]);
        $nbBooksPublished = count($booksPublished);
        $nbBooksNoPublished = count($booksNoPublished);
        $category = $repo->findSumBooksWithScienceFiction('Science-Fiction');

        $form = $this->createForm(IdSearchType::class);
        $form->handleRequest($request);
        $form2 = $this->createForm(DateAndNbreBooksType::class);
        $form2->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $ref = $form->getData();
            $books = $repo->findByRef($ref);
            return $this->render('book/listOfAllBooks.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
                'nbBooksWithSpecifiqueCategory' => $category,
            ]);
        }
        if ($form2->isSubmitted() && $form2->isValid()) {
            $date = $form2->get('publicationDate')->getData();
            $nbreOfBooks = $form2->get('nbreOfBooks')->getData();
            $books = $repo->findAllByPublicationDateAndNbBooks($date, $nbreOfBooks);

            return $this->render('book/listOfAllBooks.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'books' => $books,
                'nbBooksPublished' => $nbBooksPublished,
                'nbBooksNoPublished' => $nbBooksNoPublished,
                'nbBooksWithSpecifiqueCategory' => $category,
            ]);
        }
        
        $books=$repo->findAll();

        return $this->render('book/listOfAllBooks.html.twig', [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'books' => $books,
            'nbBooksPublished' => $nbBooksPublished,
            'nbBooksNoPublished' => $nbBooksNoPublished,
            'nbBooksWithSpecifiqueCategory' => $category,
        ]);
    }


    //Create
    #[Route('/books/create', name: 'create_book')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setPublished(true);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);
            $em->persist($book, $author);
            $em->flush();
            return $this->redirectToRoute('published_books');
        }
        return $this->render('book/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    //update
    #[Route('books/update/{ref}', name: 'update_book')]
    public function update(Request $request, BookRepository $repo, $ref, EntityManagerInterface $em): Response
    {
        $book = $repo->find($ref);
        $author = $book->getAuthor();
        $author->setNbBooks($author->getNbBooks() - 1);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);
            $em->flush();
            return $this->redirectToRoute('published_books');
        }
        return $this->render('book/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    //update categorie of books of William Shakespear to Romance
    #[Route('books/update_william', name: 'update_book_william')]
    public function updateWilliamCategory(BookRepository $repo, EntityManagerInterface $em): Response
    {
        $query=$repo->updateCategoryForWilliamShakespear("William Shakespear","romance");
        $query->execute();
        $em->flush();
        return $this->redirectToRoute('all_books');
    }



    //delete
    #[Route('book/delete/{ref}', name: 'delete_book')]
    public function delete(BookRepository $repo, $ref, EntityManagerInterface $em): RedirectResponse
    {
        $book = $repo->find($ref);
        $author = $book->getAuthor();
        $em->remove($book);
        $author->setNbBooks($author->getNbBooks() - 1);
        $em->flush();
        return $this->redirectToRoute('published_books');
    }
}
