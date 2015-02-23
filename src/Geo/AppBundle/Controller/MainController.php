<?php
namespace Geo\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geo\AppBundle\Entity\Ticket;
use Geo\AppBundle\Entity\TicketDetail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class MainController extends Controller {

    /**
     * @Route("/", name="Tickets")
     * @Template()
     */
    public function mainAction() {

      $greetings=array(
        "6" => "Mornin' Sunshine",
        "12" => "Good morning",
        "14" => "Good day",
        "17" => "Hello!!",
        "19" => "Good afternoon",
        "21" => "Good evening",
        "23" => "Go to bed...",
      );

      foreach($greetings as $_time=>$_msg){
        if(date("G")<$_time)
        {
          $msg = $_msg;//"{$_msg}.{$_time}".date("G");
          break;
        }
      }

      //$user = $this->get('security.token_storage')->getToken()->getUser();

      $tickets = $this->getDoctrine()
        ->getRepository("GeoAppBundle:Ticket")
        ->findBy(array(
          "user" => $this->get('security.token_storage')->getToken()->getUser()
        ), array('createdAt' => 'DESC'));

      return array(
        "tickets" => $tickets,
        "greeting" => $msg,
        );
    }

    /**
     * @Route("/ticket/read/{id}", name="Ticket")
     * @Template()
     */
    public function ticketAction($id) {
        $ticket = $this->getDoctrine()
          ->getRepository("GeoAppBundle:Ticket")
          ->findOneById($id);

        $draws = $this->getDoctrine()
          ->getRepository("GeoAppBundle:Draw")
          ->createQueryBuilder('p')
          ->where('p.code >= :startDraw')
          ->andWhere('p.code <= :endDraw')
          ->setParameter('startDraw', $ticket->getStartDraw())
          ->setParameter('endDraw', $ticket->getEndDraw())
          ->orderBy('p.code', 'DESC')
          ->getQuery()
          ->getResult();

        $results = array();
        //$_ticketDetails = $ticket->getTicketDetail();
        foreach ($ticket->getTicketDetail() as $_ticketDetail) {
            $_ticketDetailNumbersSliced[] = $this->sliceColumn($_ticketDetail->getNumbers());
        }
        $earnings = 0;
        foreach ($draws as $draw) {
            $_numbers = $this->sliceColumn($draw->getNumbers());
            $_results = array();
            foreach ($_ticketDetailNumbersSliced as $_slice) {
                $_results[] = $this->compareColumns($_numbers, $_slice);
            }
            $draw->setResults($_results);
            $earnings+= $draw->getEarnings();
        }
        $ticket->setEarnings($earnings);
        $ticket->setCompletion($this->getCurrentDraw());
        //var_dump($draw->getResults());
        return array("ticket" => $ticket, "draws" => $draws,);
    }

    private function sliceColumn($column) {
        //var_dump($column);
        $numbers = array_slice($column, 0, 5);
        sort($numbers);
        $joker = $column[5];
        return array("n" => $numbers, "j" => $joker);
    }

    private function compareColumns($drawColumn, $ticketColumn) {
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

    /**
     * @Route("/ticket/create", defaults={"id" = false})
     * @Route("/ticket/update/{id}")
     * @Template()
     */
    public function ticketFormAction($id) {
        $ticket = new Ticket;
        if($id){
          $ticket = $this->getDoctrine()
            ->getRepository("GeoAppBundle:Ticket")
            ->findOneById($id);

          if($ticket->getuser() != $this->get('security.token_storage')->getToken()->getUser()){
            throw new AccessDeniedException();
          }
        }
        return array(
          "ticket"=>$ticket,
        );
    }

    /**
     * @Route("/ticket/delete/{id}")
     * @Template()
     */
    public function ticketDeleteAction($id) {
        // $ticket = new Ticket;
        // if($id){
        //   $ticket = $this->getDoctrine()
        //     ->getRepository("GeoAppBundle:Ticket")
        //     ->findOneById($id);
        // }
        // return array(
        //   "ticket"=>$ticket,
        // );
    }

    /**
     * @Route("/ticketsave")
     */
    public function ticketSaveAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $_ticket_id = $request->get("ticket_id");
        $_start_draw = $request->get("start_draw");
        $_end_draw = $request->get("end_draw");
        $_number = $request->get("number");

        if($_ticket_id){
          $ticket = $em->getRepository("GeoAppBundle:Ticket")->findOneById($_ticket_id);

          foreach($ticket->getTicketDetail() as $_ticketDetail){
            $em->remove($_ticketDetail);
          }
        }
        else{
          $ticket = new Ticket();
          $ticket->setEarnings(0);
          $ticket->setCreatedAt(new \DateTime());
          $ticket->setUser($this->get('security.token_storage')->getToken()->getUser());
        }

        $ticket->setStartDraw($_start_draw);
        $ticket->setEndDraw($_end_draw);
        $em->persist($ticket);

        foreach ($_number as $col => $iter) {
            if (count(array_filter($iter)) == count($iter)) {
                $detail = new TicketDetail();
                $detail->setNumbers(array_values($iter));
                $detail->setTicket($ticket);
                $em->persist($detail);
            }
        }
        $em->flush();
        $response = new JsonResponse();
        return $response;
    }
    private function getCurrentDraw() {
        $draw = $this->getDoctrine()->getRepository("GeoAppBundle:Draw")->findOneBy(array(), array('code' => 'DESC'));
        return $draw->getCode();
    }
}
