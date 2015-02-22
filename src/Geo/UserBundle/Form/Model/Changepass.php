<?php

namespace Geo\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Geo\UserBundle\Entity\User;

class Changepass
{
    /**
     * @Assert\NotBlank()
     */
    protected $oldpassword;

    /**
     * @Assert\NotBlank()
     */
    protected $newpassword;

    public function getOldpassword()
    {
      return $this->oldpassword;
    }

    public function getNewpassword()
    {
      return $this->newpassword;
    }
}
