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
#use Auspost\Shipping\Enum\AddressState;
#use Auspost\Shipping\Enum\DeliveryNetwork;
#use Auspost\Shipping\Enum\State;

/**
 * Class OperationsTest
 * @package Auspost\Tests\Shipping
 */
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
     * @param $mock
     * @param $args
     */
    public function testValidateSuburb($mock, $args)
    {
        $this->setMockResponse($this->client, $mock);
        $response = $this->client->ValidateSuburb($args);
        $this->assertTrue($response['found']);
    }

    /**
     * @return array
     */
    public function validateSuburbProvider()
    {
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
     * @param $mock
     * @param $args
     */
    public function testInvalidateSuburb($mock, $args)
    {
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

    /**
     * @return array
     */
    public function invalidateSuburbProvider()
    {
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
     * @param $mock
     * @param $args
     */
    public function testGetItemPrices($mock, $args)
    {
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

    /**
     * @return array
     */
    public function getItemPricesProvider()
    {
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
     * @param $mock
     * @param $args
     */
    public function testCreateShipments($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->CreateShipments($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        //$this->assertFalse($response[0]['items']);
        $this->assertArrayHasKey('shipments', $response);
        $this->assertArrayHasKey('shipment_id', $response['shipments'][0]);
    }

    /**
     * @return array
     */
    public function createShipmentsProvider()
    {
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
     * @dataProvider updateShipmentProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testUpdateShipment($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->UpdateShipment($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        //$this->assertFalse($response[0]['items']);
        $this->assertArrayNotHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function updateShipmentProvider()
    {
        $path = sprintf("%s/tests/mock/shipping/request/update_shipment.json", getcwd());
        $body = json_encode(file_get_contents($path));
        return array(
            array(
                $mockPath = array('shipping/response/update_shipment'),
                array(
                    'shipment_id'   =>  "9lesEAOvOm4AAAFI3swaDRYB",
                    'body' => $body
                )
            )
        );
    }

    /**
     * @dataProvider getShipmentsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testGetShipments($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetShipments($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        //$this->assertFalse($response[0]['items']);
        $this->assertArrayHasKey('shipments', $response);
    }

    /**
     * @return array
     */
    public function getShipmentsProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/get_shipments'),
                array(
                    'shipment_ids' => "9lesEAOvOm4AAAFI3swaDRYB "
                )
            )
        );
    }

    /**
     * @dataProvider getFailedShipmentsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testGetFailedShipments($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetShipments($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        //$this->assertFalse($response[0]['items']);
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function getFailedShipmentsProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/get_failed_shipments'),
                array(
                    'shipment_ids' => "9lesEAOvOm4AAAFI3swaDRYB "
                )
            )
        );
    }

    /**
     * @dataProvider createFailedShipmentsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testCreateFailedShipments($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->CreateShipments($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function createFailedShipmentsProvider()
    {
        $path = sprintf("%s/tests/mock/shipping/request/create_failed_shipments.json", getcwd());
        $body = json_encode(file_get_contents($path));
        return array(
            array(
                $mockPath = array('shipping/response/create_failed_shipments'),
                array(
                    'body' => $body
                )
            )
        );
    }

    /**
     * @dataProvider createLabelsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testCreateLabels($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->CreateLabels($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayNotHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function createLabelsProvider()
    {
        $path = sprintf("%s/tests/mock/shipping/request/create_labels.json", getcwd());
        $body = json_encode(file_get_contents($path));
        return array(
            array(
                $mockPath = array('shipping/response/create_labels'),
                array(
                    'body' => $body
                )
            )
        );
    }

    /**
     * @dataProvider createFailedLabelsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testCreateFailedLabels($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->CreateLabels($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function createFailedLabelsProvider()
    {
        $path = sprintf("%s/tests/mock/shipping/request/create_failed_labels.json", getcwd());
        $body = json_encode(file_get_contents($path));
        return array(
            array(
                $mockPath = array('shipping/response/create_failed_labels'),
                array(
                    'body' => $body
                )
            )
        );
    }

    /**
     * @dataProvider getLabelProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testGetLabel($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetLabel($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('labels', $response);
    }

    /**
     * @return array
     */
    public function getLabelProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/get_label'),
                array(
                    'request_id'    =>  'd9d1445d-cd1b-452d-9d68-29dbb3967acf'
                )
            )
        );
    }

    /**
     * @dataProvider getFailedLabelProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testGetFailedLabel($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetLabel($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function getFailedLabelProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/get_failed_label'),
                array(
                    'request_id'    =>  'd9d1445d-cd1b-452d-9d68-29dbb3967acf'
                )
            )
        );
    }

    /**
     * @dataProvider getAccountsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testGetAccounts($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetAccounts($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('account_number', $response);
    }

    /**
     * @return array
     */
    public function getAccountsProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/get_accounts'),
                array(
                    'account_number'    =>  '0000123456'
                )
            )
        );
    }

    /**
     * @dataProvider getFailedAccountsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testGetFailedAccounts($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->GetAccounts($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function getFailedAccountsProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/get_failed_accounts'),
                array(
                    'account_number'    =>  '0000123456'
                )
            )
        );
    }

    /**
     * @dataProvider trackItemsProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testTrackItems($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->TrackItems($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('tracking_results', $response);
    }

    /**
     * @return array
     */
    public function trackItemsProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/track_items'),
                array(
                    'tracking_ids'    =>  '7XX1000,7XX1000634011427'
                )
            )
        );
    }

    /**
     * @dataProvider deleteShipmentProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testDeleteShipment($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->DeleteShipment($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayNotHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function deleteShipmentProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/delete_shipment'),
                array(
                    'shipment_id '    =>  '9lesEAOvOm4AAAFI3swaDRYB'
                )
            )
        );
    }

    /**
     * @dataProvider deleteFailedShipmentProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testDeleteFailedShipment($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->DeleteShipment($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function deleteFailedShipmentProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/delete_failed_shipment'),
                array(
                    'shipment_id '    =>  '9lesEAOvOm4AAAFI3swaDRYB'
                )
            )
        );
    }

    /**
     * @dataProvider deleteItemProvider
     * @group internet
     * @param $mock
     * @param $args
     */
    public function testDeleteItem($mock, $args)
    {
        if($this->isDebug()) {
            echo sprintf("%s->mock: %s\n", __METHOD__, print_r($mock, true));
            echo sprintf("%s->args: %s\n", __METHOD__, print_r($args, true));
        }

        $this->setMockResponse($this->client, $mock);
        $response = $this->client->DeleteItem($args);
        if($this->isDebug()) {
            echo sprintf("%s->response: %s\n", __METHOD__, print_r($response, true));
        }
        $this->assertArrayNotHasKey('errors', $response);
    }

    /**
     * @return array
     */
    public function deleteItemProvider()
    {
        return array(
            array(
                $mockPath = array('shipping/response/delete_item'),
                array(
                    'shipment_id '    =>  '9lesEAOvOm4AAAFI3swaDRYB',
                    'item_id'         =>  'TkGsEAOv9a4AAAFI8MwaDRYB'
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
