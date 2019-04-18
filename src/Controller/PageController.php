<?php

namespace App\Controller;

use App\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class PageController extends AbstractController
{
    /**
     * @Route("/", name="page.index")
     * @return Response
     */
    public function index()
    {
        $posts = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        dump($posts);

        return new Response("<body>je suis la homepage</body>");
    }
}