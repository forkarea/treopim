<?php
/**
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2018 Zinit Solutions GmbH
 * Website: http://www.treopim.com
 *
 * TreoPIM as well as EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TreoPIM as well as EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word
 * and "TreoPIM" word.
 */

declare(strict_types = 1);

namespace Espo\Modules\Pim\ORM\DB\Query;

use Espo\ORM\DB\Query\Mysql as EspoMysql;
use Espo\ORM\IEntity;
use Espo\Modules\Pim\Entities\ProductAttributeValue as Entity;

/**
 * Class of Mysql
 *
 * @author r.ratsun <r.ratsun@zinitsolutions.com>
 */
class Mysql extends EspoMysql
{

    /**
     * Get where
     *
     * @param IEntity $entity
     * @param array $whereClause
     * @param string $sqlOp
     * @param array $params
     * @param int $level
     *
     * @return string
     */
    public function getWhere(IEntity $entity, $whereClause, $sqlOp = 'AND', &$params = array(), $level = 0)
    {
        // prepare result
        $result = parent::getWhere($entity, $whereClause, $sqlOp, $params, $level);

        /**
         * Injection for Attribute filtering
         */
        if (get_class($entity) == Entity::class) {
            // prepare pattern
            $pattern = '/^(product_attribute_value\.value)(>|<|>=|<=)\'(.*)\'$/';

            // prepare str
            $str = preg_replace('/\s+/', '', $result);

            if (preg_match_all($pattern, $str, $matches) && isset($matches[3][0]) && is_numeric($matches[3][0])) {
                $result = str_replace("'", "", $result);
            }
        }

        return $result;
    }
}