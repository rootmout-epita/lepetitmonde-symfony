<?php

namespace App\Controller;

use App\Entity\Post;
use Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Extensions_Extension_Text;


/**
 * @Route("/admin")
 * Class BackEndController
 * @package App\Controller
 */
class BackEndController extends AbstractController
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
