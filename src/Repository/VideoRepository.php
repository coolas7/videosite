<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);

        $this->paginator = $paginator;
    }

    public function add(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByChildIds(array $value, int $page, ?string $sort_method)
    {
        if ($sort_method != 'rating') 
        {

            $dbquery = $this->createQueryBuilder('v')
            ->andWhere('v.category IN (:val)')
            ->leftJoin('v.comments', 'c')
            // ->addSelect('c')
            ->leftJoin('v.usersThatLike', 'l')
            ->leftJoin('v.usersThatDontLike', 'd')
            ->addSelect('c','l','d')
            ->setParameter('val', $value)
            ->orderBy('v.title', $sort_method);

        }
        else
        {

            $dbquery = $this->createQueryBuilder('v')
            ->addSelect('COUNT(l) AS HIDDEN likes')
            ->leftJoin('v.usersThatLike', 'l')
            ->andWhere('v.category IN (:val)')
            ->setParameter('val', $value)
            ->groupBy('v')
            ->orderBy('likes', 'DESC');

        }

        $dbquery->getQuery();

        $pagination = $this->paginator->paginate($dbquery, $page, Video::perPage);

        return $pagination;

    }

    public function findByTitle(string $query, int $page, ?string $sort_method)
    {

        $queryBuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term) {
            
            $queryBuilder->orWhere('v.title LIKE :t_'.$key)
            ->setParameter('t_'.$key, '%'.trim($term).'%');

        }

        if ($sort_method != 'rating') {

            $dbquery = $queryBuilder
            ->orderBy('v.title', $sort_method)
            ->leftJoin('v.comments', 'c')
            ->leftJoin('v.usersThatLike', 'l')
            ->leftJoin('v.usersThatDontLike', 'd')
            ->addSelect('c','l','d')
            ->getQuery();

        }
        else
        {
            $dbquery = $queryBuilder
            ->addSelect('COUNT(l) AS HIDDEN likes', 'c')
            ->leftJoin('v.usersThatLike', 'l')
            ->leftJoin('v.comments', 'c')
            ->groupBy('v', 'c')
            ->orderBy('likes', 'DESC')
            ->getQuery();

        }


        return $this->paginator->paginate($dbquery, $page, Video::perPage);



    }

    private function prepareQuery(string $query): array
    {
        $terms = array_unique(explode(' ', $query));

        return array_filter($terms, function($term){
            return 2 <= mb_strlen($term);
        });
    }
    

    public function videoDetails($id) 
    {

        return $this->createQueryBuilder('v')
        ->leftJoin('v.comments', 'c')
        ->leftJoin('c.user', 'u')
        ->addSelect('c','u')
        ->where('v.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();

    }

//    /**
//     * @return Video[] Returns an array of Video objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Video
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
