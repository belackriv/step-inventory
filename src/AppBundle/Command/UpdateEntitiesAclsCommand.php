<?php
namespace AppBundle\Command;

use AppBundle\Library\Service\AclUpdaterService;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class UpdateEntitiesAclsCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $entityName = $input->getOption('entityName');
        $roleMasksMap = json_decode($input->getOption('roleMasksMap'));
        $output->writeln('<info>Updating '.$entityName.' to '.json_encode($roleMasksMap).'.</info>');

        $aclUpdaterService = new AclUpdaterService($this->getContainer());

        $entities = $this->getContainer()->get('doctrine')->getRepository('AppBundle:'.$entityName)->findAll();
        foreach($entities as $entity){
            $aclUpdaterService->updateAclByRoles($entity, $roleMasksMap);
        }
        $output->writeln('<info>'.count($entities).' ACL Created/Updated.</info>');
        return 0;
    }

    public function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription("Create ACL for Entities.")
            ->addOption('entityName', null, InputOption::VALUE_REQUIRED, "Entity Name")
            ->addOption('roleMasksMap', 'r', InputOption::VALUE_REQUIRED, "Role Masks Map JSON(no spaces)");
        return true;
    }

    public function getCommandName()
    {
        return 'stepthrough:update-entities-acls';
    }
}