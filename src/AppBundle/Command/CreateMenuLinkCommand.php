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

class CreateMenuLinkCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $menuLink = new MenuLink();
        $menuLink->setName($input->getOption('label'));
        $url = $input->getOption('url')?$input->getOption('url'):null;
        $menuLink->setUrl($url);

        $entityManager->persist($menuLink);
        $entityManager->flush();

        $aclProvider = $this->getContainer()->get('security.acl.provider');

        $roleSecurityIdentity = new RoleSecurityIdentity($input->getOption('role'));
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');

        $objectIdentity = ObjectIdentity::fromDomainObject($menuLink);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($roleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $output->writeln('<info>MenuLink #'.$menuLink->getId().' created.</info>');
        return 0;
    }
    public function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription("Create a Menu Link.")
            ->addOption('label', 'l',  InputOption::VALUE_REQUIRED, "Name/Label for the link")
            ->addOption('url', 'u',  InputOption::VALUE_REQUIRED, "URL for the link")
            ->addOption('role', 'r',  InputOption::VALUE_REQUIRED, "Which Role can view this link");

        return true;
    }
    public function getCommandName()
    {
        return 'stepthrough:create-menu-link';
    }
}