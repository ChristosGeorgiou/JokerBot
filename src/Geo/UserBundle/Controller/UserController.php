<?php

namespace Geo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Geo\UserBundle\Form\Type\RegistrationType;
use Geo\UserBundle\Form\Model\Registration;

use Geo\UserBundle\Form\Type\ChangepassType;
use Geo\UserBundle\Form\Model\Changepass;

use Geo\UserBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error' => $error,
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();

            $user = $registration->getUser();

            $hashedPassword = $this->container
                ->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($hashedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return array(
                'form' => false,
                "error" => false,
            );

        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/changepass", name="changepass")
     * @Template()
     */
    public function changepassAction(Request $request)
    {
        $changepass = new Changepass();
        $form = $this->createForm(new ChangepassType(), $changepass);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // $changepass = $form->getData();
            //
            // $user = $this->get('security.token_storage')
            //   ->getToken()
            //   ->getUser();
            //
            // $oldpassowrd = $this->container
            //  ->get('security.password_encoder')
            //  ->encodePassword($user, $changepass->getOldPassword());
            //
            // if($oldpassowrd != $user->getPassword()){
            //
            // }
            //
            // $hashedPassword = $this->container
            //  ->get('security.password_encoder')
            //  ->encodePassword($user, $user->getPassword());
            //
            // $user->setPassword($hashedPassword);
            //
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($user);
            // $em->flush();
            // return $this->redirect("login");
            //

            $response = new JsonResponse();
            return $response;
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
