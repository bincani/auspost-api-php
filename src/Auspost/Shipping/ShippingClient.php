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
use Auspost\Shipping\Service\CreateShipments;
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
        echo sprintf("%s->config: %s\n", __METHOD__, print_r($config, true));
        if (isset($config['developer_mode']) && is_bool($config['developer_mode'])) {
            $developerMode = $config['developer_mode'];
            $config['base_url'] = self::API_URL . "/testbed";

            //$developerMode = false;
            //$config['base_url'] = self::API_URL;
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
            //'developer_mode',
            'base_url',
            'account_no',
            'auth_key',
            'auth_pass'
        );

        $config = Collection::fromConfig($config, $default, $required);

        //$config['base_url'] = 'https://httpbin.org';
        echo sprintf("%s->base_url: %s\n", __METHOD__, $config['base_url']);
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

        $servicePath = __DIR__ . '/service.json';
        echo sprintf("%s->servicePath: %s\n", __METHOD__, $servicePath);
        $client->setDescription(ServiceDescription::factory($servicePath));
        $client->setSslVerification(false);

        /*
        client.create_request
        client.command.create
        command.before_prepare
        command.after_prepare
        command.before_send
        command.after_send
        command.parse_response

        request.sent
        request.clone
        request.before_send
        setValidator();        
        */
        foreach($client->getAllEvents() as $event) {
            echo sprintf("ShippingClient.addListener[event: %s]\n", $event);
            $client->getEventDispatcher()->addListener(
                $event,
                function (Event $event) {
                    echo sprintf("%s->ShippingClient.event: %s\n", __METHOD__, $event->getName());
                    if ($event['request']) {
                        echo sprintf("%s->ShippingClient.event: %s\n", __METHOD__, $event['request']->getUrl());
                    }
                    //echo sprintf("%s->event: %s\n", __METHOD__, print_r($event, true));
                }
            );
        }

        /*
        request.sent see response
        */
        $client->getEventDispatcher()->addListener(
            'request.sent',
            function (Event $event) {
                echo sprintf("%s->event: %s\n", __METHOD__, $event->getName());
                echo sprintf("%s->getResponseBody: %s\n", __METHOD__, $event['request']->getResponseBody() );
            }
        );

        $client->getEventDispatcher()->addListener(
            'request.before_send',
            function (Event $event) {

                // set correct headers
                //$request->setHeader('X-FactoryX', 'test');
                //$request->setHeader('Content-Length', 0);
                // $event['request']->setHeader('Content-Type', 'application/json');

                echo sprintf("request.path: %s\n", $event['request']->getPath() );
                echo sprintf("request.url: %s\n", $event['request']->getUrl() );
                echo sprintf("request.state: %s\n", $event['request']->getState() );
                echo sprintf("request.method: %s\n", $event['request']->getMethod() );
                echo sprintf("request.query: %s\n", $event['request']->getQuery() );

                // check if Guzzle\Http\Message\Request has a json body to validate
                echo sprintf("%s->request: %s\n", __METHOD__, get_class($event['request']) );

                //echo sprintf("event: %s", print_r($event, true));
                //echo sprintf("request: %s", get_class($request));
                //echo sprintf("url: %s", $request->getUrl());

                if (preg_match("/shipping\/v1\/address$/", $event['request']->getPath()) ) {

                }

                if (preg_match("/shipping\/v1\/prices\/items$/", $event['request']->getPath())) {
                    $service = new GetItemPrices([]);
                    $body = json_decode($event['request']->getBody());
                    //echo sprintf("%s->body: %s\n", __METHOD__, print_r($body, true));
                    $validate = $service->validateRequest($event['request']->getBody());
                    echo sprintf("%s->validate: %s\n", __METHOD__, print_r($validate, true));
                }

                if (preg_match("/shipping\/v1\/shipments$/", $event['request']->getPath())) {
                    $service = new CreateShipments([]);
                    $body = json_decode($event['request']->getBody());
                    //echo sprintf("%s->body: %s\n", __METHOD__, print_r($body, true));
                    $validate = $service->validateRequest($event['request']->getBody());
                    echo sprintf("%s->validate: %s\n", __METHOD__, print_r($validate, true));
                }
/*
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
