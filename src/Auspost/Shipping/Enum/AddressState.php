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
namespace Auspost\Shipping\Enum;

use Auspost\Common\Enum;

class AddressState extends Enum
{
    const MAX_STATE_LEN = 3;

    const ACT = 'ACT';
    const NSW = 'NSW';
    const NT = 'NT';
    const QLD = 'QLD';
    const SA = 'SA';
    const TAS = 'TAS';
    const VIC = 'VIC';
    const WA = 'WA';

    private static $states = [
        self::ACT, self::NSW, self::NT, self::QLD, self::SA, self::TAS, self::VIC, self::WA
    ];

    /**
     * @param $stateString
     * @return mixed
     * @throws
     */
    public static function normaliseState($stateString) {
        $state = false;
        if (preg_match("/queensland/i", $stateString)) {
            $state = self::QLD;
        }
        elseif (preg_match("/[a-z]+\s+[a-z]+/i", $stateString)) {
            preg_match_all("/[A-Z]/", ucwords(strtolower($stateString)), $matches);
            $state = implode('', $matches[0]);
        }
        elseif (strlen($stateString) >= self::MAX_STATE_LEN) {
            $state = substr(strtoupper($stateString), 0, self::MAX_STATE_LEN);
        }
        //if (!in_array($state, self::$states)) {
        if (!preg_grep(sprintf("/%s/i", $state), self::$states)) {
            throw new \Exception(sprintf("cannot normalise state '%s'!", $stateString));
        }
        else {
            $state = strtoupper($stateString);
        }
        return $state;
    }
}
