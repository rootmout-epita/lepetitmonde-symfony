<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin")
 * Class BackEndController
 * @package App\Controller
 */
class BackEndController extends AbstractController
{
    /**
     * @Route("/", name="backend.index")
     */
    public function index()
    {
        $posts = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('back_end/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/new", name="post.new")
     */
    public function new()
    {
        //TODO nouveau post
    }

    /**
     * @Route("/post/edit/{id}", name="post.edit")
     * @param Post $post
     */
    public function edit(Post $post)
    {
        //TODO edit le post
    }

    /**
     * @Route("/post/delete/{id}", name="post.delete")
     * @param Post $post
     */
    public function delete(Post $post)
    {
        //TODO supprimer le post
    }
}
