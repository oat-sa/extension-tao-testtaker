<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoTestTaker\models\routing;

use oat\tao\model\routing\AbstractApiRoute;

class ApiRoute extends AbstractApiRoute
{
    const REST_CONTROLLER_PREFIX = 'oat\\taoTestTaker\\actions\\Rest';

    /**
     * @inheritdoc
     */
    public static function getControllerPrefix()
    {
        return self::REST_CONTROLLER_PREFIX;
    }
}
