<?php
namespace AppBundle\Command;

use AppBundle\Entity\Plan;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFromStripeCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $stripePlanCount = $this->getContainer()->get('app.stripe.plan_collector')->collect();
        $output->writeln('<info>Synced '.$stripePlanCount.' plans.</info>');
        $stripeSubscriptionCount = $this->getContainer()->get('app.stripe.subscription_collector')->collect();
        $output->writeln('<info>Synced '.$stripeSubscriptionCount.' subscriptions.</info>');
        $stripeInvoiceCount = $this->getContainer()->get('app.stripe.invoice_collector')->collect();
        $output->writeln('<info>Synced '.$stripeInvoiceCount.' bills.</info>');
        return 0;
    }
    public function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription("Sync From Stripe.");

        return true;
    }
    public function getCommandName()
    {
        return 'step-inventory:sync-from-stripe';
    }
}