<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("s/", name="list")
     */
    public function list(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository): Response
    {
        $category = $request->query->has('category') ? $categoryRepository->find($request->query->get('category')) : null;

        if ($category)
            $posts = $postRepository->findBy([ 'category' => $category ]);
        else
            $posts = $postRepository->findAll();

        return $this->render('post/list.html.twig', [
            'posts' => $posts,
            'categories' => $categoryRepository->findAll(),
            'selectedCategory' => $category
        ]);
    }

    /**
     * @Route("/read/{id}", name="read")
     */
    public function view(Post $post): Response
    {
        return $this->render('post/view.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('absolute_posts_images_directory'),
                        $newFilename
                    );
                } catch (FileException $exception) {
                    $this->addFlash('danger', 'The image can\'t be saved');
                }

                $post->setImage($newFilename);
            }

            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Your post was created!');
            return $this->redirectToRoute('index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $oldImage = $post->getImage();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename  = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('absolute_posts_images_directory'),
                        $newFilename
                    );
                } catch (FileException $exception) {
                    $this->addFlash('danger', 'The image can\'t be saved');
                }

                if ($oldImage)
                    unlink($this->getParameter('absolute_posts_images_directory') . $oldImage);

                $post->setImage($newFilename);
            }

            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'The post was edited!');
            return $this->redirectToRoute('index');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Post $post, EntityManagerInterface $entityManagerInterface)
    {
        if ($post->getImage())
            unlink($this->getParameter('absolute_posts_images_directory') . $post->getImage());
        
        $entityManagerInterface->remove($post);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'The post was deleted!');

        return $this->redirectToRoute('index');
    }
}
