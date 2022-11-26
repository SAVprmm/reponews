<?php

namespace App\Controller;

use App\Entity\TestNews;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller {
    public function home() {
        $r = '<pre>';

		$entityManager = $this->getDoctrine()->getManager();
        $repo = $entityManager->getRepository('App\Entity\TestNews');

		//$repo->findAll();

		/*$newsOne = $repo->find(10000001);
		$r .= var_export($newsOne, true);*/

		/*$newsFresh = $repo->findFreshLastDays(4);
		$r .= var_export($newsFresh, true);*/

		/*$newsPagination = $repo->findPagination(5792283, 40)->paginationMoveNext()->PaginationResult();
		$r .= var_export($newsPagination, true);*/

		/*$newsPagination = $repo->findPagination(5792283, 40)->paginationMovePrev()->PaginationResult();
		$r .= var_export($newsPagination, true);*/

		/*$newsPagination = $repo->findPagination(5792283, 40)->paginationMoveCurrent()->PaginationResult();
		$r .= var_export($newsPagination, true);*/

		//from fresh news
		$newsPagination = $repo->findPagination(0, 5)->paginationMoveCurrent()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(10000001, 5)->paginationMoveNext()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(1220910, 5)->paginationMoveNext()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';
		
		$newsPagination = $repo->findPagination(9507165, 5)->paginationMoveNext()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(9507165, 5)->paginationMoveCurrent()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		///back
		$newsPagination = $repo->findPagination(9622784, 5)->paginationMovePrev()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(9507165, 5)->paginationMovePrev(3)->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>'; 
		
		//from last news
		/*$newsPagination = $repo->findPagination(3505315, 5)->paginationMoveCurrent()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(3505315, 5)->paginationMovePrev()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(2730008, 5)->paginationMovePrev()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(4994849, 5)->paginationMoveCurrent()->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';

		$newsPagination = $repo->findPagination(4994849, 5)->paginationMoveNext(2)->PaginationResult();
		$r .= '<pre>back:'.$newsPagination['back'].'<br>';
		foreach($newsPagination['page'] as $object) {
			$r .= $object->getId().'		'.$object->getCreatedAt()->format('Y-m-d H:i:s').'<br>';
		}
		$r .= '<br>forward:'.$newsPagination['forward'].'</pre><hr>';*/

		/* * /
		//adding new
		$newNews = new TestNews();
		$r .= var_export($newNews, true);
		$newNews->setName('test112');
		$newNews->setText('test112');
		//$newNews->setCreatedAt(new \DateTime("2022-10-10 10:10:10"));
		$r .= var_export($newNews, true);
		$entityManager->persist($newNews);
		$entityManager->flush();/**/

		$r .= random_int(0, 100);
		
		$r .= '</pre>';

        return new Response(
            '<html><body>Home number: '.$r.'</body></html>'
        );
    }
}