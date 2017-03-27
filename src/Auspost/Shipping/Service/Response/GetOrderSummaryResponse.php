<?php

namespace Auspost\Shipping\Service\Response;

use Guzzle\Service\Command\ResponseClassInterface;
use Guzzle\Service\Command\OperationCommand;

class GetOrderSummaryResponse implements ResponseClassInterface {

    /**
     * Create a response model object from a completed command
     *
     * @param OperationCommand $command That serialized the request
     *
     * @return self
     */
    public static function fromCommand(OperationCommand $command) {

        $response = $command->getResponse();
        if ($response->getStatusCode() == 200) {
            $path = 'success';
            if ($response->getContentType() == "application/pdf") {
                //$url = self::_urlToArray($command->getRequest()->getUrl());
                //$path = sprintf("%s/%s_%s.pdf", getcwd(), date('Ymd-his'), $url['orders']);
                $path = tempnam(sys_get_temp_dir(), 'TMP_');
                file_put_contents($path, $response->getBody());
            }
            return array($path);
        }
        else {
            return $response->json();
        }
    }
    
    /**
     *
 
    private static _urlToArray($url) {
        $parts = parse_url($url);
        $pathChunks = preg_split('/\/+/', $parts['path']);
        $retVal = array();
        for($i = 0, $cnt = count($pathChunks); $i < $cnt; $i += 2) {
            $next = $i + 1;
            if ($pathChunks[$next]) {
                $retVal[$pathChunks[$i]] = $pathChunks[$next];
            }
        }
        return $retVal;
    }
    */    
}