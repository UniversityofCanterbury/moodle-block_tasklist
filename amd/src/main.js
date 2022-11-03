// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * @module      block_tasklist/main
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Pending from 'core/pending';
import Ajax from 'core/ajax';

const ACTIVE_LISTS = [];
let CURRENT_ITEM = null;

/**
 * Initialise new list
 *
 * @param {Integer} blockid
 */
export const init = (blockid) => {
    // Load list
    let block = document.querySelector(blockid);
    let instanceid = block.dataset.instanceid;
    ACTIVE_LISTS[blockid] = instanceid;
    fetchList(instanceid);

    let addButton = document.querySelector('div[data-instanceid="' + instanceid + '"] .block-tasklist-input-button');
    addButton.addEventListener('click', addItem);

    let taskInput = document.querySelector('div[data-instanceid="' + instanceid + '"] #block-tasklist-input-' + instanceid);
    taskInput.addEventListener('keydown', (e) => {
        if (e.code === 'Enter') {
            addItem(e);
        }
    });

    let list = document.querySelector('div[data-instanceid="' + instanceid + '"] ul.block-tasklist-list');
    list.addEventListener('dragover', (event) => {
        event.preventDefault();
    });
};

/**
 * Add item to list via webservice
 *
 * @param {Event} clickEvent
 */
const addItem = (clickEvent) => {
    let instanceid = clickEvent.target.closest('.block-tasklist').dataset.instanceid;
    let name = document.querySelector('div[data-instanceid="' + instanceid + '"] input.block-tasklist-input');
    let list = document.querySelector('div[data-instanceid="' + instanceid + '"] ul.block-tasklist-list');

    if (name.value === '') {
        return;
    }

    Ajax.call([{
        methodname: 'block_tasklist_add_item',
        args: {
            instanceid: instanceid,
            name: name.value,
            complete: false,
            position: list.childElementCount,
        },
    }])[0]
        .then(renderItem)
        .fail(Notification.exception);

    name.value = '';
};

/**
 * Load list from webservice
 *
 * @param {Integer} instanceid
 * @returns {Promise<*>}
 */
const fetchList = async (instanceid) => {
    return Ajax.call([{
        methodname: 'block_tasklist_get_items',
        args: {
            instanceid: instanceid,
        },
    }])[0]
        .then(addItems)
        .fail(Notification.exception);
};

/**
 * Iterate fresh items and render.
 *
 * @param {Array} items
 * @returns {Promise|*}
 */
const addItems = items => {
    if (!items.length) {
        return Promise.resolve();
    }

    const pendingPromise = new Pending('blocks/tasklist:addItems');
    items.forEach(item => renderItem(item));

    return pendingPromise.resolve();
};

/**
 * Render item in list
 *
 * @param {Object} itemData
 */
const renderItem = (itemData) => {
    Templates.render('block_tasklist/task_item', itemData).done(function(html) {
        let list = document.querySelector('div[data-instanceid="' + itemData.instanceid + '"] ul.block-tasklist-list');
        list.insertAdjacentHTML('beforeend', html);

        let item = document.querySelector('li[data-itemid="' + itemData.id + '"]');
        addCompleteItemEvents(item);
        addItemDragDrop(item);
        addItemEditEvent(item);
        addDeleteEvent(item);
    }).fail(Notification.exception);
};

/**
 * Add event listeners to provide ability to edit task name
 *
 * @param {HTMLElement} item
 */
const addItemEditEvent = (item) => {
    let itemname = item.querySelector('label.block-tasklist-itemname');
    let input = item.querySelector('input.block-tasklist-item-edit-input');
    item.querySelector('button.block-tasklist-item-edit').addEventListener('click', () => {
        // Check if user is attempting to save the edit by using the edit icon.
        if (itemname.style.display === 'none') {
            updateItemName(item, itemname, input);
        } else {
            // Show item name input.
            itemname.style.display = 'none';
            input.style.display = 'inline-block';
            item.draggable = false; // Remove draggable so cursor is usable in input.
            input.focus();
            // Place cursor at end of text.
            let value = input.value;
            input.value = '';
            input.value = value;
        }
    });
    item.querySelector('input.block-tasklist-item-edit-input').addEventListener('keydown', (e) => {
        if (e.code === 'Enter') {
            updateItemName(item, itemname, input);
        }
    });
};

