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
use AppBundle\Entity\InboundOrder;
use AppBundle\Entity\OutboundOrder;
use AppBundle\Entity\TravelerId;
use AppBundle\Entity\SalesItem;


class MassImportAndExportService
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
        ]
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

    public function export($type, $options = [])
    {
        $exportMethodName = 'export'.ucfirst($type);
        if(!method_exists($this, $exportMethodName)){
            throw new \Exception("Export for '".$type."'' not supported.");
        }
        return $this->$exportMethodName($options);
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

    private function getEntities(\Doctrine\ORM\QueryBuilder $qb, $class)
    {
        $data = [];
        $row = ['id'];
        foreach(self::$allowedFields[$class] as $field){
            $row[] = $field['name'];
        }
        $data[] = $row;
        $entities = $qb->getQuery()->getResult();
        foreach($entities as $entity){
            $row = [$entity->getId()];
            foreach(self::$allowedFields[$class] as $field){
                $getMethodName = 'get'.ucfirst($field['name']);
                if(!method_exists($entity, $getMethodName)){
                    throw new \Exception("No get Method found for property '".$field['name']."'.");
                }
                $value = $entity->$getMethodName();
                if(is_object($value)){
                    if(method_exists($value, 'getId')){
                        $value = $value->getId();
                    }else{
                        $value = 'N/A';
                    }
                }
                $row[] = (string)$value;
            }
            $data[] = $row;
        }
        return $data;
    }

    private function getUser()
    {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

    private function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    private function getEntityInstance(\stdClass $classData, $className)
    {
        if(property_exists($classData, 'id')){
            return $this->getEntity($className, $classData->id);
        }else{
            return new $className;
        }

    }

    private function importPart(\stdClass $partData)
    {
        $this->propertiesSet = [];
        $part = $this->getEntityInstance($partData, Part::class);
        $this->setEntityProperties($partData, $part);
        $this->checkForMissedRequiredProperties($part);
        $part->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($part);
        return $part;
    }

    private function importBin(\stdClass $binData)
    {
        $this->propertiesSet = [];
        $bin = $this->getEntityInstance($binData, Bin::class);
        $this->setEntityProperties($binData, $bin);
        $this->checkForMissedRequiredProperties($bin);
        $bin->setIsLocked(false);
        $this->getEntityManager()->persist($bin);
        return $bin;
    }

    private function importSku(\stdClass $skuData)
    {
        $this->propertiesSet = [];
        $sku = $this->getEntityInstance($skuData, Sku::class);
        $this->setEntityProperties($skuData, $sku);
        $this->checkForMissedRequiredProperties($sku);
        $sku->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($sku);
        return $sku;
    }

    private function importCommodity(\stdClass $commodityData)
    {
        $this->propertiesSet = [];
        $commodity = $this->getEntityInstance($commodityData, Commodity::class);
        $this->setEntityProperties($commodityData, $commodity);
        $this->checkForMissedRequiredProperties($commodity);
        $commodity->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($commodity);
        return $commodity;
    }

    private function importUnitType(\stdClass $unitTypeData)
    {
        $this->propertiesSet = [];
        $unitType = $this->getEntityInstance($unitTypeData, UnitType::class);
        $this->setEntityProperties($unitTypeData, $unitType);
        $this->checkForMissedRequiredProperties($unitType);
        $unitType->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($unitType);
        return $unitType;
    }

    private function importClient(\stdClass $clientData)
    {
        $this->propertiesSet = [];
        $client = $this->getEntityInstance($clientData, Client::class);
        $this->setEntityProperties($clientData, $client);
        $this->checkForMissedRequiredProperties($client);
        $client->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($client);
        return $client;
    }

    private function importCustomer(\stdClass $customerData)
    {
        $this->propertiesSet = [];
        $customer = $this->getEntityInstance($customerData, Customer::class);
        $this->setEntityProperties($customerData, $customer);
        $this->checkForMissedRequiredProperties($customer);
        $customer->setOrganization($this->getUser()->getOrganization());
        $this->getEntityManager()->persist($customer);
        return $customer;
    }

    public function exportPart()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Part::class, 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, Part::class);
    }

    public function exportBin()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Bin::class, 'e')
            ->join('e.binType', 'bt')
            ->where('bt.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, Bin::class);
    }

    public function exportSku()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Sku::class, 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, Sku::class);
    }

    public function exportCommodity()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Commodity::class, 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, Commodity::class);
    }

    public function exportUnitType()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(UnitType::class, 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, UnitType::class);
    }

    public function exportClient()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Client::class, 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, Client::class);
    }

    public function exportCustomer()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Customer::class, 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());
        return $this->getEntities($qb, Customer::class);
    }

    public function exportInboundOrderManifest(array $options)
    {
        if( !array_key_exists('inboundOrder', $options) or
            get_class($options['inboundOrder']) !== InboundOrder::class
        ){
            throw new \Exception("Must Supply an 'inboundOrder' option.");
        }
        $entities = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(TravelerId::class, 'e')
            ->where('e.inboundOrder = :ion')
            ->setParameter('ion', $options['inboundOrder'])
            ->getQuery()->getResult();
        $data = [[
            'TravelerId',
            'IsVoid',
            'IsTransformed',
            'TransformedTo',
            'Bin',
            'SKU',
            'QTY',
            'SKU QTY'
        ]];
        foreach($entities as $tid){
            $isVoid = $tid->getIsVoid()?'true':'false';
            $isTransformed = 'false';
            $transformedTo = '';
            if($tid->getTransform() and $tid->getTransform()->getIsVoid() === false){
                $isTransformed = 'true';
                $transformedTo = [];
                foreach($tid->getTransform()->getToTravelerIds() as $transformTid){
                    $transformedTo[] = $transformTid->getLabel();
                }
                foreach($tid->getTransform()->getToSalesItems() as $transformSI){
                    $transformedTo[] = $transformSI->getLabel();
                }
                $transformedTo = implode(';', $transformedTo);
            }
            $data[] = [
                $tid->getLabel(),
                $isVoid,
                $isTransformed,
                $transformedTo,
                $tid->getBin()->getName(),
                $tid->getSku()->getName(),
                $tid->getQuantity(),
                $tid->getSku()->getQuantity()
            ];
        }
        return $data;
    }

    public function exportOutboundOrderManifest(array $options)
    {
        if( !array_key_exists('outboundOrder', $options) or
            get_class($options['outboundOrder']) !== OutboundOrder::class
        ){
            throw new \Exception("Must Supply an 'outboundOrder' option.");
        }
        $entities = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(SalesItem::class, 'e')
            ->where('e.outboundOrder = :oon')
            ->setParameter('oon', $options['outboundOrder'])
            ->getQuery()->getResult();
        $data = [[
            'SalesItem',
            'IsVoid',
            'Bin',
            'SKU',
            'QTY',
            'SKU QTY'
        ]];
        foreach($entities as $si){
            $isVoid = $si->getIsVoid()?'true':'false';
            $data[] = [
                $si->getLabel(),
                $isVoid,
                $si->getBin()->getName(),
                $si->getSku()->getName(),
                $si->getQuantity(),
                $si->getSku()->getQuantity()
            ];
        }
        return $data;
    }

}