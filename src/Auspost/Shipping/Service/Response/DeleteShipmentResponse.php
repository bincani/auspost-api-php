<?php

namespace Auspost\Shipping\Service\Response;

use Guzzle\Service\Command\ResponseClassInterface;
use Guzzle\Service\Command\OperationCommand;

class DeleteShipmentResponse implements ResponseClassInterface {

    /**
     * Create a response model object from a completed command
     *
     * @param OperationCommand $command That serialized the request
     *
     * @return self
     */
    public static function fromCommand(OperationCommand $command)
    {
        $response = $command->getResponse();
        if ($response->getStatusCode() == 200) {
            return array('success');
        } else {
            return $response->json();
        }
    }

}