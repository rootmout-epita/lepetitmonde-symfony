<?php

namespace App\Controller;

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
        return new Response("<body>je suis un test</body>");
    }
}