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

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class MainController extends Controller
{

    /**
     * @Route("/", name="Tickets")
     * @Template()
     */
    public function mainAction()
    {
        $draw = $this->getCurrentDraw();

        $tickets = $this->getDoctrine()
            ->getRepository("GeoAppBundle:Ticket")
            ->findBy(array(
                "user" => $this->get('security.token_storage')->getToken()->getUser()
            ), array('createdAt' => 'DESC'));

        foreach ($tickets as $ticket) {

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

            $ticket->setDraws($draws);
            $ticket->setCurrentDraw($draw->getCode());
        }

        return array(
            "tickets" => $tickets,
            "draw" => $draw,
            "greeting" => $this->getGreetingMsg(),
        );
    }

    /**
     * @Route("/ticket/read/{id}", name="Ticket")
     * @Template()
     */
    public function ticketAction($id)
    {
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

        $ticket->setDraws($draws);
        $ticket->setCurrentDraw($this->getCurrentDraw()->getCode());

        return array(
            "ticket" => $ticket,
            "draws" => $draws,
        );
    }

    /**
     * @Route("/ticket/create", defaults={"id" = false})
     * @Route("/ticket/update/{id}")
     * @Template()
     */
    public function ticketFormAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ticket = new Ticket;
        if ($id) {
            $ticket = $em->getRepository("GeoAppBundle:Ticket")->findOneById($id);
            if ($ticket->getuser() != $this->get('security.token_storage')->getToken()->getUser()) {
                throw new AccessDeniedException();
            }
        }
        return array(
            "ticket" => $ticket,
        );
    }

    /**
     * @Route("/ticket/delete/{id}")
     */
    public function ticketDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ticket = $em->getRepository("GeoAppBundle:Ticket")->findOneById($id);
        if ($ticket->getuser() != $this->get('security.token_storage')->getToken()->getUser()) {
            throw new AccessDeniedException();
        }
        $em->remove($ticket);
        $em->flush();

        return $this->redirect("/");
    }

    /**
     * @Route("/ticketsave")
     */
    public function ticketSaveAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $_ticket_id = $request->get("ticket_id");
        $_start_draw = $request->get("start_draw");
        $_end_draw = $request->get("end_draw");
        $_number = $request->get("number");

        if ($_ticket_id) {
            $ticket = $em->getRepository("GeoAppBundle:Ticket")->findOneById($_ticket_id);

            foreach ($ticket->getTicketDetail() as $_ticketDetail) {
                $em->remove($_ticketDetail);
            }
        } else {
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

    /**
     * @Route("/cron")
     */
    public function cronAction()
    {
        $fetch_service = $this->get("fetch");
        $fetch_service->fetch();
        return new JsonResponse($fetch_service->log);
    }

    //Helper functions

    private function getCurrentDraw()
    {
        return $this->getDoctrine()
            ->getRepository("GeoAppBundle:Draw")
            ->findOneBy(array(), array('code' => 'DESC'));

    }

    private function getGreetingMsg()
    {

        $msg = false;

        $greetings = array(
            "6" => "Mornin' Sunshine",
            "12" => "Good morning",
            "14" => "Good day",
            "17" => "Hello!!",
            "19" => "Good afternoon",
            "21" => "Good evening",
            "23" => "Go to bed...",
        );

        foreach ($greetings as $_time => $_msg) {
            if (date("G") <= $_time) {
                $msg = $_msg;
                break;
            }
        }

        return $msg;
    }
}