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

namespace Treo\Composer;

use Treo\Core\Container;

/**
 * Class Cmd
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class Cmd
{
    /**
     * Before update
     */
    public static function preUpdate(): void
    {
        (new PreUpdate())->run();
    }

    /**
     * After install
     */
    public static function postInstall(): void
    {
        // relocate files
        self::relocateFiles();
    }

    /**
     * After update
     */
    public static function postUpdate(): void
    {
        // relocate files
        self::relocateFiles();

        (new PostUpdate())->setContainer(self::getContainer())->run();
    }

    /**
     * @return Container
     */
    protected static function getContainer(): Container
    {
        include "bootstrap.php";

        return (new \Treo\Core\Application())->getContainer();
    }


    /**
     * Relocate files
     */
    protected static function relocateFiles(): void
    {
        if (!defined('CORE_PATH')) {
            // define gloabal variables
            define('CORE_PATH', dirname(dirname(dirname(__DIR__))));

            \Treo\Core\Utils\Mover::update();
        }
    }
}
