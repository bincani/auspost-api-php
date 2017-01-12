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

use Auspost\Shipping\Enum\AddressState;

class AddressStateTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers Auspost\Shipping\AddressState
     */
    public function testNormaliseStateException()
    {
        $this->expectException(\Exception::class);
        AddressState::normaliseState('blah');
    }

    public function testNormaliseAllStates() {
        $this->assertEquals(AddressState::ACT, AddressState::normaliseState('Australian Capital Territory'));
        $this->assertEquals(AddressState::NSW, AddressState::normaliseState('New South Wales'));
        $this->assertEquals(AddressState::NT, AddressState::normaliseState('Northern Territory'));
        $this->assertEquals(AddressState::QLD, AddressState::normaliseState('Queensland'));
        $this->assertEquals(AddressState::SA, AddressState::normaliseState('South Australia'));
        $this->assertEquals(AddressState::TAS, AddressState::normaliseState('Tasmania'));
        $this->assertEquals(AddressState::VIC, AddressState::normaliseState('Victoria'));
        $this->assertEquals(AddressState::WA, AddressState::normaliseState('Western Australia'));
    }

}
