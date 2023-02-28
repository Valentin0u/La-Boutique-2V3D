<?php

namespace App\Class;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Mail
{
    private $api_key = '357a4594d3701ed5785e0140cc46205b';    
    private $api_key_secret = '2dd6c25ea6d3152e613af64273f4cc2a';    

    
    public function send($to_email, $to_name, $subject, $content)
    { 
        
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "laboutique2v3d@gmail.com",
                        'Name' => "La Boutique 2V3D"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4490094,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}