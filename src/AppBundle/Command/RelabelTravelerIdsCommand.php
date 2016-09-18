<?php
namespace AppBundle\Command;

use AppBundle\Entity\MenuLink;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class RelabelTravelerIdsCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tids = $this->getContainer()->get('doctrine')->getRepository('AppBundle:TravelerId')->findAll();
        foreach($tids as $tid){
            $tid->generateLabel();
        }
        $this->getContainer()->get('doctrine')->getManager()->flush();

        $output->writeln('<info>'.count($tids).' Tids relabeled.</info>');
        return 0;
    }
    public function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription("Relabel all tids.");

        return true;
    }
    public function getCommandName()
    {
        return 'step-inventory:relabel-tids';
    }
}