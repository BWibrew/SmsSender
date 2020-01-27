<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsMessagesController extends AbstractController
{
    /**
     * List all SMS messages, ordered with newest first.
     *
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $messages = [
            [
                'recipient' => 123456,
                'body' => 'Hello World!',
                'status' => 'sent',
                'created_at' => date('d-m-y h:m'),
                'sent_at' => date('d-m-y h:m'),
                'error_message' => null,
            ],
            [
                'recipient' => 654321,
                'body' => 'Foobar!',
                'status' => 'queued',
                'created_at' => date('d-m-y h:m'),
                'sent_at' => null,
                'error_message' => null,
            ],
            [
                'recipient' => 987654,
                'body' => 'Bizbaz!',
                'status' => 'failed',
                'created_at' => date('d-m-y h:m'),
                'sent_at' => null,
                'error_message' => 'The message failed to send!',
            ]
        ];

        return $this->render('index.html.twig', [
            'messages' => $messages,
        ]);
    }
}
