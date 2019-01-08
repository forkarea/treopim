/*
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
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

Espo.define('treo-core:views/queue-manager/actions/abstract-action', 'view',
    Dep => Dep.extend({

        template: 'treo-core:queue-manager/actions/abstract-action',

        buttonLabel: '',

        actionData: {},

        disabled: false,

        events: {
            'click [data-action="runAction"]': function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (this.canRun()) {
                    this.runAction();
                }
            },
        },

        setup() {
            Dep.prototype.setup.call(this);

            this.actionData = this.options.actionData || this.actionData;
        },

        data() {
            return {
                buttonLabel: this.buttonLabel,
                disabled: this.disabled
            };
        },

        runAction() {
            //run action
        },

        canRun() {
            return !this.disabled;
        }

    })
);

