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
    private $currentDraw;
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

    public function setCurrentDraw($currentDraw){
        $this->currentDraw = $currentDraw;
    }

    public function getCompletion()
    {
        if(!$this->currentDraw){
            return 0;
        }

        $_startDraw = $this->getStartDraw();
        $_endDraw = $this->getEndDraw();
        if ($_startDraw == $_endDraw) {
            $_completion = ($_endDraw == $this->currentDraw) ? 1 : 0;
        } else {
            $_pc = ($this->currentDraw - $_startDraw) / ($_endDraw - $_startDraw);
            $_completion = min(1, $_pc);
        }

        return $_completion;
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
