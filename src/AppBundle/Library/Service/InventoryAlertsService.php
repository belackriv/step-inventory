<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\InventoryAlert;
use AppBundle\Entity\InventoryAlertLog;

class InventoryAlertsService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use \AppBundle\Controller\Mixin\UpdateAclMixin;

	public function check(InventoryAlert $inventoryAlert = null)
    {
        $em = $this->container->get('doctrine')->getManager();
        if($inventoryAlert){
            $inventoryAlerts = [$inventoryAlert];
        }else{
            $inventoryAlerts = $this->container->get('doctrine')->getRepository('AppBundle:InventoryAlert')->findAll();
        }

        $results = [
            'alertsRun' => 0,
            'alertsFound' => 0,
            'alertsSent' => 0,
            'alertLogs' => []
        ];

        foreach($inventoryAlerts as $alert){
            if($alert->getIsActive()){
                $results['alertsRun']++;
                $count = $this->container->get('doctrine')->getRepository('AppBundle:InventoryAlert')->hasAlert($alert);
                if($count !== false){
                    $results['alertsFound']++;

                    $inventoryAlertLog = new InventoryAlertLog;
                    $inventoryAlertLog->setInventoryAlert($alert);
                    $inventoryAlertLog->setPerformedAt(new \DateTime);
                    $inventoryAlertLog->setIsActive(true);
                    $inventoryAlertLog->setCount($count);
                    $em->persist($inventoryAlertLog);
                    $results['alertLogs'][] = $inventoryAlertLog;

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
        }

        $em->flush();
        foreach($results['alertLogs'] as $alertLog){
            $this->updateAclByRoles($alertLog, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
        }
        return $results;

    }

}