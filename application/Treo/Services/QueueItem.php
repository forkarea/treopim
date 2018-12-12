<?php
/**
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2018 TreoLabs GmbH
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

namespace Treo\Services;

use Espo\Core\Exceptions\BadRequest;
use Espo\ORM\Entity;

/**
 * Class QueueItem
 *
 * @author r.ratsun@zinitsolutions.com
 */
class QueueItem extends \Espo\Core\Templates\Services\Base
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @inheritdoc
     */
    public function prepareEntityForOutput(Entity $entity)
    {
        parent::prepareEntityForOutput($entity);

        // prepare entity
        $entity->set('actions', $this->getItemActions($entity));
    }

    /**
     * @inheritdoc
     */
    public function updateEntity($id, $data)
    {
        // get entity
        $entity = $this->getEntity($id);

        if (isset($data->status) && $data->status == 'Canceled'
            && in_array($entity->get('status'), ['Running', 'Failed', 'Success'])) {
            throw new BadRequest($this->exception('Queue item cannot be changed'));
        }

        return parent::updateEntity($id, $data);
    }

    /**
     * @inheritdoc
     */
    protected function init()
    {
        parent::init();

        $this->addDependency('serviceFactory');
        $this->addDependency('language');
    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected function getItemActions(Entity $entity): array
    {
        // prepare result
        $result = [];

        // prepare action statuses
        $statuses = ['Pending', 'Running', 'Success', 'Failed'];

        if (!empty($serviceName = $entity->get('serviceName')) && in_array($entity->get('status'), $statuses)) {
            // create service
            if (!isset($this->services[$serviceName])) {
                $this->services[$serviceName] = $this->getInjection('serviceFactory')->create($serviceName);
            }

            // prepare methodName
            $methodName = "get" . $entity->get('status') . "StatusActions";

            // prepare result
            $result = $this->services[$serviceName]->$methodName($entity);
        }

        return $result;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function exception(string $key): string
    {
        return $this->getInjection('language')->translate($key, 'exceptions', 'QueueItem');
    }
}