<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class SingleQueryReportParameter {

    /**
     * @ORM\OneToMany(targetEntity="SingleQueryReportParameterPart", mappedBy="singleQueryReportParameter", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"order" = "ASC"})
     * @JMS\Exclude
     */
    //JMS\Type("ArrayCollection<AppBundle\Entity\SingleQueryReportParameterPart>")
    protected $parts;

    public function getParts(){
        return $this->parts;
    }

    public function addPart(SingleQueryReportParameterPart $part)
    {
        $part->setSingleQueryReportParameter($this);
        $this->parts->add($part);
    }

    public function removePart(SingleQueryReportParameterPart $part)
    {
        $part->setSingleQueryReportParameter($this);
        $this->parts->remove($part);
    }

    public function __construct() {
        $this->parts = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
    protected $id;

    public function getId(){
        return $this->id;
    }

   /**
     * @ORM\ManyToOne(targetEntity="SingleQueryReport", inversedBy="singleQueryReportParameters")
     * @JMS\Type("AppBundle\Entity\SingleQueryReport")
     */
    protected $singleQueryReport;

    /**
     * Set singleQueryReport
     *
     * @param \AppBundle\Entity\User $singleQueryReport
     * @return SingleQueryReportCountPart
     */
    public function setSingleQueryReport(SingleQueryReport $singleQueryReport)
    {
        $this->singleQueryReport = $singleQueryReport;
        return $this;
    }

    /**
     * Get singleQueryReport
     *
     * @return \AppBundle\Entity\SingleQueryReport
     */
    public function getSingleQueryReport()
    {
        return $this->singleQueryReport;
    }

    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
    protected $name = null;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
    protected $title = null;

    public function setTitle($title){
        $this->title = $title;
        return $this;
    }

    public function getTitle(){
        return $this->title;
    }

    /**
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     */
    protected $priority = null;

    public function setPriority($priority){
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(){
        return $this->priority;
    }

    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
    protected $type = null;

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function getType(){
        return $this->type;
    }

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isFuzzy = null;

    public function setIsFuzzy($isFuzzy){
        $this->isFuzzy = $isFuzzy;
        return $this;
    }

    public function getIsFuzzy(){
        return $this->isFuzzy;
    }

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isHidden = null;

    public function setIsHidden($isHidden){
        $this->isHidden = $isHidden;
        return $this;
    }

    public function getIsHidden(){
        return $this->isHidden;
    }

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isOptional = null;

    public function setIsOptional($isOptional){
        $this->isOptional = $isOptional;
        return $this;
    }

    public function getIsOptional(){
        return $this->isOptional;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Type("string")
     */
    protected $template = null;

    public function setTemplate($template){
        $this->template = $template;
        return $this;
    }

    public function getTemplate(){
        return $this->template;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Type("string")
     */
    protected $value = null;

    public function setValue($value){
        $this->value = $value;
        return $this;
    }

    public function getValue(){
        return $this->value;
    }

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @JMS\Exclude
     */
    protected $choicesPropertyName = null;

    public function setChoicesPropertyName($choicesPropertyName){
        $this->choicesPropertyName = $choicesPropertyName;
        return $this;
    }

    public function getChoicesPropertyName(){
        return $this->choicesPropertyName;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("choices")
     */
    public function getChoices(){
        if($this->choicesPropertyName and method_exists($this, 'get'.ucfirst($this->choicesPropertyName))){
            return call_user_func([$this, 'get'.ucfirst($this->choicesPropertyName)]);
        }else{
            return null;
        }
    }

    public function setChoices(ContainerInterface $container){
        if($this->choicesPropertyName and method_exists($this, 'set'.ucfirst($this->choicesPropertyName))){
            call_user_func([$this, 'set'.ucfirst($this->choicesPropertyName)], $container);
        }
    }

    public function getParameterValue($params){
        if($this->value !== null){
            $paramValue = $this->value;
        }else{
            $paramValue = isset($params[$this->name])?$params[$this->name]:null;
            if(!$this->isOptional and $paramValue === null){
                throw new HttpException(422, 'Report Parameter "'.$this->name.'" not set');
            }
        }
        if($this->isFuzzy){
            return '%'.(string)$paramValue.'%';
        }else{
            return $this->castValueToType($paramValue);
        }
    }

    protected function castValueToType($value){
        if(empty($value)){
            return null;
        }
        switch( $this->type ){
            case 'string':
                return (string)$value;
                break;
            case 'int':
            case 'integer':
                return (integer)$value;
                break;
            case 'float':
                return (float)$value;
                break;
            case 'number':
                return (string)$value;
                break;
            case 'bool':
            case 'boolean':
                if($value==='false'){
                    return false;
                }
                return (boolean)$value;
                break;
            case 'date':
            case 'time':
            case 'datetime':
                return new \DateTime($value);
                break;
            default:
                //return $GLOBALS['JMS_serializer']->deserialize($value, $type, 'json');
                return $value;
                break;
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        $this->setCreatedAt(new DateTime)
            ->setUpdatedAt(new DateTime)
            ->setStatus(Constants::ENABLED);
    }

    /**
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->setUpdatedAt(new DateTime);
    }


    //choices functions
    public function getClientsChoiceList()
    {
        //must be set ahead of time
        if(property_exists($this, 'customersChoicesList') and is_array($this->clientsChoicesList)){
            return $this->clientsChoicesList;
        }else{
            return null;
        }
    }

    //choices functions
    public function setClientsChoiceList(ContainerInterface $container)
    {
        $qb = $container->get('doctrine')->getManager()->createQueryBuilder()
            ->select('c.id value, c.name label')
            ->from('AppBundle:Client', 'c')
            ->join('c.organization', 'org')
            ->where('org.id = :org_id')
            ->orderBy('c.name', 'ASC')
            ->setParameter('org_id', $container->get('security.token_storage')->getToken()->getUser()->getOrganization()->getId());

        $this->clientsChoicesList = $qb->getQuery()->getResult();
    }

    //choices functions
    public function getCustomersChoiceList()
    {
        //must be set ahead of time
        if(property_exists($this, 'customersChoicesList') and is_array($this->customersChoicesList)){
            return $this->customersChoicesList;
        }else{
            return null;
        }
    }

    //choices functions
    public function setCustomersChoiceList(ContainerInterface $container)
    {
        $qb = $container->get('doctrine')->getManager()->createQueryBuilder()
            ->select('c.id value, c.name label')
            ->from('AppBundle:Customer', 'c')
            ->join('c.organization', 'org')
            ->where('org.id = :org_id')
            ->orderBy('c.name', 'ASC')
            ->setParameter('org_id', $container->get('security.token_storage')->getToken()->getUser()->getOrganization()->getId());

        $this->customersChoicesList = $qb->getQuery()->getResult();
    }

}