<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Extensions_Extension_Text;
use Parsedown;

class PostController extends AbstractController
{
    private $twig;

    /**
     * PostController constructor.
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig =$twig;
        $this->twig->addExtension(new Twig_Extensions_Extension_Text());

        $this->twig->addFilter(new TwigFilter('markdown',
            function ($value):string {
                return (new Parsedown())->text($value);
            },array('is_safe' => array('html'))
        ));

        $this->twig->addFilter(new TwigFilter('noMarkdown',
            function($value)
            {
                return preg_replace('`\(.*?\)|[^a-zA-Z0-9 ]`i', '', $value);
            }));

        $this->twig->addFunction(new TwigFunction('rand', function(){return rand(0,100);}));
    }


    /**
     * Display the latest posts, website homepage.
     *
     * @Route("/", name="post.index")
     * @return Response
     */
    public function index(): Response
    {


        $posts = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('front/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * Display the article
     *
     * @Route("/article/{slug}-{id}", name="post.view", requirements={"slug": "[a-z0-9\-]*"})
     * @param int $id
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function view(int $id, string $slug): Response
    {
        //Check if post ID exist
        if(!$post = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->find($id))
        {
            return $this->redirectToRoute('post.index');
        }

        //Check if the slug is ok. Redirect with the correct slug.
        if($slug !== $post->getSlug())
        {
            return $this->redirectToRoute('post.view', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }


        return $this->render('front/view.html.twig', [
            "post" => $post
        ]);
    }
}