/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
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

Espo.define('treo-core:views/fields/unit', 'views/fields/float',
    Dep => Dep.extend({

        type: 'unit',

        editTemplate: 'treo-core:fields/unit/edit',

        detailTemplate: 'treo-core:fields/unit/detail',

        listTemplate: 'treo-core:fields/unit/list',

        events: _.extend({
            'click button[data-action="showUnitSelection"]': function (e) {
                this.showUnitSelection();
            }
        }, Dep.prototype.events),

        data() {
            let data = Dep.prototype.data.call(this);

            data.unitFieldName = this.unitFieldName;
            data.unitValue = this.model.get(this.unitFieldName);
            data.valueOrUnit = !!(data.value || data.unitValue);
            return data;
        },

        setup() {
            Dep.prototype.setup.call(this);

            this.unitFieldName = this.name + 'Unit';
        },

        showUnitSelection() {
            this.createView('unitSelection', 'treo-core:views/modals/unit-selection', {
                model: this.model
            }, view => {
                view.listenTo(view, 'unit-selected', abbr => {
                    if (abbr) {
                        this.model.set({[this.unitFieldName]: abbr});
                        this.trigger('change');
                    }
                });
                view.render();
            });
        },

        formatNumber(value) {
            if (this.mode === 'list' || this.mode === 'detail') {
                return this.formatNumberDetail(value);
            }
            return this.formatNumberEdit(value);
        },

        formatNumberEdit(value) {
            if (value !== null) {
                let parts = value.toString().split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.thousandSeparator);

                return parts.join(this.decimalMark);
            }
            return '';
        },

        formatNumberDetail(value) {
            if (value !== null) {
                let parts = value.toString().split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.thousandSeparator);

                return parts.join(this.decimalMark);
            }
            return '';
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.mode === 'edit') {
                this.$unit = this.$el.find('.unit-value');
                this.$unit.on('change', function () {
                    this.model.set(this.unitFieldName, this.$unit.val());
                }.bind(this));
            }
        },

        fetch: function () {
            let data = {};

            let value = this.$element.val();
            value = this.parse(value);

            data[this.name] = value;
            data[this.unitFieldName] = this.$unit.val();

            return data;
        },
    })
);

