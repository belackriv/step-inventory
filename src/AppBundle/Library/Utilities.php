<?php

namespace AppBundle\Library;

class Utilities
{

	public static function setupSearchableEntityQueryBuild(\Doctrine\ORM\QueryBuilder $qb, \Symfony\Component\HttpFoundation\Request $request)
    {
        $terms = $request->query->get('terms');
        $search = $request->query->get('search');
        $baseEntityName = $qb->getRootAliases()[0];
        if($terms){
            $termsArray = explode(',', $terms);
        }
        if($search){
            $searchArray = explode(',', $search);
        }
        $expr = $qb->expr()->orX();
        if(!empty($terms)){
            foreach($searchArray as $searchablePropertyPath){
                if(!empty($searchablePropertyPath)){
                    $searchablePropertyName = str_replace('.', '_', $searchablePropertyPath);
                    $searchablePropertyPathArray = explode('.', $searchablePropertyPath);
                    if(count($searchablePropertyPathArray) > 1){
                        $lastPathPart = $baseEntityName;
                        foreach($searchablePropertyPathArray as $pathIndex => $pathPart){
                            if($pathIndex < (count($searchablePropertyPathArray) - 1) ){
                                $searchableProperty = $lastPathPart.'.'.$pathPart;
                                if(!self::doesJoinAExistOnQueryBuilder($qb, $searchableProperty)){
                                    if(!self::doesJoinAliasExistOnQueryBuilder($qb, $pathPart)){
                                        $qb->leftJoin($searchableProperty, $pathPart);
                                        $lastPathPart = $pathPart;
                                    }else{
                                        $pathPart = self::incrementQueryAlias($pathPart);
                                        $qb->leftJoin($searchableProperty, $pathPart);
                                        $lastPathPart = $pathPart;
                                    }
                                }
                            }
                        }
                        $searchableProperty = $lastPathPart.'.'. $searchablePropertyPathArray[count($searchablePropertyPathArray)-1];
                    }else{
                        $searchableProperty = $baseEntityName.'.'.$searchablePropertyPath;
                    }
                    foreach($termsArray as $term){
                        $parameterName = preg_replace('/[^a-zA-Z0-9]+/', '_', $searchablePropertyName.'_'.$term);
                        $expr->add('LOWER('.$searchableProperty.') LIKE :'.$parameterName);
                        $qb->setParameter($parameterName, '%'.strtolower($term).'%');
                    }
                }
            }
        }
        if($expr->count() > 0 ){
            $qb->andWhere($expr);
        }
    }
    public static function doesJoinAExistOnQueryBuilder(\Doctrine\ORM\QueryBuilder $qb, $join)
    {
        $joinDqlParts = $qb->getDQLPart('join');
        foreach ($joinDqlParts as $joins) {
            foreach ($joins as $join) {
                if ($join->getJoin() === $join) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function doesJoinAliasExistOnQueryBuilder(\Doctrine\ORM\QueryBuilder $qb, $alias)
    {
        $joinDqlParts = $qb->getDQLPart('join');
        foreach ($joinDqlParts as $joins) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $alias) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function incrementQueryAlias($alias)
    {
        $matches = [];
        if(preg_match('/\_(\d)$/', $alias, $matches)){
            $newCount = (int)str_replace('_', '', $matches[0]) + 1;
            return substr($alias, 0, strlen($alias) - strlen($matches[0])) . '_'.$newCount;
        }else{
            return $alias . '_2';
        }
    }
}