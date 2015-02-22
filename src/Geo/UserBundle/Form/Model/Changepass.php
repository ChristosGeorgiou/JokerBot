<?php

namespace Geo\UserBundle\Form\Model;

class Changepass
{
    protected $oldpassword;

    protected $newpassword;

    public function getOldPassword()
    {
      return $this->oldpassword;
    }

    public function setOldPassword($oldpassword)
    {
      $this->oldpassword = $oldpassword;
    }

    public function getNewPassword()
    {
      return $this->newpassword;
    }

    public function setNewPassword($newpassword)
    {
      $this->newpassword = $newpassword;
    }
    
}
