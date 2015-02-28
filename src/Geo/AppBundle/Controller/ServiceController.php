<?php
namespace Geo\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Console\Output\OutputInterface;
use Geo\AppBundle\Entity\Draw;

class ServiceController extends Controller
{

    private $opapws = "http://applications.opap.gr/DrawsRestServices/joker/";

    public function fetchAction(OutputInterface &$output)
    {
        $output->writeln("Loading missing draws");

        //$fetchedResults = array();
        if (!$missingDraws = $this->getMissingDraws()) {
            $output->writeln("No missing draws were found");
            return;
        } else {
            $output->writeln("Missing draws: ", implode(", ", $missingDraws));

            foreach ($missingDraws as $code) {
                $output->write("Draw {$code}: ");

                if ($status = $this->fetchDraw($code, $draw)) {
                    $fetchedResults[] = array("code" => $code, "status" => $status, "date" => date("d/m/Y", strtotime($draw->drawTime)), "numbers" => json_encode($draw->results),);
                    $this->saveDraw($draw);
                    $this->refreshTickets($draw);

                    $output->writeln("COMPLETED [" . json_encode($draw) . "]");
                } else {
                    $output->writeln("FAILED");
                }

                //$output->writeln($results);
            }
            //return $fetchedResults;
        }
    }

    private function getMissingDraws()
    {
        $em = $this->getDoctrine()->getManager();
        $results = $em
            ->createQuery("select min(t.startDraw) as s, max(t.endDraw) as e from GeoAppBundle:Ticket t")
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
            ->getScalarResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $codes = array_map('current', $draws);
        $missing = array_diff($range, $codes);
        return $missing;
    }

    private function fetchDraw($code, &$draw)
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

    private function refreshTickets($draw)
    {
    }
}
