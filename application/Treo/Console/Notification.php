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

namespace Treo\Console;

/**
 * Class Notification
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class Notification extends AbstractConsole
{
    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return 'Refresh users notifications cache.';
    }

    /**
     * @inheritdoc
     */
    public function run(array $data): void
    {
        if (empty($this->getConfig()->get('isInstalled'))) {
            exit(1);
        }

        // refresh
        $this->refresh();

        self::show('Users notifications cache refreshed successfully', self::SUCCESS, true);
    }

    /**
     * Refresh
     */
    protected function refresh(): void
    {
        // prepare sql
        $sql
            = "SELECT
                  u.id as userId, COUNT(n.id) as count
                FROM
                  user AS u
                LEFT JOIN notification AS n ON n.user_id=u.id AND n.deleted=0 AND n.read=0
                WHERE u.deleted=0
                  AND u.is_active=1
                  AND u.user_name!='system'
                GROUP BY u.id";

        // get data
        $sth = $this->getContainer()->get('entityManager')->getPDO()->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            // prepare content
            $content = [];
            foreach ($data as $row) {
                $content[$row['userId']] = (int)$row['count'];
            }

            // set to file
            file_put_contents('data/notReadCount.json', json_encode($content));
        }
    }
}
