<?php

namespace App\Controller;

use App\Form\BlogType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('listPost');
    }

    /**
     * @Route("/list", name="listPost")
     */
    public function listPost(PostRepository $postRepository): Response
    {
        $blogs = $postRepository->findAll();
        return $this->render('post/list.html.twig', [
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/create", name="editPost")
     */
    public function createPost(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $form = $this->createForm(BlogType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
