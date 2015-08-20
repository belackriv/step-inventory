<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Office;

class LoadOfficeData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $dfwOffice = new Office();
        $dfwOffice->setName('Coppell');
        $manager->persist($dfwOffice);

        $ausOffice = new Office();
        $ausOffice->setName('Austin');
        $manager->persist($ausOffice);

        $manager->flush();

        $this->addReference('dfwOffice', $dfwOffice);
        $this->addReference('ausOffice', $ausOffice);
    }

}