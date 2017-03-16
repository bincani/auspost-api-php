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

use \Exception;
use Auspost\Shipping\Service\AbstractShippingService;

/**
 * Class CreateOrderIncludingShipments
 * @package Auspost\Shipping\Service
 */
class CreateOrderIncludingShipments extends AbstractShippingService
{
    /**
     * ShippingServiceGetItemPrices constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->schemaPath = realpath(__DIR__ . '/CreateOrderIncludingShipments/request.json');
        if (!file_exists($this->schemaPath)) {
            throw new Exception(sprintf("schema file '%s' not found", $this->schemaPath));
        }

        $this->schema = (object)['$ref' => 'file://' . $this->schemaPath];
    }
}