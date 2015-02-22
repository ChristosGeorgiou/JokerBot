<?php

namespace Geo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\ExecutionContext;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('username','text',array(
        'attr' => array(
          'placeholder' => 'Username',
        ),
        'label' => false,
      ))
      ->add('password','repeated',array(
        'first_name'  => 'password',
        'second_name' => 'repeat',
        'first_options'  => array(
          'attr' => array(
            'placeholder' => 'Password',
          )
        ),
        'second_options' => array(
            'attr' => array(
              'placeholder' => 'Repeat Password',
            )
          ),
        'type' => 'password',
      ));
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
        'data_class' => 'Geo\UserBundle\Entity\User'
    ));
  }

  public function getName()
  {
      return 'user';
  }
}
