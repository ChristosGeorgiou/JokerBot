<?php

namespace Geo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable
{
  private $id;
  private $username;
  private $password;
  private $isActive;

  public function __construct()
  {
    $this->isActive = true;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function setUsername($username)
  {
    $this->username = $username;
  }

  public function getPassword()
  {
    return $this->password;
  }

  public function setPassword($hashedPassword)
  {
    $this->password = $hashedPassword;
  }

  public function getRoles()
  {
    return array('ROLE_USER');
  }

  public function getSalt()
  {
    // Bcrypt have a built-in salt mechanism
    return null;
  }

  public function eraseCredentials()
  {
  }

  public function serialize()
  {
    return serialize(array(
    $this->id,
    $this->username,
    $this->password,
    ));
  }

  public function unserialize($serialized)
  {
    list (
    $this->id,
    $this->username,
    $this->password,
    ) = unserialize($serialized);
  }

  public function isPasswordLegal()
  {
    return $this->username != $this->password;
  }
}
