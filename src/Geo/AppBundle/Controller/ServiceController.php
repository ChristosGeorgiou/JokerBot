<?php
namespace Geo\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Geo\AppBundle\Entity\Draw;
class ServiceController extends Controller {
    private $opapws = "http://applications.opap.gr/DrawsRestServices/joker/";
    /**
     * @Route("/cron", name="Fetch OPAP")
     * @Template()
     */
    public function fetchAction() {
        $missingDraws = $this->getMissingDraws();
        $fetchedResults = array();
        foreach ($missingDraws as $code) {
            if ($status = $this->fetchDraw($code, $draw)) {
                $fetchedResults[] = array("code" => $code, "status" => $status, "date" => date("d/m/Y", strtotime($draw->drawTime)), "numbers" => json_encode($draw->results),);
                $this->saveDraw($draw);
                $this->refreshTickets($draw);
            }
        }
        return array("fetchedResults" => $fetchedResults);
    }
    private function getMissingDraws() {
        $em = $this->getDoctrine()->getManager();
        $results = $em->createQuery("select min(t.startDraw) as s, max(t.endDraw) as e from GeoAppBundle:Ticket t")->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //var_dump($results);
        if (!$results["s"] || !$results["e"]) {
            return array();
        }
        $range = range($results["s"], $results["e"]);
        $draws = $em->getRepository("GeoAppBundle:Draw")->createQueryBuilder('q')->select('q.code')->getQuery()->getScalarResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $codes = array_map('current', $draws);
        //var_dump($codes);
        $missing = array_diff($range, $codes);
        return $missing;
    }
    private function fetchDraw($code, &$draw) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->opapws . $code . ".json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($output);
        if (@$ret->draw) {
            $draw = $ret->draw;
            return TRUE;
        } else {
            return FALSE;
        }
    }
    private function saveDraw($draw) {
        $_draw = new Draw();
        $_draw->setCode($draw->drawNo);
        $_draw->setNumbers($draw->results);
        $_draw->setDrawAt(new \DateTime($draw->drawTime));
        $_draw->setCreatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($_draw);
        $em->flush();
    }
    private function refreshTickets($draw) {
    }
}
