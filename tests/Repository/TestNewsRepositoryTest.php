<?php

namespace App\Tests\Repository;

use App\Entity\TestNews;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestNewsRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

	private $news_tpl = '';
	private $news_tpl2 = '';
	private $news_tpl3 = '';

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
		
		$this->news_tpl = '<div style="margin:0 0 5px 5px;padding:0 0 0 5px"><div>{N} <u>(id:{I})</u></div><div style="padding:0 0 0 5px"><div>{T}</div></div></div>'.PHP_EOL;
		$this->news_tpl2 = '<div onclick="obj = document.getElementById(\'result{ID}\');obj.style.display = (obj.style.display!=\'none\'?\'none\':\'block\')">open/hide result</div><div id="result{ID}" style="display:none">';
		$this->news_tpl3 = '</div>';
    }

    /*public function testGetAll()
    {
        $news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findAll()
        ;
    }*/

	public function testGetById()
    {
        //OUTPUT///////////
		$html = PHP_EOL.PHP_EOL.'<hr>function test: <b>'.__FUNCTION__.'</b>'.strtr($this->news_tpl2, array('{ID}'=>__FUNCTION__));
		///////////////////

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->find(2);
        ;
		
		//OUTPUT///////////
		$arr = array(
			'{T}' => $news[0]->getCreatedAt()->format('Y-m-d H:i:s'),
			'{I}' => $news[0]->getId(),
			'{N}' => $news[0]->getName()
		);

		$html .= strtr($this->news_tpl, $arr);

		$html .= $this->news_tpl3;
		file_put_contents(__DIR__.DIRECTORY_SEPARATOR.basename(__FILE__).'.html', $html, FILE_APPEND);
		///////////////////
    }

	public function testGetFresh()
    {
        //OUTPUT///////////
		$html = PHP_EOL.PHP_EOL.'<hr>function test: <b>'.__FUNCTION__.'</b>'.strtr($this->news_tpl2, array('{ID}'=>__FUNCTION__));
		///////////////////

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findFreshLastDays(4);
        ;

		//OUTPUT///////////
		foreach($news as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html .= strtr($this->news_tpl, $arr);
		}

		$html .= $this->news_tpl3;
		file_put_contents(__DIR__.DIRECTORY_SEPARATOR.basename(__FILE__).'.html', $html, FILE_APPEND);
		///////////////////
    }

	public function testGetFindPagination()
    {
        //OUTPUT///////////
		$html = PHP_EOL.PHP_EOL.'<hr>function test: <b>'.__FUNCTION__.'</b>'.strtr($this->news_tpl2, array('{ID}'=>__FUNCTION__));
		///////////////////

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(0, 5)->paginationMoveCurrent()->PaginationResult();
        ;

		//OUTPUT///////////
		//1
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>open first page <u>->findPagination(<b>0</b>, 5)->paginationMoveCurrent()->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(10000001, 5)->paginationMoveNext()->PaginationResult();
        ;
		//1
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>go next <u>->findPagination(<b>10000001</b>, <b>5</b>)->paginationMoveNext()->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(1220910, 5)->paginationMoveNext()->PaginationResult();
        ;
		//2
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>go next <u>->findPagination(<b>1220910</b>, <b>5</b>)->paginationMoveNext()->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(9507165, 5)->paginationMoveNext(2)->PaginationResult();
        ;
		//3
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>go next step 2 <u>->findPagination(<b>9507165</b>, <b>5</b>)->paginationMoveNext(<b>2</b>)->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(5905224, 5)->paginationMoveCurrent()->PaginationResult();
        ;
		//4
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>open prev page again <u>->findPagination(<b>5905224</b>, <b>5</b>)->paginationMoveCurrent()->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(2730008, 5)->paginationMoveCurrent()->PaginationResult();
        ;
		//5
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>open penultimate page <u>->findPagination(<b>2730008</b>, <b>5</b>)->paginationMoveCurrent()->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(2730008, 5)->paginationMovePrev()->PaginationResult();
        ;
		//6
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>open prev page <u>->findPagination(<b>2730008</b>, <b>5</b>)->paginationMovePrev()->PaginationResult();</u> with 5 news per page<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$news = $this->entityManager
            ->getRepository(TestNews::class)
            ->findPagination(4994849, 5)->paginationMoveNext(2)->PaginationResult();
        ;
		//7
		$html .= '<div>';
		$html2 = PHP_EOL.str_replace('->', PHP_EOL.'<br>->','<br>open next step 2 <u>->findPagination(<b>4994849</b>, <b>5</b>)->paginationMoveNext(2)->PaginationResult();</u><br> with last block of 5 news<hr>').PHP_EOL;
		foreach($news['page'] as $newsOne) {
			$arr = array(
				'{T}' => $newsOne->getCreatedAt()->format('Y-m-d H:i:s'),
				'{I}' => $newsOne->getId(),
				'{N}' => $newsOne->getName()
			);

			$html2 .= strtr($this->news_tpl, $arr);
		}

		$html2 .= '<table width="100%"><tr><td width="50%" style="visibility:'.($news['forward']==0?'hidden':'').'">prev</td><td width="50%" align="right" style="visibility:'.($news['back']==0?'hidden':'').'">next</td></tr></table>';
		$html .= '<div style="width:250px;padding:5px;border:1px solid #'.rand(100000,999999).';float:left">'.$html2.'</div></div>';
		//

		$html .= '<div style="clear:both">&nbsp;</div>'.$this->news_tpl3;
		file_put_contents(__DIR__.DIRECTORY_SEPARATOR.basename(__FILE__).'.html', $html, FILE_APPEND);
		///////////////////
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
?>