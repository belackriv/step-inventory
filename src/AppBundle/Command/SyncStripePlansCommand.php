<?php
namespace AppBundle\Command;

use AppBundle\Entity\Plan;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncStripePlansCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $stripePlanCount = $this->getContainer()->get('app.stripe.plan_collector')->collect();
        $output->writeln('<info>Synced '.$stripePlanCount.' plans.</info>');
        return 0;
    }
    public function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription("Sync Plans with Stripe.");

        return true;
    }
    public function getCommandName()
    {
        return 'step-inventory:sync-stripe-plans';
    }
}