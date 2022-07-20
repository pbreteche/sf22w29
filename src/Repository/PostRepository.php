<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    const POST_LIMIT = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Post[]
     */
    public function searchByTitle(string $needle)
    {
        return $this
            ->createQueryBuilder('post')
            ->andWhere('post.title LIKE :pattern')
            ->orderBy('post.createdAt', 'DESC')
            ->getQuery()
            ->setParameter('pattern', $needle.'%')
            ->getResult()
        ;
    }

    /**
     * @return Post[]
     */
    public function searchByTitleDQL(string $needle)
    {
       return $this->getEntityManager()->createQuery(
           'SELECT post FROM '.Post::class.' post '.
           'WHERE post.title LIKE :pattern '.
           'ORDER BY post.createdAt DESC'
       )
           ->setParameter('pattern', $needle.'%')
           ->getResult()
       ;
    }

    /**
     * @return Post[]
     */
    public function findLatest(int $page, ?int &$maxPage = null)
    {
        $postsCount = $this->count([]);
        $maxPage = ceil($postsCount / self::POST_LIMIT);
        if (0 > $page || $maxPage < $page) {
            $page = 1;
        }
        $offset = ($page - 1) * self::POST_LIMIT;
        return
            $this->createQueryBuilder('post')
            ->addSelect('category')
            ->leftJoin('post.categorizedBy', 'category')
            ->setMaxResults(self::POST_LIMIT)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[]
     */
    public function sameCategory(Post $post)
    {
        $qb = $this->createQueryBuilder('post');

        if ($post->getCategorizedBy()) {
            $qb
                ->andWhere('post.categorizedBy = :category')
                ->setParameter('category', $post->getCategorizedBy())
            ;
        } else {
            $qb->andWhere('post.categorizedBy IS NULL');
        }

        return $qb
            ->andWhere('post <> :post')
            ->setMaxResults(self::POST_LIMIT)
            ->getQuery()
            ->setParameter('post', $post)
            ->getResult()
        ;
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        return
            parent::createQueryBuilder($alias, $indexBy)
            ->orderBy($alias.'.createdAt', 'DESC')
        ;
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
