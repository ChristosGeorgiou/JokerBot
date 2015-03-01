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

    private $currentDraw;

    private $draws;

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

    public function setDraws($draws){
        $this->draws = $draws;

        return $this;
    }

    public function getEarnings()
    {
        foreach ($this->ticketDetail as $_ticketDetail) {
            $_ticketDetailNumbersSliced[] = $this->sliceColumn($_ticketDetail->getNumbers());
        }
        $earnings = 0;
        foreach ($this->draws as $draw) {
            $_numbers = $this->sliceColumn($draw->getNumbers());
            $_results = array();
            foreach ($_ticketDetailNumbersSliced as $_slice) {
                $_results[] = $this->compareColumns($_numbers, $_slice);
            }
            $draw->setResults($_results);
            $earnings += $draw->getEarnings();
        }

        return $earnings;
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

    private function sliceColumn($column)
    {
        //var_dump($column);
        $numbers = array_slice($column, 0, 5);
        sort($numbers);
        $joker = $column[5];
        return array("n" => $numbers, "j" => $joker);
    }

    private function compareColumns($drawColumn, $ticketColumn)
    {
        $res = array();
        foreach ($ticketColumn["n"] as $_ticketNumber) {
            $item["status"] = in_array($_ticketNumber, $drawColumn["n"]);
            $item["value"] = $_ticketNumber;
            $res[] = $item;
        }
        $item["status"] = ($ticketColumn["j"] == $drawColumn["j"]);
        $item["value"] = $ticketColumn["j"];
        $res[] = $item;
        return $res;
    }
}
