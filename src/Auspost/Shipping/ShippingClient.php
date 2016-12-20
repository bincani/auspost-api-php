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
namespace Auspost\Shipping;

use Auspost\Shipping\Service\GetItemPrices;
use Guzzle\Common\Collection;
use Guzzle\Common\Event;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Client to interact with Postage Assessment Calculator and Postcode Search
 * services
 */
class ShippingClient extends Client
{

    const API_URL = 'https://digitalapi.auspost.com.au';

    public static function factory($config = array())
    {
        if (isset($config['developer_mode']) && is_bool($config['developer_mode'])) {
            $developerMode = $config['developer_mode'];
            $config['base_url'] = self::API_URL . "/testbed";
        }
        else {
            $developerMode = false;
            $config['base_url'] = self::API_URL;
        }

        $default = array(
            'developer_mode' => $developerMode,
            //'base_url' => self::API_URL
        );

        $required = array(
            'developer_mode',
            'base_url',
            'account_no',
            'auth_key',
            'auth_pass'
        );

        $config = Collection::fromConfig($config, $default, $required);

        //$config['base_url'] = 'https://httpbin.org';
        echo sprintf("base_url: %s\n", $config['base_url']);
        $client =  new self($config['base_url'], $config);

        $client->getConfig()->setPath(
            'request.options/headers/Authorization',
            'Basic ' . base64_encode($config->get('auth_key') . ':' . $config->get('auth_pass'))
        );

        $client->getConfig()->setPath('request.options/headers/Account-Number', $config['account_no']);
        //$client->getConfig()->setPath('request.options/headers/Accept', 'application/json');
        //$client->getConfig()->setPath('request.options/headers/Content-Type', 'application/json');
        $client->getConfig()->setPath('request.options/headers/Cache-Control', 'no-cache');
        $client->getConfig()->setPath('request.options/headers/Connection', 'close');

        $client->setDescription(ServiceDescription::factory(__DIR__ . '/service.json'));
        $client->setSslVerification(false);

        $client->getEventDispatcher()->addListener(
            'request.before_send',
            function (Event $event) {

                // set correct headers
                if (is_object(json_decode($event['request']->getBody()))) {
                    $event['request']->setHeader('Content-Type', 'application/json');
                }

                //$request->setHeader('X-FactoryX', 'test');

                //echo sprintf("event: %s", print_r($event, true));
                $request = $event['request'];
                //$request->setHeader('Content-Length', 0);
                echo sprintf("%s->body: %s\n", __METHOD__, $request->getBody());

                //echo sprintf("request: %s", get_class($request));
                //echo sprintf("url: %s", $request->getUrl());
                if (preg_match("/shipping\/v1\/prices\/items$/", $request->getUrl())) {
                    $service = new GetItemPrices([]);
                    $validate = $service->validateRequest($request->getBody());
                    echo sprintf("%s->validate: %s", __METHOD__, print_r($validate, true));
                    die();
                }
/*
                if (preg_match("/shipments$/", $request->getUrl())) {
                    $testShipments = file_get_contents("/var/www/factoryx/bincani/austpost/auspost-api-php/testShipments.json");
                    $request->setBody($testShipments);
                }
                if (preg_match("/labels$/", $request->getUrl())) {
                    $testCreateLabels = file_get_contents("/var/www/factoryx/bincani/austpost/auspost-api-php/testCreateLabels.json");
                    $request->setBody($testCreateLabels);
                    echo sprintf("%s->body: %s", __METHOD__, $request->getBody());
                }
*/
            }
        );


        $client->getEventDispatcher()->addListener(
            'command.after_prepare',
            function (Event $event) {
                //echo sprintf("url: %s", $request->getUrl());
            }
        );
        return $client;
    }
}
