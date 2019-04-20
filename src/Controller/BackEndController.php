<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $post = new Post();
        $post->setAuthor($this->getUser());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            $manager = $this
                ->getDoctrine()
                ->getManager();

            $manager->persist($post);
            $manager->flush();

            $this->addFlash('success', 'Post ajouté avec succès');
            return $this->redirectToRoute('backend.index');
        }

        return $this->render('back_end/post_form.html.twig',[
            'form' => $form->createView(),
            'title' => "Nouveau post"
        ]);
    }

    /**
     * @Route("/post/edit/{id}", name="post.edit")
     * @param Post $post
     */
    public function edit(Post $post, Request $request)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid())
        {
            $manager = $this
                ->getDoctrine()
                ->getManager();

            $manager->flush();

            $this->addFlash('success', 'Post modifié avec succès');
            return $this->redirectToRoute('backend.index');
        }

        return $this->render('back_end/post_form.html.twig',[
            'form' => $form->createView(),
            'title' => "Edition du post"
        ]);
    }

    /**
     * @Route("/post/delete/{id}", name="post.delete")
     * @param Post $post
     */
    public function delete(Post $post)
    {
        $this
            ->getDoctrine()
            ->getManager()
            ->remove($post);

        $this
            ->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', 'Post supprimé avec succès');
        return $this->redirectToRoute('backend.index');
    }
}
