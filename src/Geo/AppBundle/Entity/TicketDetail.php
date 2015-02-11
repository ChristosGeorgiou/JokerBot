<?php

namespace Geo\AppBundle\Entity;

class TicketDetail
{
    private $id;
    private $numbers;
    private $ticket;

    public function getId()
    {
        return $this->id;
    }

    public function setNumbers($numbers)
    {
        $this->numbers = json_encode($numbers);

        return $this;
    }

    public function getNumbers()
    {
        return json_decode($this->numbers);
    }

    public function setTicket(\Geo\AppBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getTicket()
    {
        return $this->ticket;
    }
}
