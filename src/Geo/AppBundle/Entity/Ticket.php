<?php

namespace Geo\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Geo\UserBundle\Entity\User;

class Ticket
{
    private $id;
    private $startDraw;
    private $endDraw;
    private $createdAt;
    private $ticketDetail;

    private $earnings;
    private $completion;

    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function setStartDraw($startDraw)
    {
        $this->startDraw = $startDraw;

        return $this;
    }

    public function getStartDraw()
    {
        return $this->startDraw;
    }

    public function setEndDraw($endDraw)
    {
        $this->endDraw = $endDraw;

        return $this;
    }

    public function getEndDraw()
    {
        return $this->endDraw;
    }

    public function setEarnings($earnings)
    {
        $this->earnings = $earnings;

        return $this;
    }

    public function getEarnings()
    {
        return $this->earnings;
    }

    public function setCompletion($_currentDraw)
    {
      $_startDraw = $this->getStartDraw();
      $_endDraw = $this->getEndDraw();
      $_completion = ($_endDraw <= $_currentDraw)?100:($_currentDraw - $_startDraw)*100/($_endDraw - $_startDraw);
      $this->completion = $_completion;

      return $this;
    }

    public function getCompletion()
    {
        return $this->completion;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    public function setTicketDetail(\Geo\AppBundle\Entity\TicketDetail $ticketDetail = null)
    {
        $this->ticketDetail = $ticketDetail;

        return $this;
    }

    public function getTicketDetail()
    {
        return $this->ticketDetail;
    }

    public function setUser(User $user)
    {
      $this->user = $user;
    }

    public function getUser()
    {
      return $this->user;
    }
}
