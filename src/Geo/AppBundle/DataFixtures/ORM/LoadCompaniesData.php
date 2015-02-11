<?php


namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Company;

class LoadCompaniesData extends AbstractFixture implements OrderedFixtureInterface
{

    private $demoCompanies=array(
        "softloop"=>array(
            "name"=>"SoftLoop",
            "legalName"=>"ΠΑΠΑΦΩΤΗΣ Β. ΓΕΩΡΓΙΟΥ Χ. Ο.Ε.",
            "taxId"=>"546123879",
            "taxOffice"=>"Α' ΑΘΗΝΩΝ",
            "logo"=>"lorempixel.com/56/42/abstract",
            "group"=>1,
        ),
        "microsoft"=>array(
            "name"=>"Microsoft",
            "legalName"=>"Microsoft Corporation",
            "taxId"=>"98746544",
            "taxOffice"=>"Α' ΑΘΗΝΩΝ",
            "logo"=>"lorempixel.com/56/42/abstract",
            "group"=>1,
        ),
        "softone"=>array(
            "name"=>"SoftOne",
            "legalName"=>"Lorem Ipsum Dolor",
            "taxId"=>"98746544",
            "taxOffice"=>"Α' ΑΘΗΝΩΝ",
            "logo"=>"lorempixel.com/56/42/abstract",
            "group"=>1,
        ),
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->demoCompanies as $alias => $com) {
            $company = new Company();
            $company
                ->setAlias($alias)
                ->setName($com['name'])
                ->setLegalName($com['legalName'])
                ->setTaxId($com['taxId'])
                ->setTaxOffice($com['taxOffice'])
                ->setLogo($com['logo'])
                ->setGroup($this->getReference('default-company-group'));

            $manager->persist($company);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}