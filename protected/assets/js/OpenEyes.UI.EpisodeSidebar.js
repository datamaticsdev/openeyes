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

    EpisodeSidebar._defaultOptions = {
        switch_firm_text: 'Please switch firm to add an event to this episode',
        subspecialty: null,
        event_button_selector: '#add-event',
        subspecialty_labels: {},
        event_list_selector: '.events li'
    };

    EpisodeSidebar.prototype.create = function() {
        var self = this;
        self.setSubspecialty(self.options.subspecialty);
        self.orderEvents();

        $(document).on('click', self.options.event_button_selector + '.enabled', function() {
            self.openNewEventDialog();
        });
    };

    EpisodeSidebar.prototype.setSubspecialty = function(subspecialty) {
        this.subspecialty = subspecialty;
        if (this.subspecialty && this.subspecialty == this.options.subspecialty) {
            // something selected, and matches the current session subspecialty.
            this.enableEventButton();
        }
        else {
            this.disableEventButton();
        }
    };

    EpisodeSidebar.prototype.getSubspecialtyLabel = function() {
        if (this.subspecialty) {
            return this.options.subspecialty_labels[this.subspecialty];
        }
        else {
            return "Support services"
        }
    };

    EpisodeSidebar.prototype.enableEventButton = function() {
        $(this.options.event_button_selector).removeClass('disabled').addClass('enabled');
    };

    EpisodeSidebar.prototype.disableEventButton = function() {
        $(this.options.event_button_selector).removeClass('enabled').addClass('disabled');
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
        parent.append(sorted);
    };

    exports.EpisodeSidebar = EpisodeSidebar;

}(OpenEyes.UI));