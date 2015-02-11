<?php

namespace Geo\AppBundle\Entity;

class Draw
{
    private $id;
    private $code;
    private $numbers;
    private $drawAt;
    private $createdAt;

    private $results;
    private $earnings;

    public function getId()
    {
        return $this->id;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
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

    public function setDrawAt($drawAt)
    {
        $this->drawAt = $drawAt;

        return $this;
    }

    public function getDrawAt()
    {
        return $this->drawAt;
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

    public function setResults($results)
    {
        $this->results = $results;

        $this->earnings = $this->calcEarnings();

        return $this;
    }

    public function getResults()
    {
      return $this->results;
    }

    public function getEarnings()
    {
      return $this->earnings;
    }

    private function calcEarnings(){
      //var_dump($this->results[0]);

      $drawEarning=0;
      foreach($this->results as $result){
        $nums=0;
        $earnings=0;
        for($i=0 ; $i<5 ; $i++){
          if($result[$i]["status"]) $nums++;
        }

        if( $result[5]["status"])
        {
          switch($nums){
            case 5 : $earnings = -1; break;
            case 4 : $earnings = 2500; break;
            case 3 : $earnings = 50; break;
            case 2 : $earnings = 2; break;
            case 1 : $earnings = 1.5; break;
          }
        }
        else
        {
          switch($nums){
            case 5 : $earnings = -1; break;
            case 4 : $earnings = 50; break;
            case 3 : $earnings = 2; break;
            case 2 : $earnings = 0; break;
            case 1 : $earnings = 0; break;
          }
        }

        if($earnings==-1){
          $drawEarning=-1;
          break;
        }
        $drawEarning+=$earnings;
      }

      return $drawEarning;
    }
}
