<?php

namespace App\Controllers;

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

class ContactController
{
    public function index(ServerRequestInterface $request)
    {
        $html = file_get_contents(__DIR__ . '/../views/contact.html');
        return new Response(200, ['Content-Type' => 'text/html'], $html);
    }
}
