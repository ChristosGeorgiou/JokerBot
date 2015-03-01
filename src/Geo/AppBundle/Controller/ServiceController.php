<?php
namespace Geo\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geo\AppBundle\Entity\Draw;

use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\ORM\Query;

class ServiceController extends Controller
{

    private $opapws = "http://applications.opap.gr/DrawsRestServices/joker/";

    public function fetchAction(ProgressBar &$progress)
    {

        $progress->setMessage('Loading missing draws...');
        $progress->advance();

        if (!$missingDraws = $this->getMissingDraws()) {
            $progress->setMessage('No missing draws were found!');
            $progress->advance();
            return;
        } else {
            $progress->setMessage("Found " . count($missingDraws) . " missing draws");
            $progress->advance();

            foreach ($missingDraws as $code) {
                if ($status = $this->fetchDraw($code, $draw)) {
                    $progress->setMessage("[SUCC] {$code} - " . json_encode($draw->results));
                    $progress->advance();
                    $this->saveDraw($draw);
                } else {
                    $progress->setMessage("[FAIL] {$code}");
                    $progress->advance();
                }
            }
        }
    }

    public function getMissingDraws()
    {
        $em = $this->getDoctrine()->getManager();
        $results = $em
            ->createQuery("select min(t.startDraw) as s, max(t.endDraw) as e from GeoAppBundle:Ticket t")
            ->getSingleResult(Query::HYDRATE_ARRAY);
        if (!$results["s"] || !$results["e"]) {
            return array();
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

    public function fetchDraw($code, &$draw)
    {
        try {
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
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage() . "\n");
            //return FALSE;
        }
    }

    public function saveDraw($draw)
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
