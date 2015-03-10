<?php
namespace Geo\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geo\AppBundle\Entity\Draw;

use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\ORM\Query;

class ServiceController extends Controller
{

    public $log;
    private $opapws = "http://applications.opap.gr/DrawsRestServices/joker/";

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function fetch()
    {
        $this->log("CALCULATING MISSING DRAWS");

        if (!$missingDraws = $this->getMissingDraws()) {
            $this->log('NO MISSING DRAWS WHERE FOUND');
            return FALSE;
        }

        $this->log("FOUND " . count($missingDraws) . " MISSING DRAWS");

        foreach ($missingDraws as $code) {

            if ($draw = $this->fetchDraw($code)) {
                $this->log("SUCC - {$code} - " . json_encode($draw->results));
                $this->saveDraw($draw);
            } else {
                $this->log("FAIL - {$code}");
            }

        }
    }

    private function log($message)
    {
        $this->logger->info($message);
        $this->log[] = $message;
    }

    private function getMissingDraws()
    {
        $em = $this->getDoctrine()->getManager();
        $results = $em
            ->createQuery("select min(t.startDraw) as s, max(t.endDraw) as e from GeoAppBundle:Ticket t")
            ->getSingleResult(Query::HYDRATE_ARRAY);
        if (!$results["s"] || !$results["e"]) {
            return false;
        }

        $results["s"] = ($results["s"] < 1500) ? 1500 : $results["s"];
        $range = range($results["s"], $results["e"]);
        $draws = $em
            ->getRepository("GeoAppBundle:Draw")
            ->createQueryBuilder('q')
            ->select('q.code')
            ->getQuery()
            ->getScalarResult(Query::HYDRATE_ARRAY);
        $codes = array_map('current', $draws);
        $missing = array_diff($range, $codes);

        return $missing;
    }

    private function fetchDraw($code)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->opapws . $code . ".json");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $ret = json_decode($output);
            if (@$ret->draw) {
                return $ret->draw;
            } else {
                return FALSE;
            }
        } catch (Exception $e) {

            $this->log("ERROR:" . $e->getMessage());
            return FALSE;
        }
    }

    private function saveDraw($draw)
    {
        $_draw = new Draw();
        $_draw->setCode($draw->drawNo);
        $_draw->setNumbers($draw->results);
        $_draw->setDrawAt(new \DateTime($draw->drawTime));
        $_draw->setCreatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($_draw);
        $em->flush();
    }
}
