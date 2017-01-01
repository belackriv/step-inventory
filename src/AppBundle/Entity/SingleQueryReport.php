<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class SingleQueryReport
{

	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
	protected $id = null;

	public function getId()
	{
		return $this->id;
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
	 * @ORM\Column(type="text")
     * @JMS\Type("string")
     */
	protected $tag = null;

	public function getTag()
	{
		return $this->tag;
	}

	public function setTag($tag)
	{
		$this->tag = $tag;
		return $this;
	}

    /**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isEnabled = true;

	public function getIsEnabled()
	{
		return $this->isEnabled;
	}

	public function setIsEnabled($isEnabled)
	{
		$this->isEnabled = $isEnabled;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
	protected $fileName = null;

	public function getFileName()
	{
		return $this->fileName;
	}

	public function setFileName($fileName)
	{
		$this->fileName = $fileName;
		return $this;
	}


	/**
	 * @ORM\Column(type="json_array")
     * @JMS\Type("array")
     */
	protected $columns = [];

	public function getColumns(){
		return $columns;
	}

	public function getColumnLabelByName($name){
		foreach($this->getColumns() as $col){
			if($col['name'] == $name){
				return $col['label'];
			}
		}
		return null;
	}

	public function setColumns($columns){
		$this->columns = $columns;
		return $this;
	}

	/**
	 * @ORM\Column(type="text", nullable=true)
     * @JMS\Exclude
     */
    //JMS\Type("string")
	protected $parameterWhiteList = null;

	public function getParameterWhiteList(){
		return $this->parameterWhiteList;
	}

		public function setParameterWhiteList($parameterWhiteList){
		if($this->parameterWhiteList === null){
			$this->parameterWhiteList = $parameterWhiteList;
			return $this;
		}else if($parameterWhiteList != $this->parameterWhiteList){
			throw new \Exception('Cannot Change the ParameterWhiteList in a SingleQueryReport');
		}
	}

	/**
     * @ORM\OneToMany(targetEntity="SingleQueryReportRole", mappedBy="singleQueryReport", cascade={"all"}, orphanRemoval=true)
     * @JMS\Type("ArrayCollection<AppBundle\Entity\SingleQueryReportRole>")
     */
	 protected $singleQueryReportRoles;

   	public function getSingleQueryReportRoles(){
   		return $this->singleQueryReportRoles;
   	}

   	public function addSingleQueryReportRole(SingleQueryReportRole $singleQueryReportRole)
    {
		$singleQueryReportRole->setSingleQueryReport($this);
    	$this->singleQueryReportRoles->add($singleQueryReportRole);
    }


    public function removeSingleQueryReportRole(SingleQueryReportRole $singleQueryReportRole)
    {
        $singleQueryReportRole->setSingleQueryReport(null);
        $this->singleQueryReportRoles->remove($singleQueryReportRole);
    }

    /**
     * @ORM\OneToMany(targetEntity="SingleQueryReportParameter", mappedBy="singleQueryReport", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"priority" = "ASC"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\SingleQueryReportParameter>")
     */
    protected $singleQueryReportParameters;

   	public function getSingleQueryReportParameters(){
   		return $this->singleQueryReportParameters;
   	}

   	public function addSingleQueryReportParameter(SingleQueryReportParameter $singleQueryReportParameter)
    {
		$singleQueryReportParameter->setSingleQueryReport($this);
    	$this->singleQueryReportParameters->add($singleQueryReportParameter);
    }


    public function removeSingleQueryReportParameter(SingleQueryReportParameter $singleQueryReportParameter)
    {
        $singleQueryReportParameter->setSingleQueryReport(null);
        $this->singleQueryReportParameters->remove($singleQueryReportParameter);
    }

    /**
     * @ORM\OneToMany(targetEntity="SingleQueryReportPart", mappedBy="singleQueryReport", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"order" = "ASC"})
     * @JMS\Exclude
     */
    //JMS\Type("ArrayCollection<AppBundle\Entity\SingleQueryReportPart>")
    protected $parts;

    public function getParts(){
        return $this->parts;
    }

    public function addPart(SingleQueryReportPart $part)
    {
        $part->setSingleQueryReport($this);
        $this->parts->add($part);
    }

    public function removePart(SingleQueryReportPart $part)
    {
        $part->setSingleQueryReport(null);
        $this->parts->remove($part);
    }

    /**
     * @ORM\OneToMany(targetEntity="SingleQueryReportCountPart", mappedBy="singleQueryReport", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"order" = "ASC"})
     * @JMS\Exclude
     */
    //JMS\Type("ArrayCollection<AppBundle\Entity\SingleQueryReportCountPart>")
    protected $countParts;

    public function getCountParts(){
        return $this->countParts;
    }

    public function addCountPart(SingleQueryReportCountPart $part)
    {
        $part->setSingleQueryReport($this);
        $this->countParts->add($part);
    }

    public function removeCountPart(SingleQueryReportCountPart $part)
    {
        $part->setSingleQueryReport($this);
        $this->countParts->remove($part);
    }

    public function __construct() {
        $this->singleQueryReportParameters = new ArrayCollection();
        $this->parts = new ArrayCollection();
        $this->countParts = new ArrayCollection();
    }

    public function setChoices(ContainerInterface $container)
    {
    	foreach($this->singleQueryReportParameters as $param){
    		$param->setChoices($container);
    	}
    }

    public function run(ContainerInterface $container, Request $request)
    {
        $mainQuery = $this->getMainQuery($container, $request);
        $countQuery = $this->getCountQuery($container, $request);
		$page = (int)$request->query->get('page') - 1;
		$perPage =(int)$request->query->get('per_page');
		$mainQuery->setMaxResults($perPage)->setFirstResult($page*$perPage);
        return [
    		'columns' => $this->columns,
    		'total_count' => count($mainQuery->getResult()),
            'total_items' =>(int)$countQuery->getSingleScalarResult(),
    		'list' => $mainQuery->getResult()
    	];
    }

    public function export(ContainerInterface $container, Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit','2048M');
    	$query = $this->getMainQuery($container, $request);
        return [
    		'columns' => $this->columns,
    		'data' => $query->getResult()
    	];
    }

    private function getMainQuery(ContainerInterface $container, Request $request)
    {
        $qb = $container->get('doctrine')->getManager()->createQueryBuilder();
        $this->buildQuery($qb, $this->parts);
        $this->addSecurityQueryParts($qb, $container);
        $this->assignQueryParamters($qb, $request);
        $this->assignSecurityParamaters($qb, $container);
        return $qb->getQuery();
    }

    private function getCountQuery(ContainerInterface $container, Request $request)
    {
        $countQb = $container->get('doctrine')->getManager()->createQueryBuilder();
        $this->buildQuery($countQb, $this->countParts);
        $this->addSecurityQueryParts($countQb, $container);
        $this->assignQueryParamters($countQb, $request);
        $this->assignSecurityParamaters($countQb, $container);
        return $countQb->getQuery();
    }

    private function buildQuery(\Doctrine\ORM\QueryBuilder $qb, $parts)
    {
        foreach($parts as $part){
            call_user_func_array([$qb, $part->getMethodName()],$part->getArgs());
        }
    }

    private function assignQueryParamters(\Doctrine\ORM\QueryBuilder $qb, Request $request)
    {
    	foreach($this->singleQueryReportParameters as $param){
        	$paramValue = $param->getParameterValue($request->query->all());
        	if($param->getIsOptional() === false or $paramValue !== null){
        		$this->buildQuery($qb, $param->getParts());
	        }
        }
        foreach($this->singleQueryReportParameters as $param){
            $paramValue = $param->getParameterValue($request->query->all());
            if($param->getIsOptional() === false or $paramValue !== null){
                $qb->setParameter($param->getName(), $paramValue);
            }
        }
    }

    private function addSecurityQueryParts(\Doctrine\ORM\QueryBuilder $qb, ContainerInterface $container)
    {
        $qb->andWhere('org.id = :org_id');
    }

    private function assignSecurityParamaters(\Doctrine\ORM\QueryBuilder $qb, ContainerInterface $container)
    {
        $qb->setParameter('org_id', $container->get('security.token_storage')->getToken()->getUser()->getOrganization()->getId());
    }

}

