<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
    public function findAllByAuthorId($id)
    {
        $req = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->where('a.id= :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
        return $req;
    }
    public function findAllByAuthorUsername()
    {
        $req = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
        return $req;
    }
    public function findByRef($ref)
    {
        $req = $this->createQueryBuilder('b')
            ->where('b.ref=:ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
        return $req;
    }
    /*public function findAllByNbBooks($nbBooks){
        $req= $this->createQueryBuilder('b')
            ->join('b.author','a')
            ->where('a.getNbBooks() > : nbBooks')
            ->setParameter('nbBooks',$nbBooks)
            ->getQuery()          
            ->getResult() ;
        return $req;
    }
    public function findAllByPublicationDate($date){
        $req=$this->createQueryBuilder('b')
        ->where('b.publicationDate < :date')
        ->setParameter('date',$date)
        ->getQuery()          
        ->getResult() ;
        return $req;
    }*/
    public function findAllByPublicationDateAndNbBooks($date, $nbreOfBooks)
    {
        $req = $this->createQueryBuilder('b')
            ->where('b.publicationDate < :date')
            ->setParameter('date', $date)
            ->join('b.author', 'a')
            ->Andwhere('a.nb_books > :nbreOfBooks')
            ->setParameter('nbreOfBooks', $nbreOfBooks - 1)
            ->getQuery()
            ->getResult();
        return $req;
    }
    public function findSumBooksWithScienceFiction($category)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(b) FROM App\Entity\Book b WHERE b.category = :categ')
            ->setParameter('categ', $category);

        return $query->getSingleScalarResult();
    }
    public function findByTwoDate($firstDate, $secDate)
    {
        $em = $this->getEntityManager();



        $query = $em->createQuery("SELECT b FROM App\Entity\Book b 
        WHERE b.publicationDate >= :firstDate
        AND b.publicationDate <= :secDate")
            ->setParameter('firstDate', $firstDate)
            ->setParameter('secDate', $secDate);

        return $query->getResult();
    }
    public function updateCategoryForWilliamShakespear($username, $category)
{
    $em = $this->getEntityManager();
    $query = $em->createQuery('UPDATE App\Entity\Book b SET b.category = :category WHERE b.author IN (SELECT a FROM App\Entity\Author a WHERE a.username = :username)')
        ->setParameters([
            'category' => $category,
            'username' => $username,
        ]);

    return $query;
}



}
