<?php
// src/Controller/LiflowController.php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Liflowweb;
use App\Form\FormType;
use App\Services\TestService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    * @Route("/liflow/step4")
    */

    public function step4()
    {
        return $this->render('liflow/step2.html.twig', [
        
        ]);
    }                                            

    /**
    * @Route("/liflow/step2")
    */

    public function step2(Request $request)
    {
        $form = $this->createForm(FormType::class       , ['action' => $this->generateUrl('step3'), 'method' => 'POST',] );
        $form->handleRequest($request);
        
        $target_dir = substr(md5(microtime()),rand(0,26),8);    //получаем имя временного каталога
        $source_dir = $request->query->get('sourceDirName');    //получаем имя каталога-источника из командной строки
        
        $data = file_get_contents("uploads/$source_dir/input.txt");    //значения конф. файла по умолчанию
        
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
        //    dump($data['user']);
        //    die();
        //return $this->redirectToRoute('step3');
        }
        
        
        
        return $this->render('liflow/step2.html.twig', [
        'form' => $form->createView(),
        'tmp' => $tmp
        ]);
    }

    /**
    * @Route("/liflow/step3", name="step3")
    */
    
    public function step3()
    {
        $request = Request::createFromGlobals();
        $mydesc = $request->request->get('description');
    
        return $this->render('liflow/step3.html.twig', [
        'mydesc' => $mydesc
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
