<?php
// src/Controller/LiflowController.php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Liflowweb;
use App\Services\TestService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LiflowController extends AbstractController
{

    /**
    * @Route("/liflow")
    */
    
    public function step1(EntityManagerInterface $em)
    {
        //Generates random name for temp dir
        $rand = substr(md5(microtime()),rand(0,26),8);
        //Get entityes from DB
        $experiments = $em->getRepository(Liflowweb::class)->findBy([], ['id' => 'DESC']);
        //dd($pages);
                            
        return $this->render('liflow/step1.html.twig', [
        'rand' => $rand, 'experiments' => $experiments
        ]);
    }

    /**
    * @Route("/liflow/step2")
    */

    public function step2()
    {
        return $this->render('liflow/step2.html.twig', [
        
        ]);
    }                                            

    /**
    * @Route("/liflow/add-data")
    */
    
    public function addData(EntityManagerInterface $em)
    {
        $experiment = new Liflowweb();
        $experiment->setDate();
        $experiment->setUserid('u1224');
        $experiment->setCluster('imm');
        $experiment->setDescription('test2');
        $experiment->setHomedir('heart');
        $experiment->setTargetdir('id_2');
        $experiment->setBinarypath('~/bin/LVD');
        
        
        $em->persist($experiment);
        $em->flush();
        
        return new Response('<html><body>Success</html></body>');
    }
    
    /**
    * @Route("/liflow/show-data/{id}")
    */
            
    public function showData(Page $page)
    {
        //if(!$page) throw $this->createNotFoundException(sprintf('No article for id "%s"', $id));
        dd($page);
    }                                                
            
            
    //public function index(LoggerInterface $logger)
    public function index(TestService $service)
    {
            
        //$logger->info('Test log');
        $var1 = $service->convert(1000);
        
        return $this->render('liflow/step1.html.twig', [
            'var1' => $var1,
        ]);
    }


}
?>
