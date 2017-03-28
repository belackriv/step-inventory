<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\InventoryAlert;

class InventoryAlertsService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

	public function check(InventoryAlert $inventoryAlert = null)
    {
        if($inventoryAlert){
            $inventoryAlerts = [$inventoryAlert];
        }else{
            $inventoryAlerts = $this->container->get('doctrine')->getRepository('AppBundle:InventoryAlert')->findAll();
        }

        $results = [
            'alertsRun' => 0,
            'alertsFound' => 0,
            'alertsSent' => 0,
        ];

        foreach($inventoryAlerts as $alert){
            $results['alertsRun']++;
            $count = $this->container->get('doctrine')->getRepository('AppBundle:InventoryAlert')->hasAlert($alert);
            if($count !== false){
                $results['alertsFound']++;

                $emails = $alert->getUsersEmails();
                if(count($emails) > 0){
                    $body = $this->container->get('templating')
                    ->render('email/inventory-alert.html.twig', ['alert' => $alert, 'inventoryCount' => $count]);

                    $message = \Swift_Message::newInstance()
                        ->setTo($emails)
                        ->setFrom($this->container->getParameter('from_email'))
                        ->setSubject('Inventory Alert')
                        ->setContentType('text/html')
                        ->setBody($body);

                    $results['alertsSent']++;
                }

                $this->container->get('mailer')->send($message);
            }
        }

        return $results;

    }

}