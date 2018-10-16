window.Unison = (function() {

    'use strict';

    let win = window;
    let doc = document;
    let head = doc.head;
    let eventCache = {};
    let unisonReady = false;
    let currentBP;

    let util = {
        parseMQ : function(el) {
            let str = win.getComputedStyle(el, null).getPropertyValue('font-family');
            return str.replace(/"/g, '').replace(/'/g, '');
        },
        debounce : function(func, wait, immediate) {
            let timeout;
            return function() {
                let context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    timeout = null;
                    if (!immediate) {
                        func.apply(context, args);
                    }
                }, wait);
                if (immediate && !timeout) {
                    func.apply(context, args);
                }
            };
        },
        isObject : function(e) { return typeof e === 'object'; },
        isUndefined : function(e) { return typeof e === 'undefined'; }
    };

    let events = {
        on : function(event, callback) {
            if ( !util.isObject(eventCache[event]) ) {
                eventCache[event] = [];
            }
            eventCache[event].push(callback);
        },
        emit : function(event, data) {
            if ( util.isObject(eventCache[event]) ) {
                let eventQ = eventCache[event].slice();
                for ( let i = 0; i < eventQ.length; i++ ) {
                    eventQ[i].call(this, data);
                }
            }
        }
    };

    let breakpoints = {
        all : function() {
            let BPs = {};
            let allBP = util.parseMQ(doc.querySelector('title')).split(',');
            for ( let i = 0; i < allBP.length; i++ ) {
                let mq = allBP[i].trim().split(' ');
                BPs[mq[0]] = mq[1];
            }
            return ( unisonReady ) ? BPs : null ;
        },
        now : function(callback) {
            let nowBP = util.parseMQ(head).split(' ');
            let now = {
                name : nowBP[0],
                width : nowBP[1]
            };
            return ( unisonReady ) ? (( util.isUndefined(callback) ) ? now : callback(now)) : null ;
        },
        update : function() {
            breakpoints.now(function(bp) {
                if ( bp.name !== currentBP ) {
                    events.emit(bp.name);
                    events.emit('change', bp);
                    currentBP = bp.name;
                }
            });
        }
    };

    win.onresize = util.debounce(breakpoints.update, 100);
    doc.addEventListener('DOMContentLoaded', function(){
        unisonReady = win.getComputedStyle(head, null).getPropertyValue('clear') !== 'none';
        breakpoints.update();
    });

    return {
        fetch : {
            all : breakpoints.all,
            now : breakpoints.now
        },
        on : events.on,
        emit : events.emit,
        util : {
            debounce : util.debounce,
            isObject : util.isObject
        }
    };

})();