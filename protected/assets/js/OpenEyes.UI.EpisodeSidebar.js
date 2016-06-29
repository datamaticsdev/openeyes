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

    EpisodeSidebar.prototype.create = function() {
        var self = this;
        self.subspecialty = self.options.user_subspecialty;
        if (self.options.default_sort == 'asc') {
            self.sortOrder = 'asc';
        }
        else {
            self.sortOrder = 'desc';
        }
        self.lastSort = null;

        self.addControls();

        self.updateGrouping();

        $(self.options.event_button_selector).unbind();

        $(document).on('click', self.options.event_button_selector + '.enabled', function() {
            if (self.subspecialty)
                self.openNewEventDialog();
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

        self.element.on('change', '.' + self.options.grouping_picker_class, function() {
            self.updateGrouping();
        });
    }

    EpisodeSidebar.prototype.getGroupingPicker = function() {
        var self = this;
        var select = '<span style="white-space: nowrap;"><label for="grouping-picker" style="display: inline;">Grp by:</label>';
        select += '<select name="grouping-picker" class="' + self.options.grouping_picker_class + '">';
        $(groupings).each(function() {
            select += '<option value="' + this.id +'">' + this.label + '</option>';
        });
        select += '</select></span>';

        return select;
    };

    EpisodeSidebar.prototype.getListControls = function() {
        var controls = '<div class="list-controls"><a href="#" class="collapse-all">collapse all</a> | <a href="#" class="expand-all">expand all</a></div>';
        return controls;
    };

    EpisodeSidebar.prototype.resetGrouping = function() {
        this.element.find('.grouping-container').remove();
        this.orderEvents();
        this.element.find(this.options.event_list_selector).parent().show();
    };

    EpisodeSidebar.prototype.updateGrouping = function() {
        var self = this;
        var groupingId = self.element.find('.' + self.options.grouping_picker_class).val();

        self.resetGrouping();
        if (groupingId == 'none')
            return;

        itemsByGrouping = {};
        groupingVals = [];
        self.element.find(self.options.event_list_selector).each(function() {
            var groupingVal = $(this).data(groupingId);
            if (!groupingVal) {
                console.log('ERROR: missing grouping data attribute ' + groupingId);
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
            var grouping = '<div class="grouping-container"><h3>'+this+'</h3><ol class="events">';
            $(itemsByGrouping[this]).each(function() {
                grouping += $(this).prop('outerHTML');
            });
            grouping += '</ol></div>';
            groupingElements += grouping;
        });

        $(groupingElements).insertAfter(self.element.find(this.options.event_list_selector).parent());
        self.element.find(this.options.event_list_selector).parent().hide();
        self.element.find('.grouping-container ol.events').show();

    };

    exports.EpisodeSidebar = EpisodeSidebar;

}(OpenEyes.UI));