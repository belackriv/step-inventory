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



    /**
     * Converts a base 10 number to any other base.
     *
     * @param int $val   Decimal number
     * @param int $pad   How far to pad string
     * @param int $base  Base to convert to. If null, will use strlen($chars) as base.
     * @param string $chars Characters used in base, arranged lowest to highest. Must be at least $base characters long.
     *
     * @return string    Number converted to specified base
     */
    public static function baseEncode($val, $pad=4, $base=30, $chars='0123456789BCDFGHJKLMNPQRSTVWXZ') {
        if(!isset($base)) $base = strlen($chars);
        $str = '';
        do {
            $m = bcmod($val, $base);
            $str = $chars[$m] . $str;
            $val = bcdiv(bcsub($val, $m), $base);
        } while(bccomp($val,0)>0);

        while(strlen($str)<$pad){
            $str = '0'.$str;
        }
        return $str;
    }

    /**
     * Convert a number from any base to base 10
     *
     * @param string $str   Number
     * @param int $base  Base of number. If null, will use strlen($chars) as base.
     * @param string $chars Characters use in base, arranged lowest to highest. Must be at least $base characters long.
     *
     * @return int    Number converted to base 10
     */
    public static function baseDecode($str, $pad=4, $base=30, $chars='0123456789BCDFGHJKLMNPQRSTVWXZ') {
        if(!isset($base)) $base = strlen($chars);
        $str = ltrim(trim($str), ['0']);
        $len = strlen($str);
        $val = 0;
        $arr = array_flip(str_split($chars));
        for($i = 0; $i < $len; ++$i) {
            $val = bcadd($val, bcmul($arr[$str[$i]], bcpow($base, $len-$i-1)));
        }
        return $val;
    }
}