<?php
// src/Controller/LuckyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{

    /**
    * @Route("/lucky/number")
    */

    public function number()
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
    /**
    * @Route("/lucky/main")
    */
    
    public function index()
    {
        return new Response('<html><head>Welcome to LiFlow!</head><body></body></html>');
	
    }
}
?>
