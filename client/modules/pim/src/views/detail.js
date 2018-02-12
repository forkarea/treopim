Espo.define('pim:views/detail', 'views/detail',
    Dep => Dep.extend({

        selectBoolFilterLists: {},

        boolFilterData: {},

        getBoolFilterData(link) {
            let data = {};
            this.selectBoolFilterLists[link].forEach(item => {
                if (this.boolFilterData[link] && typeof this.boolFilterData[link][item] === 'function') {
                    data[item] = this.boolFilterData[link][item].call(this);
                }
            });
            return data;
        },

        updateRelationshipPanel(name) {
            let bottom = this.getView('record').getView('bottom');
            if (bottom) {
                let rel = bottom.getView(name);
                if (rel) {
                    if (rel.collection) {
                        rel.collection.fetch();
                    }
                    if (typeof rel.updateGrid === 'function') {
                        rel.updateGrid();
                    }
                    if (typeof rel.setupList === 'function') {
                        rel.setupList();
                    }
                }
            }
        },

        actionCreateRelatedConfigured: function (data) {
            data = data || {};

            let link = data.link;
            let scope = this.model.defs['links'][link].entity;
            let foreignLink = this.model.defs['links'][link].foreign;
            let fullFormDisabled = data.fullFormDisabled;
            let afterSaveCallback = data.afterSaveCallback;
            let panelView = this.getPanelView(link);

            let attributes = {};

            if (this.relatedAttributeFunctions[link] && typeof this.relatedAttributeFunctions[link] == 'function') {
                attributes = _.extend(this.relatedAttributeFunctions[link].call(this), attributes);
            }

            Object.keys(this.relatedAttributeMap[link] || {}).forEach(function (attr) {
                attributes[this.relatedAttributeMap[link][attr]] = this.model.get(attr);
            }, this);

            this.notify('Loading...');

            let viewName = this.getMetadata().get('clientDefs.' + scope + '.modalViews.edit') || 'views/modals/edit';
            this.createView('quickCreate', viewName, {
                scope: scope,
                relate: {
                    model: this.model,
                    link: foreignLink,
                },
                attributes: attributes,
                fullFormDisabled: fullFormDisabled
            }, function (view) {
                view.render();
                view.notify(false);
                this.listenToOnce(view, 'after:save', function () {
                    this.updateRelationshipPanel(link);
                    this.model.trigger('after:relate', link);

                    if (afterSaveCallback && panelView && typeof panelView[afterSaveCallback] === 'function') {
                        panelView[afterSaveCallback](view.getView('edit').model);
                    }
                }, this);
            }.bind(this));
        },

        actionCreateRelatedEntity(data) {
            let link = data.link;
            let scope = data.scope || this.model.defs['links'][link].entity;
            let afterSaveCallback = data.afterSaveCallback;
            let panelView = this.getPanelView(link);

            let viewName = this.getMetadata().get('clientDefs.' + scope + '.modalViews.edit') || 'views/modals/edit';

            this.notify('Loading...');
            this.createView('quickCreate', viewName, {
                scope: scope,
                attributes: {},
                fullFormDisabled: true
            }, function (view) {
                view.render();
                view.notify(false);
                this.listenToOnce(view, 'after:save', () => {
                    if (afterSaveCallback && panelView && typeof panelView[afterSaveCallback] === 'function') {
                        panelView[afterSaveCallback](view.getView('edit').model);
                    }
                }, this);
            }.bind(this));
        },

        actionSelectRelatedEntity(data) {
            let link = data.link;
            let scope = data.scope || this.model.defs['links'][link].entity;
            let afterSelectCallback = data.afterSelectCallback;
            let boolFilterListCallback = data.boolFilterListCallback;
            let boolFilterDataCallback = data.boolFilterDataCallback;
            let panelView = this.getPanelView(link);

            let filters = Espo.Utils.cloneDeep(this.selectRelatedFilters[link]) || {};
            for (let filterName in filters) {
                if (typeof filters[filterName] == 'function') {
                    let filtersData = filters[filterName].call(this);
                    if (filtersData) {
                        filters[filterName] = filtersData;
                    } else {
                        delete filters[filterName];
                    }
                }
            }

            let primaryFilterName = data.primaryFilterName || this.selectPrimaryFilterNames[link] || null;
            if (typeof primaryFilterName == 'function') {
                primaryFilterName = primaryFilterName.call(this);
            }

            let boolFilterList = data.boolFilterList || Espo.Utils.cloneDeep(this.selectBoolFilterLists[link] || []);
            if (typeof boolFilterList == 'function') {
                boolFilterList = boolFilterList.call(this);
            }

            if (boolFilterListCallback && panelView && typeof panelView[boolFilterListCallback] === 'function') {
                boolFilterList = panelView[boolFilterListCallback]();
            }

            let boolfilterData = [];
            if (boolFilterDataCallback && panelView && typeof panelView[boolFilterDataCallback] === 'function') {
                boolfilterData = panelView[boolFilterDataCallback](boolFilterList);
            }

            let viewName = this.getMetadata().get('clientDefs.' + scope + '.modalViews.select') || 'views/modals/select-records';

            this.notify('Loading...');
            this.createView('dialog', viewName, {
                scope: scope,
                multiple: true,
                createButton: false,
                listLayout:  data.listLayout,
                filters: filters,
                massRelateEnabled: false,
                primaryFilterName: primaryFilterName,
                boolFilterList: boolFilterList,
                boolFilterData: boolfilterData
            }, function (dialog) {
                dialog.render();
                this.notify(false);
                dialog.once('select', selectObj => {
                    if (selectObj && selectObj.length) {
                        if (afterSelectCallback && panelView && typeof panelView[afterSelectCallback] === 'function') {
                            panelView[afterSelectCallback](selectObj);
                        } else {
                            let data = {
                                ids: selectObj.map(item => item.id)
                            };
                            this.ajaxPostRequest(`${this.scope}/${this.model.id}/${link}`, data)
                                .then(() => {
                                    this.notify('Linked', 'success');
                                    this.updateRelationshipPanel(link);
                                    this.model.trigger('after:relate', link);
                                });
                        }
                    }
                }, this);
            }.bind(this));
        },

        getPanelView(name) {
            let panelView;
            let recordView = this.getView('record');
            if (recordView) {
                let bottomView = recordView.getView('bottom');
                if (bottomView) {
                    panelView = bottomView.getView(name)
                }
            }
            return panelView;
        }
    })
);
