<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{
    /**
     * @Route("/register", name="user.register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        //Handle the form.
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        //If submitted, the user is created in the database.
        if($form->isSubmitted() AND $form->isValid())
        {
            //encode password
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            //Persist user in base.
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            //Redirect user to login page.
            $this->addFlash("success", "Votre compte a été créé, bienvenu !");
            return $this->redirectToRoute('user.login');
        }

        //Display form is not submitted.
        return $this->render("back_end/register.html.twig",[
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="user.login")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        //catch last login tentative.
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render("back_end/login.html.twig", [
            "last_username" => $lastUsername,
            "error" => $error
        ]);
    }

    /**
     * Edit the user settings
     *
     * @Route("/my_account", name="user.edit")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        dump($form->isSubmitted() and $form->isValid());

        if($form->isSubmitted())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Votre compte a été mis à jour correctement");
            return $this->redirectToRoute("post.index");
        }

        return $this->render('back_end/edit_user.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/logout", name="user.logout")
     */
    public function logout()
    {
        //nothing to do ?
    }
}