/**
 * Finishes the interaction with the item name input
 *
 * @param {HTMLElement} item
 * @param {HTMLLabelElement} itemname
 * @param {HTMLInputElement} input
 */
const updateItemName = (item, itemname, input) => {
    item.draggable = true;
    itemname.innerText = input.value;
    itemname.style.display = 'inline-block';
    input.style.display = 'none';
    let itemData = {
        id: parseInt(item.dataset.itemid),
        name: item.querySelector('label.block-tasklist-itemname').innerText,
        position: parseInt(item.dataset.position),
        complete: item.classList.contains('block-tasklist-item-complete')
    };
    updateItems(parseInt(item.dataset.instanceid), [itemData]);
};

/**
 * Add complete item events
 *
 * @param {HTMLElement} item
 */
const addCompleteItemEvents = (item) => {
    item.querySelector('button.block-tasklist-item-complete').addEventListener('click', () => {
        let itemData = {
            id: parseInt(item.dataset.itemid),
            name: item.querySelector('label.block-tasklist-itemname').innerText,
            position: parseInt(item.dataset.position),
        };
        if (item.classList.contains('block-tasklist-item-complete')) {
            item.classList.remove('block-tasklist-item-complete');
            itemData.complete = false;
        } else {
            item.classList.add('block-tasklist-item-complete');
            itemData.complete = true;
        }
        updateItems(parseInt(item.dataset.instanceid), [itemData]);
    });
};

/**
 * Add delete button events
 *
 * @param {HTMLElement} item
 */
const addDeleteEvent = (item) => {
    item.querySelector('.block-tasklist-item-delete').addEventListener('click', () => {
        item.remove();
        deleteItem(item.dataset.instanceid, item.dataset.itemid);
        recalculatePositions(item.dataset.instanceid);
    });
};

/**
 * Add relevant event listeners to provide drag/drop functionality to rearrange list.
 *
 * @param {HTMLElement} item
 */
const addItemDragDrop = (item) => {
    item.addEventListener('dragstart', () => {
        CURRENT_ITEM = item;
    });
    item.addEventListener('drop', (event) => {
        event.preventDefault();
        if (item !== CURRENT_ITEM) {
            let currentPos = 0, droppedPos = 0;
            let children = item.parentElement.children;
            for (let i = 0; i < children.length; i++) {
                if (CURRENT_ITEM === children[i]) {
                    currentPos = i;
                }
                if (item === children[i]) {
                    droppedPos = i;
                }
            }
            if (currentPos < droppedPos) {
                item.parentNode.insertBefore(CURRENT_ITEM, item.nextSibling);
                recalculatePositions(parseInt(item.dataset.instanceid));
            } else {
                item.parentNode.insertBefore(CURRENT_ITEM, item);
                recalculatePositions(parseInt(item.dataset.instanceid));
            }
        }
    });
};

/**
 * Recalculate all item positions and update using webservice.
 *
 * @param {Integer} instanceid
 */
const recalculatePositions = (instanceid) => {
    let list = document.querySelector('div[data-instanceid="' + instanceid + '"] ul.block-tasklist-list');
    let children = list.children;
    let toUpdate = [];

    for (let i = 0; i < children.length; i++) {
        let item = children[i];
        if (item.dataset.position != i) {
            item.dataset.position = i;
            let itemData = {
                id: parseInt(item.dataset.itemid),
                name: item.querySelector('label.block-tasklist-itemname').innerText,
                position: parseInt(item.dataset.position),
                complete: item.classList.contains('block-tasklist-item-complete')
            };
            toUpdate.push(itemData);
        }
    }

    updateItems(instanceid, toUpdate);
};

/**
 * Bulk update item details
 *
 * @param {Integer} instanceid
 * @param {Array} items
 */
const updateItems = (instanceid, items) => {
    Ajax.call([{
        methodname: 'block_tasklist_update_items',
        args: {
            instanceid: instanceid,
            items: items
        },
    }])[0].fail(Notification.exception);
};

/**
 * Call delete item webservice.
 *
 * @param {Integer} instanceid
 * @param {Integer} itemid
 */
const deleteItem = (instanceid, itemid) => {
    Ajax.call([{
        methodname: 'block_tasklist_delete_item',
        args: {
            instanceid: instanceid,
            itemid: itemid
        },
    }])[0].fail(Notification.exception);
};
