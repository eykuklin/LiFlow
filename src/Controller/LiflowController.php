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
    * @Route("/liflow/step2/{exp_id}", name="step2")
    */

    public function step2(Request $request, EntityManagerInterface $em, LiFlowServices $service, string $exp_id)
    {
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
        $form->get('code')->setData($experiment->getRuncode());
            //For compatibility with old experiments, we will check the slurmcommand field for emptiness
        if (empty($experiment->getSlurmcommand()))
        {
            $lines = file("../uploads/$source_dir/runme.sh");    //вытащим строку запуска для slurm
            $form->get('options')->setData(trim($lines[5]));
        }
        else $form->get('options')->setData($experiment->getSlurmcommand());
        
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //Check the passwd
            $form_data = $form->getData();
            $my_user = $form_data['user'];
            $my_passwd = $form_data['password'];
            //dd($form_data['user']);
            $command = 'sshpass -p "' . $my_passwd . '" ssh -o StrictHostKeyChecking=no ' . $my_user . '@umt.imm.uran.ru "/usr/bin/hostname"';
            //$command = "pwd";
            exec($command, $arr);
            //dd($arr);
            //dd$command);
            if (empty($arr))
            {
                return $this->render('liflow/step2.html.twig', [
                'form' => $form->createView(), 'message' => "Wrong IMM login/password!",
                ]);            
            }
            else                      
            {
                if ($arr[0] == "umtnew")
                    return $this->redirectToRoute('step3', [ 'request' => $request ], 307);             //307 saves POST method instead of transforming it to GET
                    //return $this->redirectToRoute('step3', ['query' => $source_dir], [ 'request' => $request ], 307);    
                else 
                    return $this->render('liflow/step2.html.twig', [
                    'form' => $form->createView(), 'message' => "Wrong IMM login/password!",
                    ]);                    
            }
        }
                
        return $this->render('liflow/step2.html.twig', [
        'form' => $form->createView(), 'message' => "",
        ]);
    }

    /**
    * @Route("/liflow/step3", name="step3", methods="POST")
    */
    
    public function step3(Request $request, EntityManagerInterface $em, LiFlowServices $service)
    {
        //$target_dir = substr(md5(microtime()),rand(0,26),8);    //имя временного каталога
        
        $form_data = $request->request->get('form');            //Получаем параметры из предыдущей формы

        //TODO: сделать класс для настроек
        $my_conf_file = $form_data['conf_file'];
        $my_description = $form_data['description'];
        $my_binary_path = $form_data['binary_path'];
        $my_workdir = $form_data['workdir'];
        $my_options = $form_data['options'];
        $my_code = $form_data['code'];
        //$my_runme = $form_data['runme'];
        $my_cluster = "imm";              //костыль
        //$my_cluster = $form_data['cluster'];
        $my_templatename = $form_data['template_name'];
        $my_description = $form_data['description'];
        $my_user = $form_data['user'];
        $my_passwd = $form_data['password'];
        //dd($form_data);
        $my_description = $service->create_description($my_description, $my_conf_file);  //добавим отмеченные строчки к описанию
    
            //Insert into database
        $experiment = new Liflowweb();
        $experiment->setDate();
        $experiment->setUserid("$my_user");
        $experiment->setCluster('imm');
        $experiment->setDescription("$my_description");
        $experiment->setHomedir("$my_workdir");
        $experiment->setTargetdir('temp');
        $experiment->setBinarypath("$my_binary_path");
        $experiment->setTemplatename("$my_templatename");
        $experiment->setSlurmcommand("$my_options");
        $experiment->setRuncode("$my_code");
        $em->persist($experiment);
        $em->flush();
            //Retrieve experiment ID            
        $target_dir = "id_" . $experiment->getId();
        $experiment->setTargetdir("$target_dir");
        $em->persist($experiment);
        $em->flush();
            
            //Save data on disk
        $service->save_data("$target_dir", "$my_conf_file", "$my_options", "$my_code", "$my_description");
        
            //  ./upload.py user password /var/www/html/my-project/uploads/default /home/u1224/heart/ binary_path alternate_name computational_cluster
        $command0 = "echo `date` " . $my_user . " >> /tmp/upload.log";
        exec($command0, $output0);
        $command1 = "../scripts/upload.py " . $my_user . " " . $my_passwd . " /var/www/html/my-project/uploads/" . $target_dir . ' "/home/' . $my_user . "/" . $my_workdir . '" "' 
            . $my_binary_path . '" "' . $my_templatename . '" "' . $my_cluster . '" 2>&1 | tee -a /tmp/upload.log';
        exec($command1,$output1);    
        
        sleep(2);
        
        //return $this->render('liflow/step3.html.twig', [
        //]);
        return $this->redirectToRoute('step4');
    }
    

    /**
    * @Route("/liflow/step4", name="step4")
    */
            
    public function step4()
    {
        /*
        // sshpass -p "passwd" ssh -o StrictHostKeyChecking=no user@umt.imm.uran.ru
        if ($my_cluster == 'imm') $command = 'sshpass -p "' . $my_passwd . '" ssh -o StrictHostKeyChecking=no ' . $my_user . '@umt.imm.uran.r
        else $command = 'sshpass -p "' . $my_passwd . '" ssh -o StrictHostKeyChecking=no ' . $my_user . '@cluster.alexbers.com "squeue -u ' .
        exec($command, $res);  //получаем массив строчек с результатами
        
        $stat='white';
        echo "<table border='1'>";
        foreach($res as $key=>$value){
            echo "<tr>";
            $pieces = preg_split('/\s+/', $res[$key]);  //разбиваем строку на кусочки
            if($pieces[3]=='R'){
                $stat='#82FA58';    //зеленый, выполняется
            }
            if($pieces[3]=='PD'){
                $stat='yellow';     //желтый, ждет ресурсов
            }
            if($pieces[3]=='F'){
                $stat='red';        //красный, завершилась с ошибкой
            }
            foreach($pieces as $key=>$value)
            {
                if($pieces[$key] !== ''){
                    echo "<td bgcolor='$stat', align='left'>".$pieces[$key]."</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
        
        */
                    
    
    
        return $this->render('liflow/step4.html.twig', [
    
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
            
            
}
?>
