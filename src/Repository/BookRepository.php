<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function search(array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->addSelect('a')                    // On évite le N+1 sur authors
            ->leftJoin('b.authors', 'a')
            ->orderBy('b.title', 'ASC');

        if (!empty($criteria['q'])) {
            $qb->andWhere('b.title LIKE :q')
                ->setParameter('q', '%'.$criteria['q'].'%');
        }

        if (!empty($criteria['author'])) {
            $qb->andWhere('a.name LIKE :author')
                ->setParameter('author', '%'.$criteria['author'].'%');
        }

        if (!empty($criteria['available'])) {
            $qb->andWhere('b.available = true');
        }

        return $qb->getQuery()->getResult();
    }
}
