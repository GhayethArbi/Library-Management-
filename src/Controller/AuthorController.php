<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinMaxBooksType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    //Read
    //show By id
    #[Route('author/show/{id}', name: 'show_author')]
    public function showAuthor($id, AuthorRepository $repo):Response
    {
        $author=$repo->find($id);
        
        return $this->render('author/show.html.twig', [
           'author'=>$author ,
        ]);
    }
    //shwo all authors ordred by email
    #[Route('/authors_ordred', name: 'list_authors_byEmail')]
    public function listOrdredByEmail(AuthorRepository $Repo, Request $request): Response
    {
        
        $authors = $Repo->findAllOrdredByEmail();
        $form=$this->createForm(MinMaxBooksType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $min=$form->get('minNumber')->getData();
            $max=$form->get('maxNumber')->getData();
            $authors=$Repo->findByNbreBooks($min,$max);
        }
        return $this->render('author/authorsOBE.html.twig', [
            'authors' => $authors,
            'form'=>$form->createView(),
        ]);
    }
    //show all authors
    #[Route('/authors', name: 'list_authors')]
    public function list(AuthorRepository $Repo,Request $request): Response
    {
        $authors=$Repo->findAll();
        $form=$this->createForm(MinMaxBooksType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $min=$form->get('minNumber')->getData();
            $max=$form->get('maxNumber')->getData();
            $authors=$Repo->findByNbreBooks($min,$max);
        }
        
        
        return $this->render('author/list.html.twig', [
           'form'=>$form->createView(),
            'authors' => $authors,
        ]);
    }
    


    //Create
    #[Route('/authors/create', name: 'create_author')]
    public function add(Request $request ,EntityManagerInterface $em): Response
    {
        $author = new Author();
        $author->setNbBooks(0);
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('list_authors');
        }

        return $this->render('author/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //Update
    #[Route('authors/update/{id}', name: 'update_author')]
    public function update(Request $request ,AuthorRepository $repo,$id,EntityManagerInterface $em): Response
    {
        
        
        $author = $repo->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('list_authors');
        }

        return $this->render('author/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    //Delete
    //Delete by id
    #[Route('author/delete/{id}', name: 'delete_author')]
    public function delete(AuthorRepository $repo,$id,EntityManagerInterface $em): RedirectResponse
    {
        
        $author = $repo->find($id);
            $em->remove($author);
            $em->flush();
            return $this->redirectToRoute('list_authors');
    }
    //Delete All with no books
    #[Route('authors/delete/no_books',name: 'delete_authors_with_no_books')]
    public function deleteAuthorsWithNoBooks(AuthorRepository $repo,EntityManagerInterface $em): RedirectResponse
    {
        /*$authors = $repo->findBy(['nb_books'=> 0]);
        foreach($authors as $author){
            $em->remove($author);
            $em->flush();
        }*/
        $repo->deleteAuthorsWithZerosNbBooks();
        return $this->redirectToRoute('list_authors');
    }
    
}
