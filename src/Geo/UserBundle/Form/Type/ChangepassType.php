<?php

namespace Geo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangepassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('oldpassword',"password")
          ->add('newpassword','repeated',array(
            'first_name'  => 'password',
            'second_name' => 'repeat',
            'type' => 'password',
          ))
          ->add('submit', 'submit',array(
            "label"=>"Change Password"
          ));
    }

    public function getName()
    {
        return 'changepass';
    }
}
