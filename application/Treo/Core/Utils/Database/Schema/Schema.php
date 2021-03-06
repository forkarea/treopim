<?php
/**
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2019 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2019 TreoLabs GmbH
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
declare(strict_types=1);

namespace Treo\Core\Utils\Database\Schema;

use Espo\Core\Exceptions\Error;
use Treo\Traits\ContainerTrait;

/**
 * Class Schema
 *
 * @author r.ratsun r.ratsun@treolabs.com
 */
class Schema extends \Espo\Core\Utils\Database\Schema\Schema
{
    use ContainerTrait;

    /**
     * @inheritdoc
     */
    public function rebuild($entityList = null)
    {
        if (!$this->getConverter()->process()) {
            return false;
        }

        // get current schema
        $currentSchema = $this->getCurrentSchema();

        // get entityDefs
        $entityDefs = $this->triggered(
            'Schema',
            'prepareEntityDefsBeforeRebuild',
            ['data' => $this->ormMetadata->getData()]
        )['data'];

        // get metadata schema
        $metadataSchema = $this->schemaConverter->process($entityDefs, $entityList);

        // init rebuild actions
        $this->initRebuildActions($currentSchema, $metadataSchema);

        // execute rebuild actions
        $this->executeRebuildActions('beforeRebuild');

        // get queries
        $queries = $this->getDiffSql($currentSchema, $metadataSchema);

        // berore rebuild action
        $queries = $this->triggered('Schema', 'beforeRebuild', ['queries' => $queries])['queries'];

        // run rebuild
        $result = true;
        $connection = $this->getConnection();
        foreach ($queries as $sql) {
            $GLOBALS['log']->info('SCHEMA, Execute Query: ' . $sql);
            try {
                $result &= (bool)$connection->executeQuery($sql);
            } catch (\Exception $e) {
                $GLOBALS['log']->alert('Rebuild database fault: ' . $e);
                $result = false;
            }
        }

        // execute rebuild action
        $this->executeRebuildActions('afterRebuild');

        // after rebuild action
        $result = $this->triggered(
            'Schema',
            'afterRebuild',
            ['result' => (bool)$result, 'queries' => $queries]
        )['result'];

        return $result;
    }

    /**
     * Triggered event
     *
     * @param string $target
     * @param string $action
     * @param array  $data
     *
     * @return array
     */
    protected function triggered(string $target, string $action, array $data = []): array
    {
        return $this->getContainer()->get('eventManager')->triggered($target, $action, $data);
    }
}
