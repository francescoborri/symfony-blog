<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('post.list');
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(PostRepository $postRepository): Response
    {
        return $this->render('post/list.html.twig', [
            'posts' => $postRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $exception) {
                    $this->addFlash('error', 'The image can\'t be saved.');
                }

                $post->setImage($newFilename);
            }

            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Post was created!');
            return $this->redirectToRoute('index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/view/{id}", name="view")
     */
    public function view(Post $post): Response
    {
        return $this->render('post/view.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Post $post)
    {
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Post $post)
    {
    }
}
