<?php

namespace Geo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Geo\UserBundle\Form\Type\RegistrationType;
use Geo\UserBundle\Form\Model\Registration;

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
        'error'         => $error,
      );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
      $registration = new Registration();
      // $form = $this->createForm(new RegistrationType(), $registration, array(
      //   'action' => $this->generateUrl('account_create'),
      // ));

      $form = $this->createForm(new RegistrationType(), $registration);

      $em = $this->getDoctrine()->getManager();

      //$form = $this->createForm(new RegistrationType(), new Registration());

      $form->handleRequest($request);

      if ($form->isValid()) {
          $registration = $form->getData();
          $em->persist($registration->getUser());
          $em->flush();
          return $this->redirect("login");
      }


      //  return $this->render(
      //      'AcmeAccountBundle:Account:register.html.twig',
      //      array('form' => $form->createView())
      //  );


      return array(
        'form' => $form->createView(),
      );
    }

    // /**
    //  * @Route("/create", name="account_create")
    //  * @Template()
    //  */
    // public function createAction(Request $request)
    // {
    //     $em = $this->getDoctrine()->getManager();
    //
    //     $form = $this->createForm(new RegistrationType(), new Registration());
    //
    //     $form->handleRequest($request);
    //
    //     if ($form->isValid()) {
    //         $registration = $form->getData();
    //
    //         $em->persist($registration->getUser());
    //         $em->flush();
    //
    //         return $this->redirect(...);
    //     }
    //
    //     return $this->render(
    //         'AcmeAccountBundle:Account:register.html.twig',
    //         array('form' => $form->createView())
    //     );
    // }

    // public function registerAction(Request $request)
    // {
    //   $user = new User();
    //
    //   $form = $this->createFormBuilder($user)
    //     ->add('username', 'text')
    //     ->add('password', 'password')
    //     ->add(false, 'checkbox', array(
    //       'label'     => 'I agree to the <a href="#">Terms of Service</a>.',
    //       'required'  => true,
    //     ))
    //     ->add('save', 'submit', array('label' => 'Create User'))
    //     ->getForm();
    //
    //   $form->handleRequest($request);
    //   if ($form->isValid()) {
    //       // perform some action, such as saving the task to the database
    //
    //     return $this->redirect($this->generateUrl('task_success'));
    //   }
    //
    //   return array(
    //     'form' => $form->createView(),
    //   );
    // }

}
