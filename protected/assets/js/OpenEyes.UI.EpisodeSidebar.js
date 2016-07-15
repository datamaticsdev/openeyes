/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

(function(exports) {
    /**
     * EpisodeSideBar constructor. The EpisodeSideBar manages the controls of the patient episode side bar when in single
     * episode behaviour, managing the sorting and grouping of the patient events.
     *
     * @param options
     * @constructor
     */
    function EpisodeSidebar(element, options) {
        this.element = $(element);
        this.options = $.extend(true, {}, EpisodeSidebar._defaultOptions, options);
        this.create();
    }

    var groupings = [
        {id: 'none', label: 'None'},
        {id: 'event-year-display', label: 'Year'},
        {id: 'event-date-display', label: 'Date'},
        {id: 'event-type', label: 'Type'},
        {id: 'subspecialty', label: 'Subspecialty'}
    ];

    EpisodeSidebar._defaultOptions = {
        switch_firm_text: 'Please switch firm to add an event to this episode',
        user_subspecialty: null,
        event_button_selector: '#add-event',
        subspecialty_labels: {},
        event_list_selector: '.events li',
        grouping_picker_class: 'grouping-picker',
        default_sort: 'desc'
    };

    var sidebarCookiePrefix = 'oe-sidebar-state-';

    EpisodeSidebar.prototype.loadState = function() {
        var self = this;
        if (typeof(Storage) !== "undefined") {
            state = sessionStorage.getItem(sidebarCookiePrefix + self.element.attr('id'));
            if (state) {
                stateObj = JSON.parse(state);
                if (stateObj.sortOrder)
                    self.sortOrder = stateObj.sortOrder;
                if (stateObj.grouping)
                    self.grouping = stateObj.grouping;
            }
        }
    };

    EpisodeSidebar.prototype.saveState = function() {
        var self = this;
        if (typeof(Storage) !== "undefined") {
            var state = {
                sortOrder: self.sortOrder,
                grouping: self.grouping
            };
            sessionStorage.setItem(sidebarCookiePrefix + self.element.attr('id'), JSON.stringify(state));
        }
    };

    EpisodeSidebar.prototype.create = function() {
        var self = this;
        self.subspecialty = self.options.user_subspecialty;
        if (self.options.default_sort == 'asc') {
            self.sortOrder = 'asc';
        }
        else {
            self.sortOrder = 'desc';
        }
        self.grouping = {
            id: groupings[0].id
        };

        self.lastSort = null;
        self.loadState();

        self.addControls();

        self.updateGrouping();

        $(self.options.event_button_selector).unbind();

        $(document).on('click', self.options.event_button_selector + '.enabled', function() {
            if (self.subspecialty)
                self.openNewEventDialog();
        });

        self.element.on('click', '.collapse-all', function(e) {
            self.collapseAll();
            e.preventDefault();
        });

        self.element.on('click', '.expand-all', function(e) {
            self.expandAll();
            e.preventDefault();
        });

        self.element.on('click', '.grouping-collapse', function(e) {
            self.collapseGrouping($(e.target).parents('.grouping-container'));
            e.preventDefault();
        });

        self.element.on('click', '.grouping-expand', function(e) {
            self.expandGrouping($(e.target).parents('.grouping-container'));
            e.preventDefault();
        });
    };

    EpisodeSidebar.prototype.getSubspecialtyLabel = function() {
        if (this.subspecialty) {
            return this.options.subspecialty_labels[this.subspecialty];
        }
        else {
            return "Support services"
        }
    };

    EpisodeSidebar.prototype.openNewEventDialog = function() {
        var self = this;
        if (!self.newEventDialog) {
            self.newEventDialog = new OpenEyes.UI.Dialog({
                destroyOnClose: false,
                title: 'Add a new ' + self.getSubspecialtyLabel() + ' event',
                content: Mustache.render($('#add-new-event-template').html(), {
                    subspecialty: self.getSubspecialtyLabel()
                }),
                dialogClass: 'dialog event add-event',
                width: 580,
                id: 'add-new-event-dialog',
            });

        }
        self.newEventDialog.open();
    };

    EpisodeSidebar.prototype.orderEvents = function() {
        var self = this;
        if (self.lastSort == self.sortOrder)
            return;

        var items = this.element.find(this.options.event_list_selector);
        var parent = items.parent();

        function dateSort(b, a) {
            var edA = (new Date($(a).data('event-date'))).getTime();
            var cdA = (new Date($(a).data('created-date'))).getTime();
            var edB = (new Date($(b).data('event-date'))).getTime();
            var cdB = (new Date($(b).data('created-date'))).getTime();
            var ret = null;
            // for some reason am unable to do a chained ternery operator for the comparison, hence the somewhat convoluted
            // if statements to perform the comparison here.
            if (edA === edB) {
                if (cdA === cdB) {
                    ret = 0;
                }
                else {
                    ret = cdA < cdB ? -1 : 1;
                }
            }
            else {
                ret = edA < edB ? -1 : 1;
            }
            return ret;
        }
        var sorted = items.sort(dateSort);

        if (self.sortOrder == 'asc')
            sorted = sorted.get().reverse();

        self.lastSort = self.sortOrder;

        parent.append(sorted);
    };

    EpisodeSidebar.prototype.addControls = function() {
        var self = this;
        var controls = '';
        controls += self.getGroupingPicker();
        controls += self.getListControls();

        $(controls).insertBefore(self.element.find(self.options.event_list_selector).parent());

        self.element.on('change', '.' + self.options.grouping_picker_class, function(e) {
            self.grouping.id = $(e.target).val();
            self.grouping.state = null;
            self.updateGrouping();
            self.saveState();
        });

        self.element.on('click', '.sorting-order', function(e) {
            e.preventDefault();
            if ($(e.target).hasClass('fa-sort-asc')) {
                self.sortOrder = 'asc';
                $(e.target).removeClass('fa-sort-asc').addClass('fa-sort-desc');
            }
            else {
                self.sortOrder = 'desc';
                $(e.target).removeClass('fa-sort-desc').addClass('fa-sort-asc');
            }

            self.updateGrouping();
            self.saveState();
        });
    }

    EpisodeSidebar.prototype.getGroupingPicker = function() {
        var self = this;
        var select = '<span class="sidebar-grouping"><label for="grouping-picker">Grp by:</label>';
        select += '<select name="grouping-picker" class="' + self.options.grouping_picker_class + '">';
        $(groupings).each(function() {
            select += '<option value="' + this.id +'"';
            if (self.grouping && self.grouping.id == this.id)
                select += ' selected';
            select += '>' + this.label + '</option>';
        });
        select += '</select></span>';

        return select;
    };

    EpisodeSidebar.prototype.getListControls = function() {
        var controls = '<div class="list-controls"><a href="#" class="collapse-all">collapse all</a> | <a href="#" class="expand-all">expand all</a>';
        controls += '<a href="#" class="sorting-order" style="float: right;"><span class="fa fa-sort-asc"></span></a>';
        controls += '</div>';
        return controls;
    };

    EpisodeSidebar.prototype.resetGrouping = function() {
        this.element.find('.grouping-container').remove();
        this.orderEvents();
        this.element.find(this.options.event_list_selector).parent().show();
    };

    EpisodeSidebar.prototype.updateGrouping = function() {
        var self = this;
        self.resetGrouping();
        if (self.grouping.id == 'none')
            return;

        itemsByGrouping = {};
        groupingVals = [];
        self.element.find(self.options.event_list_selector).each(function() {
            var groupingVal = $(this).data(self.grouping.id);
            if (!groupingVal) {
                console.log('ERROR: missing grouping data attribute ' + self.grouping.id);
            }
            else {
                if (!itemsByGrouping[groupingVal]) {
                    itemsByGrouping[groupingVal] = [this];
                    groupingVals.push(groupingVal);
                }
                else {
                    itemsByGrouping[groupingVal].push(this);
                }
            }
        });

        var groupingElements = '';
        $(groupingVals).each(function() {
            var grouping = '<div class="grouping-container" data-grouping-val="' + this + '">' +
                '<h3>'+this+'<span style="float:right">' +
                '<span class="grouping-expand fa fa-plus-square"></span> ' +
                '<span class="grouping-collapse fa fa-minus-square"></span></span></h3>' +
                '<ol class="events">';

            $(itemsByGrouping[this]).each(function() {
                grouping += $(this).prop('outerHTML');
            });
            grouping += '</ol></div>';
            groupingElements += grouping;
        });

        $(groupingElements).insertAfter(self.element.find(this.options.event_list_selector).parent());
        self.element.find(this.options.event_list_selector).parent().hide();
        // TODO: here we should expand or collapse based on current state
        self.processGroupingState();

    };

    EpisodeSidebar.prototype.setGroupingState = function(groupingValue, state) {
        if (this.grouping.state == undefined)
            this.grouping.state = {};
        this.grouping.state[groupingValue] = state;
    };

    EpisodeSidebar.prototype.expandGrouping = function(element, saveState) {
        var self = this;
        if (saveState == undefined)
            saveState = true;

        element.find('.grouping-expand').hide();
        element.find('ol.events').show();
        element.find('.grouping-collapse').show();

        element.each(function() {
            self.setGroupingState($(this).data('grouping-val'),'expand');
        });

        if (saveState)
            this.saveState();
    };

    EpisodeSidebar.prototype.collapseGrouping = function(element, saveState) {
        var self = this;
        if (saveState == undefined)
            saveState = true;

        element.find('.grouping-collapse').hide();
        element.find('ol.events').hide();
        element.find('.grouping-expand').show();
        element.each(function() {
            self.setGroupingState($(this).data('grouping-val'), 'collapse');
        });

        if (saveState)
            this.saveState();
    };

    EpisodeSidebar.prototype.expandAll = function() {
        this.expandGrouping(this.element.find('.grouping-container'), false);
        this.saveState();
    };

    EpisodeSidebar.prototype.collapseAll = function() {
        this.collapseGrouping(this.element.find('.grouping-container'), false);
        this.saveState();
    };
    //TODO: loading is not working, need to verify where we're at!!
    EpisodeSidebar.prototype.processGroupingState = function() {
        var self = this;
        if (self.grouping.state == undefined) {
            self.expandAll();
        }
        else {
            self.element.find('.grouping-container').each(function () {
                if (self.grouping.state[$(this).data('grouping-val')] == 'collapse') {
                    self.collapseGrouping($(this), false);
                }
                else {
                    self.expandGrouping($(this), false);
                }
            });
        }
    };

    exports.EpisodeSidebar = EpisodeSidebar;

}(OpenEyes.UI));