<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\MassImport;
use AppBundle\Entity\Part;
use AppBundle\Entity\Bin;
use AppBundle\Entity\Sku;
use AppBundle\Entity\Commodity;
use AppBundle\Entity\UnitType;
use AppBundle\Entity\Client;
use AppBundle\Entity\Customer;


class MassImporterService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use \AppBundle\Controller\Mixin\UpdateAclMixin;

    public static $allowedFields = [
        Part::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
            ['name' => 'partId', 'required' => false, 'type' => 'string'],
            ['name' => 'partAltId', 'required' => false, 'type' => 'string'],
            ['name' => 'description', 'required' => false, 'type' => 'string'],
            ['name' => 'partCategory', 'required' => false, 'type' => 'entity', 'class' => \AppBundle\Entity\PartCategory::class],
            ['name' => 'partGroup', 'required' => false, 'type' => 'entity', 'class' => \AppBundle\Entity\PartGroup::class],
            ['name' => 'isActive', 'required' => true, 'type' => 'boolean'],
        ],
        Bin::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
            ['name' => 'description', 'required' => false, 'type' => 'string'],
            ['name' => 'department', 'required' => true, 'type' => 'entity', 'class' => \AppBundle\Entity\Department::class],
            ['name' => 'partCategory', 'required' => false, 'type' => 'entity', 'class' => \AppBundle\Entity\PartCategory::class],
            ['name' => 'binType', 'required' => true, 'type' => 'entity', 'class' => \AppBundle\Entity\BinType::class],
            ['name' => 'parent', 'required' => false, 'type' => 'entity', 'class' => Bin::class],
            ['name' => 'isActive', 'required' => true, 'type' => 'boolean'],
        ],
        Sku::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
            ['name' => 'number', 'required' => true, 'type' => 'string'],
            ['name' => 'label', 'required' => true, 'type' => 'string'],
            ['name' => 'supplierCode', 'required' => false, 'type' => 'string'],
            ['name' => 'supplierSku', 'required' => false, 'type' => 'string'],
            ['name' => 'part', 'required' => false, 'type' => 'entity', 'class' => Part::class],
            ['name' => 'commodity', 'required' => false, 'type' => 'entity', 'class' => Commodity::class],
            ['name' => 'unitType', 'required' => false, 'type' => 'entity', 'class' => UnitType::class],
            ['name' => 'isVoid', 'required' => true, 'type' => 'boolean'],
            ['name' => 'quantity', 'required' => true, 'type' => 'integer'],
        ],
        Commodity::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
            ['name' => 'commodityId', 'required' => false, 'type' => 'string'],
            ['name' => 'commodityAltId', 'required' => false, 'type' => 'string'],
            ['name' => 'description', 'required' => false, 'type' => 'string'],
            ['name' => 'isActive', 'required' => true, 'type' => 'boolean'],
        ],
        UnitType::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
            ['name' => 'manufacturer', 'required' => false, 'type' => 'string'],
            ['name' => 'model', 'required' => false, 'type' => 'string'],
            ['name' => 'description', 'required' => false, 'type' => 'string'],
            ['name' => 'isActive', 'required' => true, 'type' => 'boolean'],
        ],
        Client::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
        ],
        Customer::class => [
            ['name' => 'name', 'required' => true, 'type' => 'string'],
        ],
    ];

    private $propertiesSet = [];

    public function import(MassImport $massImport)
    {
        $createdEnities = [];
        foreach($massImport->getItems() as $itemData){
            $importMethodName = 'import'.ucfirst($massImport->type);
            if(!method_exists($this, $importMethodName)){
                throw new \Exception("Import for '".$massImport->getType()."'' not supported.");
            }
            $entity = $this->$importMethodName($itemData);
            $createdEnities[] = $entity;
        }
        $this->getEntityManager()->flush();

        foreach ($createdEnities as $entity) {
            $this->updateAclByRoles($entity, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
        }
        return $createdEnities;
    }

    private function setEntityProperties(\stdClass $entityData, $entity)
    {
        foreach ($entityData as $name => $value){
            if($this->isImportSupported($name, $entity)){
                $setMethodName = 'set'.ucfirst($name);
                if(!method_exists($entity, $setMethodName)){
                    throw new \Exception("No set Method found for property '".$name."'.");
                }
                $castedValue = $this->getCastedValue($entity, $name, $value);
                $entity->$setMethodName($castedValue);
                if($castedValue !== null){
                    $this->propertiesSet[] = $name;
                }
            }
        }
    }

    private function isImportSupported($name, $entity)
    {
        if(isset(self::$allowedFields[get_class($entity)])){
            foreach(self::$allowedFields[get_class($entity)] as $importInfo){
                if($importInfo['name'] === $name){
                    return true;
                }
            }
        }else{
            throw new \Exception("Import for '".get_class($entity)."' not supported.");
        }
        return false;
    }

    private function getCastedValue($entity, $name, $value)
    {
        $importInfo = null;
        if(isset(self::$allowedFields[get_class($entity)])){
            foreach(self::$allowedFields[get_class($entity)] as $localImportInfo){
                if($localImportInfo['name'] === $name){
                    $importInfo = $localImportInfo;
                }
            }
        }else{
            throw new \Exception("Import for '".get_class($entity)."' not supported.");
        }
        if($importInfo){
            switch($importInfo['type']) {
                case 'string':
                    return (string)$value;
                case 'integer':
                    return (integer)$value;
                case 'boolean':
                    return (boolean)$value;
                case 'entity':
                    return $this->getEntity($importInfo['class'], $value);
                default:
                    return $value;
            }
        }else{
            throw new \Exception("Import for '".get_class($entity)."'.'".$name."' not supported.");
        }
    }

    private function getEntity($class, $value)
    {
        return $this->container->get('doctrine')->getRepository($class)->findOneById($value);
    }

    private function checkForMissedRequiredProperties($entity)
    {
        if(isset(self::$allowedFields[get_class($entity)])){
            foreach(self::$allowedFields[get_class($entity)] as $importInfo){
                if($importInfo['required'] and !in_array($importInfo['name'], $this->propertiesSet)){
                    throw new \Exception("Missing required property '".$importInfo['name']."'.");
                }
            }
        }else{
            throw new \Exception("Import for '".get_class($entity)."' not supported.");
        }
        return true;
    }

    private function getUser()
    {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

    private function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    private function importPart(\stdClass $partData)
    {
        $this->propertiesSet = [];
        $part = new Part();
        $this->setEntityProperties($partData, $part);
        $this->checkForMissedRequiredProperties($part);
        $part->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($part);
        return $part;
    }

    private function importBin(\stdClass $binData)
    {
        $this->propertiesSet = [];
        $bin = new Bin();
        $this->setEntityProperties($binData, $bin);
        $this->checkForMissedRequiredProperties($bin);
        $bin->setIsLocked(false);
        $this->getEntityManager()->persist($bin);
        return $bin;
    }

    private function importSku(\stdClass $skuData)
    {
        $this->propertiesSet = [];
        $sku = new Sku();
        $this->setEntityProperties($skuData, $sku);
        $this->checkForMissedRequiredProperties($sku);
        $sku->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($sku);
        return $sku;
    }

    private function importCommodity(\stdClass $commodityData)
    {
        $this->propertiesSet = [];
        $commodity = new Commodity();
        $this->setEntityProperties($commodityData, $commodity);
        $this->checkForMissedRequiredProperties($commodity);
        $commodity->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($commodity);
        return $commodity;
    }

    private function importUnitType(\stdClass $unitTypeData)
    {
        $this->propertiesSet = [];
        $unitType = new UnitType();
        $this->setEntityProperties($unitTypeData, $unitType);
        $this->checkForMissedRequiredProperties($unitType);
        $unitType->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($unitType);
        return $unitType;
    }

    private function importClient(\stdClass $clientData)
    {
        $this->propertiesSet = [];
        $client = new Client();
        $this->setEntityProperties($clientData, $client);
        $this->checkForMissedRequiredProperties($client);
        $client->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($client);
        return $client;
    }

    private function importCustomer(\stdClass $customerData)
    {
        $this->propertiesSet = [];
        $customer = new Customer();
        $this->setEntityProperties($customerData, $customer);
        $this->checkForMissedRequiredProperties($customer);
        $customer->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($customer);
        return $customer;
    }
}