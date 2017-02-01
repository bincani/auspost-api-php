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
namespace Auspost\Tests\Shipping;

use Auspost\Shipping\ShippingClient;
use Auspost\Shipping\Enum\AddressState;
#use Auspost\Shipping\Enum\DeliveryNetwork;
#use Auspost\Shipping\Enum\State;

class OperationsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /** @var ShippingClient */
    private $client;

    public function setUp()
    {
        $this->client = self::getServiceBuilder()->get('shipping', true);
        //$this->client = $this->getServiceBuilder()->get('shipping');
    }

    /**
     * @dataProvider validateSuburbProvider
     * @group internet
     */
    public function testValidateSuburb($mock, $args) {
        $this->setMockResponse($this->client, $mock);
        $response = $this->client->ValidateSuburb($args);
        $this->assertTrue($response['found']);
    }

    public function validateSuburbProvider() {
        return array(
            array(
                $mockPath = array('shipping/response/address_valid'),
                array(
                    'suburb'    => 'Abbotsford',
                    'state'     => 'VIC',
                    'postcode'  => 3067
                )
            )
        );
    }

    /**
     * @dataProvider invalidateSuburbProvider
     * @group internet
     */
    public function testInvalidateSuburb($mock, $args) {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            //echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }
        $this->setMockResponse($this->client, $mock);
        $response = $this->client->ValidateSuburb($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertFalse($response['found']);
    }

    public function invalidateSuburbProvider() {
        return array(
            array(
                $mockPath = array('shipping/response/address_invalid_state'),
                array(
                    'suburb'    => 'Abbotsford',
                    'state'     => 'BAR',
                    'postcode'  => 3067
                )
            )
        );
    }

    /**
     * @dataProvider getItemPricesProvider
     * @group internet
     */
    public function testGetItemPrices($mock, $args) {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            //echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }
        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetItemPrices($args);
        //echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        //$this->assertFalse($response[0]['items']);
        $this->assertArrayHasKey('items', $response);
    }

    public function getItemPricesProvider() {
        $path = sprintf("%s/tests/mock/shipping/request/get_item_prices.json", getcwd());
        $body = json_encode(file_get_contents($path));
        return array(
            array(
                $mockPath = array('shipping/response/get_item_prices-with_warnings'),
                array(
                    'body' => $body
                )
            )
        );
    }

    /**
     * @dataProvider createShipmentsProvider
     * @group internet
     */
    public function testCreateShipments($mock, $args) {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }
        $this->setMockResponse($this->client, $mock);
        $response = $this->client->CreateShipments($args);
        //echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        //$this->assertFalse($response[0]['items']);
        $this->assertArrayHasKey('shipments', $response);
    }

    public function createShipmentsProvider() {
        $path = sprintf("%s/tests/mock/shipping/request/create_shipments.json", getcwd());
        $body = json_encode(file_get_contents($path));
        return array(
            array(
                $mockPath = array('shipping/response/create_shipments'),
                array(
                    'body' => $body
                )
            )
        );
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return in_array('--debug', $_SERVER['argv'], true);
    }

}
