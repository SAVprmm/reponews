<?php

namespace App\Repository;

use App\Entity\TestNews;
use Psr\Log\LoggerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TestNews|null find(int $id, $lockMode = null, $lockVersion = null)
 * @method TestNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestNews[]    findAll()
 * @method TestNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method TestNews[]    findFreshLastDays(int $days = 3, array $orderBy = null)
 * @method $this         findPagination(int $id, int $newsPerPage = 0)
 * @method $this         paginationMoveCurrent()
 * @method $this         paginationMovePrev(int $pageCount = 1)
 * @method $this         paginationMoveNext(int $pageCount = 1)
 * @method [int,TestNews[],int]         PaginationResult()
 */
class TestNewsRepository extends ServiceEntityRepository
{    
    /**
     * newsPerPare default value of news per page
     *
     * @var mixed
     */
    private $newsPerPare;
    
    /**
     * newsMoveWarning write log warting if moving more than
     *
     * @var mixed
     */
    private $newsMoveWarning;
    
    /**
     * newsPaginationPerPage current value
     *
     * @var mixed
     */
    private $newsPaginationPerPage;
    
    /**
     * newsPaginationId first news id of pagination
     *
     * @var mixed
     */
    private $newsPaginationId;
    
    /**
     * newsPaginationStep step size of moving to next|prev page
     *
     * @var mixed
     */
    private $newsPaginationStep;
    
    /**
     * newsPeriodFrameDays period in day for search frame of news on prev move
     *
     * @var mixed
     */
    private $newsPeriodFrameDays;
    
    /**
     * firstActiveNewstId
     *
     * @var mixed
     */
    private $firstActiveNewsId;

    /**
     * lastActiveNewsCreateAt
     *
     * @var mixed
     */
    private $lastActiveNews;

    public function __construct(ManagerRegistry $registry, int $newsPerPare, int $newsMoveWarning, int $newsPeriodFrameDays)
    {
        parent::__construct($registry, TestNews::class);
        $this->newsPerPare = $newsPerPare;
        $this->newsMoveWarning = $newsMoveWarning;
        $this->newsPeriodFrameDays = $newsPeriodFrameDays;
        $this->newsPaginationId = -1;
        $this->firstActiveNewsId = 0;
        $this->lastActiveNews = null;
    }

