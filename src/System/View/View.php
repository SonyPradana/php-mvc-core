<?php

namespace System\View;

use System\Http\Response;

class View
{
  public static function render(string $view_path, array $portal = [])
  {
    $auth = new Portal($portal['auth'] ?? []);
    $meta = new Portal($portal['meta'] ?? []);
    $content = new Portal($portal['contents'] ?? []);
    $content_type = $portal['header']['content_type'] ?? 'Content-Type: text/html';

    // get render content
    ob_start();
    require_once $view_path ?? '';
    $html = ob_get_clean();

    // send render content to client
    $response = new Response();
    return $response
      ->setContent($html)
      ->setResponeCode(\System\Http\Response::HTTP_OK)
      ->setHeaders([$content_type])
      ->removeHeader([
        'Expires',
        'Pragma',
        'X-Powered-By',
        'Connection',
        'Server',
      ]);
  }

}
