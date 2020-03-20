<?php
// src/Controller/LiflowController.php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Liflowweb;
use App\Form\FormType;
use App\Services\TestService;
use App\Services\LiFlowServices;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LiflowController extends AbstractController
{

    /**
    * @Route("/liflow", name="step1")
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
    * @Route("/liflow/step2/{exp_id}", name="step2")
    */

    public function step2(Request $request, EntityManagerInterface $em, LiFlowServices $service, string $exp_id)
    {
        $target_dir = substr(md5(microtime()),rand(0,26),8);    //получаем имя временного каталога
        //$source_dir = $service->test_input( $request->query->get('sourceDirName') );  //получаем имя каталога-источника из командной строки
        $source_dir = $service->test_input( $exp_id );
                    
            //Read the database to find settings
        $experiment = $em->getRepository(Liflowweb::class)->findOneBy(['targetdir' => "$source_dir"]);
            
            //Fill the form with settings
        $form = $this->createForm(FormType::class  , ['action' => $this->generateUrl('step3'), 'method' => 'POST',] );
        
        $form->get('binary_path')->setData($experiment->getBinarypath());
        //$form->get('cluster')->setData($experiment->getCluster());
        $form->get('workdir')->setData($experiment->getHomedir());
        $form->get('template_name')->setData($experiment->getTemplatename());
        $form->get('description')->setData($experiment->getDescription());
        $form->get('user')->setData($experiment->getUserid());
            //More settings
        $conf_file = file_get_contents("../uploads/$source_dir/input.txt");    //значения конф. файла по умолчанию
        $form->get('conf_file')->setData($conf_file);
        $lines = file("../uploads/$source_dir/runme.sh");    //вытащим строку запуска для slurm, поскольку в базу данных ее записать проблематично
        $form->get('runme')->setData(trim($lines[5]));
        $form->get('exp_id')->setData( $source_dir );        //hidden field
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            //$form_data = $form->getData();
            //dd($form_data['user']);
            return $this->redirectToRoute('step3', [ 'request' => $request ], 307);
            //return $this->redirectToRoute('step3', ['query' => $source_dir], [ 'request' => $request ], 307);         //307 saves POST method instead of transforming it to GET
        }
        
        
        
        return $this->render('liflow/step2.html.twig', [
        'form' => $form->createView(),
        /*'res' => $res,*/
        ]);
    }

    /**
    * @Route("/liflow/step3", name="step3", methods="POST")
    */
    
    public function step3(Request $request)
    {
        $form_data = $request->request->get('form');            //Получаем параметры из предыдущей формы

        $my_description = $form_data['description'];
        $my_binaryPath = $form_data['binary_path'];
        $my_workDir = $form_data['workdir'];
        $my_runme = $form_data['runme'];
        $my_templatename = $form_data['template_name'];
        $my_description = $form_data['description'];
        $my_user = $form_data['user'];
        $my_passwd = $form_data['password'];
        //$my_cluster = $form_data['cluster'];
        dd($form_data);
        
    
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
        $experiment->setDescription('Default config');
        $experiment->setHomedir('heart');
        $experiment->setTargetdir('_default');
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
