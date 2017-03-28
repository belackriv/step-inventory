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

class CheckInventoryAlertsCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->getContainer()->get('app.inventory_alerts')->check();
        $output->writeln('<info>'.
            $result['alertsRun'].' Alerts run, '.
            $result['alertsFound'].' Alerts found, '.
            $result['alertsSent'].' Alerts sent.</info>');
        return 0;
    }
    public function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription("Check all inventory alerts.");

        return true;
    }
    public function getCommandName()
    {
        return 'step-inventory:check-inventory-alerts';
    }
}