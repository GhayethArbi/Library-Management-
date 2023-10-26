<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
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
    #[Route('/authors', name: 'list_authors')]
    public function list(AuthorRepository $Repo): Response
    {
        $authors=$Repo->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }
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
    #[Route('author/delete/{id}', name: 'delete_author')]
    public function delete(AuthorRepository $repo,$id,EntityManagerInterface $em): RedirectResponse
    {
        
        $author = $repo->find($id);
            $em->remove($author);
            $em->flush();
            return $this->redirectToRoute('list_authors');
    }
    #[Route('authors/delete/no_books',name: 'delete_authors_with_no_boks')]
    public function deleteAuthorsWithNoBooks(AuthorRepository $repo,EntityManagerInterface $em): RedirectResponse
    {
        $authors = $repo->findBy(['nb_books'=> 0]);
        foreach($authors as $author){
        $em->remove($author);
    }
        $em->flush();
        return $this->redirectToRoute('list_authors');
    }
    #[Route('author/show/{id}', name: 'show_author')]
    public function showAuthor($id, AuthorRepository $repo):Response
    {
        $author=$repo->find($id);
        
        return $this->render('author/show.html.twig', [
           'author'=>$author ,
        ]);
    }
}
