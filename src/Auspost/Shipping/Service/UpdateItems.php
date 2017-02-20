<?php
/**
 * This file is part of Auspost API Client Library for PHP.
 *
 * The Auspost API Client Library for PHP is free software: you can redistribute
 * it and/or modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The Auspost API Client Library for PHP is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Auspost API Client Library for PHP.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @category   Fontis
 * @package    auspost-api-php
 * @author     Thai Phan
 * @copyright  Copyright (c) 2013 Fontis Pty Ltd (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/LGPL-3.0 GNU Lesser General Public License (LGPL 3.0)
 */
namespace Auspost\Shipping\Service;

use JsonSchema\Validator;

use \Exception;
#use Guzzle\Common\Collection;
#use Guzzle\Common\Event;
#use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

/**
 */
class UpdateItems extends ServiceDescription
{
    protected $schema;
    protected $schemaPath;

    /**
     * ShippingServiceGetItemPrices constructor.
     * @param array $config
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->schemaPath = realpath(__DIR__ . '/UpdateItems/request.json');
        echo sprintf("%s->schemaPath: %s\n", __METHOD__, $this->schemaPath);
        if (!file_exists($this->schemaPath)) {
            throw new Exception(sprintf("schema file '%s' not found", $this->schemaPath));
        }
        //$this->schema = json_encode(file_get_contents($this->schemaPath));
        $this->schema = (object)['$ref' => 'file://' . $this->schemaPath];
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateRequest($data) {
        // Validate
        $validator = new Validator();

        echo sprintf("%s->data: %s\n", __METHOD__, print_r($data, true));
        echo sprintf("%s->schema: %s\n", __METHOD__, print_r($this->schema, true));

        $validator->check($data, $this->schema);
        $retVal = true;
        if (!$validator->isValid()) {
            foreach ($validator->getErrors() as $error) {
                echo sprintf("Error: [%s] %s\n", $error['property'], $error['message']);
            }
            $retVal = $validator->getErrors();
        }
        echo sprintf("%s->retVal: %s\n", __METHOD__, print_r($retVal, true)) ;
        return $retVal;
    }
}