    /**
     * find
     *
     * @param  mixed  $id news id
     * @param  mixed $lockMode
     * @param  mixed $lockVersion
     * @return TestNews[] Returns an array of TestNews objects
     */
    public function find($id, $lockMode = null, $lockVersion = null) {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', strval($id))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     *findFreshLastDays
     *
     * @param  int $days number days of fresh
     * @param  array $orderBy ordering null|ASC|DESC
     * @return TestNews[] Returns an array of TestNews objects
     */
    public function findFreshLastDays(int $days = 3, array $orderBy = null) {
        if ($orderBy == null || count($orderBy) != 1 || ($orderBy[0] !== 'ASC' && $orderBy[0] !== 'DESC')) {
            $orderBy[0] = 'DESC';
        }
        
        if($days <= 0) {
            $days = 1;
        }
        $orderAsc = ($orderBy[0] == 'ASC');
        
        $datetime = new \DateTime('now');
        $datetime->modify('-'.strval($days-1).' day');

        $newsFresh = $this->createQueryBuilder('t')
            ->andWhere('t.created_at >= :val AND t.deleted_at IS NULL')
            ->setParameter('val', $datetime->format('Y-m-d 00:00:00'))
            ->orderBy('t.created_at', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        usort($newsFresh, function($object1, $object2) use ($orderAsc) {
            if($object1->getCreatedAt() == $object2->getCreatedAt()) return 0;
            return (($object1->getCreatedAt() > $object2->getCreatedAt()) ? -1 : 1) * ($orderAsc ? -1 : 1);
        });

        return $newsFresh;
    }
    
    /**
     * findPagination
     *
     * @param  mixed $id
     * @param  mixed $newsPerPage
     * @return $this
     */
    public function findPagination(int $id, int $newsPerPage = 0) {
        $this->newsPaginationPerPage = $newsPerPage > 0 ? $newsPerPage : $this->newsPerPare;
        $this->newsPaginationId = $id;
        return $this;
    }
    
    /**
     * paginationMoveNext setting direction and step of moving under pagination
     *
     * @param  mixed $pageCount step
     * @return $this
     */
    public function paginationMoveNext(int $pageCount = 1){
        return $this->paginationMove((-1*$pageCount));
    }
        
    /**
     * paginationMovePrev setting direction and step of moving under pagination
     *
     * @param  mixed $pageCount step
     * @return $this
     */
    public function paginationMovePrev(int $pageCount = 1){
        return $this->paginationMove($pageCount);
    }

    /**
     * paginationMoveCurrent get current page
     *
     * @return $this
     */
    public function paginationMoveCurrent(){
        return $this->paginationMove(0);
    }
        
    /**
     * paginationMove action of moving
     *
     * @param  mixed $pageCount
     * @return $this
     */
    private function paginationMove(int $pageCount = 1){
        if((abs($pageCount)*$this->newsPaginationPerPage)>$this->newsMoveWarning) {
            trigger_error('big move with LIMIT OFSET, slow query', E_USER_WARNING);
        }
        $this->newsPaginationStep = $this->newsPaginationId != 0 ? $pageCount : -1;
        return $this;
    }
        
    /**
     * paginationResult generate  result of back/forward moving under pagination
     * back/forward -> 0 - moving next in this way not allowed
     *
     * @return array('back' => int $newsId|0, 'page' => TestNews[], 'forward' => $newsId|0) Returns an array of Pagination objects
     */
    public function paginationResult(){
        $prevStatus = 0;
        $newsInPage = array();
        $nextStatus = 0;
        $dateFirstNews = '';

        //unconfigured call not allow
        if($this->newsPaginationId == -1) {
            //!!!needs log or other error
            return array();
        }

        if(empty($this->lastActiveNewsCreateAt)) { //need know last fresh news
            $datetime = new \DateTime('now');
            $newsById = $this->createQueryBuilder('t')
                ->andWhere('t.created_at <= :val AND t.deleted_at IS NULL')
                ->setParameter('val', $datetime->format('Y-m-d H:i:s'))
                ->orderBy('t.created_at', 'DESC')
                ->setFirstResult(0)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult()
            ;
            
            $this->lastActiveNews = $newsById[0];
            unset($datetime, $newsById);
        }

        if($this->newsPaginationId != 0) { //get datetime of first news on page
            $newsById = $this->createQueryBuilder('t')
                ->andWhere('t.id = :val')
                ->setParameter('val', strval($this->newsPaginationId))
                ->getQuery()
                ->getResult()
            ;

            //news id not exists (first in page)
            if(count($newsById) != 1) {
                //!!!needs log or other error
                return array();
            }
            
            $dateFirstNews = $newsById[0]->getCreatedAt()->format('Y-m-d H:i:s');
            unset($newsById);
        } else { //like start from first page
            $dateFirstNews = $this->lastActiveNews->getCreatedAt()->format('Y-m-d H:i:s');
        }

        //get first id of news for case news inserted (add new news backdating) not in order
        if($this->firstActiveNewsId == 0) {
            //search first active news_id ordered by date created news
            $newsFirst = $this->createQueryBuilder('t')
                ->andWhere("t.created_at > '0000-00-00 00:00:00' AND t.deleted_at IS NULL")
                ->orderBy('t.created_at', 'ASC')
                ->setFirstResult(0)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult()
            ;

            //empty news storage?
            if(count($newsFirst) != 1) {
                //!!!needs log or other error
                return array();
            }
            $this->firstActiveNewsId = $newsFirst[0]->getId();

            unset($newsFirst);
        }

        for($attempt = 0; $attempt <= 16; $attempt++) {
            $dayFrame = $attempt == 0 ? $this->newsPeriodFrameDays : ($dayFrame*2);
            if($dayFrame >= 36500) {
                //if the news was read over a period of 100 years and not found -> break;
                //$this->newsPeriodFrameDays^$attempt >= 100 year
                //max 16 cycles can be with $this->newsPeriodFrameDays == 1
                //on each attempt frame of days doubled
                break;
            }
            $where = '';
            $parameters = array(
                'val1' => $dateFirstNews
            );
            if($this->newsPaginationStep > 0) { //if move back by date query is simple
                $where .= ' t.created_at >= :val1';
            } else {
                //if move forward needs read news beetwen date frame
                //frame moving like on each next step
                //1 2 3 4 5 6 7 8 910111213141516
                //- ___ ------- _______________......
                $datetime = new \DateTime($dateFirstNews);
                $dateFirstNews = $datetime->modify('-'.strval($this->newsPeriodFrameDays*$dayFrame).' day')->format('Y-m-d H:i:s');
                
                //if($this->newsPaginationStep != 0) { //old code
                    //$where .= ' t.created_at < :val1 AND t.created_at >= :val2';
                //} else {
                    $where .= 't.created_at <= :val1 AND t.created_at > :val2';
                //}
                $parameters['val2'] = $dateFirstNews;
            }
            $where .= ' AND t.deleted_at IS NULL';

            $query = $this->createQueryBuilder('t')
                ->andWhere($where)
                ->setParameters($parameters)
                ->orderBy('t.created_at', 'ASC')
            ;
            if($this->newsPaginationStep > 0) { //simple again
                //read from LIMIT after OFFSET
                //-1 need for read one from back(past) block
                //+1 need for detect, if next page exists
                $query = $query->setFirstResult(($this->newsPaginationPerPage*($this->newsPaginationStep-1)))
                            ->setMaxResults($this->newsPaginationPerPage+1);
            } else {
                //count will adding by newsFrame read
                //get all result on each step
            }

            $query = $query->getQuery(); //main move query

//echo $query->getSQL().PHP_EOL;
//echo '<br>'; print_r($parameters);

            $newsInPage = array_merge($newsInPage, $query->getResult()); //merge result of frames
//echo '<br>newsPaginationStep:'; print_r($this->newsPaginationStep);           
//echo '<br>newsPaginationId:'; print_r($this->newsPaginationId);
//echo '<br>'; print_r(count($newsInPage));
//echo '<br>first id'.$this->firstActiveNewsId.'<hr>';
            if( $this->newsPaginationStep > 0 ) {
                //if move back stop search anyway
                //because can be read by limit-offset
                break;
            } else {
                if(count($newsInPage)>=($this->newsPaginationPerPage*2+1)) {
                    //if found all+1 per page stop search
                    break;
                }
                foreach ( $newsInPage as $news ) { //prevent repeated empty queries in the wall
//echo '<br>first test'.$this->firstActiveNewsId.'|'.$news->getId();
                    if ( $this->firstActiveNewsId == $news->getId() ) {
                        //if found first news id stop search
                        break 2;
                    }
                }
            }
        }

        //in storage all records always reading with ASC sorting
        //this prevent of "Backward index scan"
        usort($newsInPage, $this->sortArrayObjects(false));

        //cut off the extra amount that was used for test on exist of next page
        if(count($newsInPage) >= ($this->newsPaginationPerPage+1)) {
            $cutFrom = 0;
            if($this->newsPaginationStep < 0) {
                //if moving in past (back) list sorted DESC and start from prev news block
                //skip prev block and cut anly needed
                //and $this->newsPaginationStep used for skip some count of page
                $cutFrom = $this->newsPaginationId != 0 ? $this->newsPaginationPerPage * abs($this->newsPaginationStep) : 0;
            }
            $newsInPage = array_slice($newsInPage, $cutFrom, $this->newsPaginationPerPage);
        }
        
//print_r($newsInPage[0]);
//print_r($newsInPage[(count($newsInPage)-1)]);
//echo '<br>f and last|'.$this->firstActiveNewsId.'|'.$this->lastActiveNews->getId().'<br>';
        
        //test if pagination can go back
        if($newsInPage[0]->getId() != $this->firstActiveNewsId && $newsInPage[(count($newsInPage)-1)]->getId() != $this->firstActiveNewsId) {
            $nextStatus = $newsInPage[0]->getId();
        }
//echo '<br>'.$newsInPage[0]->getCreatedAt()->format('Y-m-d H:i:s').'|'.$this->lastActiveNews->getCreatedAt()->format('Y-m-d H:i:s');

        //test if pagination can go forward
        if($newsInPage[0]->getCreatedAt() != $this->lastActiveNews->getCreatedAt()) {
            $prevStatus = $newsInPage[0]->getId();;
        }

        //reset all works parameters
        $this->newsPaginationId = -1;

        return array(
            'forward' => $prevStatus,
            'page' => $newsInPage,
            'back' => $nextStatus
        );
    }
        
    /**
     * sortArrayObjects
     *
     * @param  object $object1
     * @param  object $object2
     * @param  int $orderAsc
     * @return void
     */
    private function sortArrayObjects($orderAsc) {
        return function (object $object1, object $object2) use ($orderAsc) {
            if($object1->getCreatedAt() == $object2->getCreatedAt()) return 0;
            return (($object1->getCreatedAt() > $object2->getCreatedAt()) ? -1 : 1) * ($orderAsc ? -1 : 1);
        };
    }

    public function findAll() {
        exit('Big amount of records, stop this');
    }

    // /**
    //  * @return TestNews[] Returns an array of TestNews objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestNews
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
