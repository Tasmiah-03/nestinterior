/**
 * Luxe Cost Calculator — Frontend JS v1.0.0
 *
 * Live calculation engine:
 * • Tier / finish / complexity card selection
 * • Dynamic room row add/remove/reorder
 * • Preset room configurations
 * • Real-time per-room cost display
 * • AJAX server-side calculation with debounce
 * • Animated number counters
 * • Breakdown table rendering
 * • Add-on toggling
 * • Lead capture / email send
 * • Typical size autofill
 */
( function ( $ ) {
    'use strict';

    var RATES    = window.lccData ? lccData.rates    : {};
    var SYMBOL   = window.lccData ? lccData.symbol   : '৳';
    var UNIT     = window.lccData ? lccData.unit     : 'sqft';
    var AJAX_URL = window.lccData ? lccData.ajaxUrl  : '/wp-admin/admin-ajax.php';
    var NONCE    = window.lccData ? lccData.nonce    : '';
    var TIER_INC = window.lccTierIncludes || {};
    var ROOMS    = window.lccRooms || {};

    var $calc       = $( '#lcc-main' );
    var roomIndex   = 1; // Start at 1 (first row is 0)
    var calcTimer   = null;
    var lastResult  = null;

    if ( ! $calc.length ) return;

    /* ============================================================
       INIT
    ============================================================ */
    function init() {
        bindTierCards();
        bindFinishCards();
        bindComplexityCards();
        bindAddRoom();
        bindRoomEvents();
        bindAddonCards();
        bindPresets();
        bindLeadCapture();
        bindBreakdownToggle();

        // Initial calculation after short delay
        setTimeout( recalculate, 300 );
    }

    /* ============================================================
       TIER SELECTION
    ============================================================ */
    function bindTierCards() {
        $calc.on( 'click', '.lcc-tier-card', function () {
            $calc.find( '.lcc-tier-card' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            $( this ).find( 'input' ).prop( 'checked', true );

            // Update includes list
            var tierKey = $( this ).data( 'tier' );
            updateTierIncludes( tierKey );

            // Update tier label in estimate card
            $( '#lcc-tier-label' ).text( $( this ).find( '.lcc-tier-card__name' ).text() );

            scheduleCalc();
        } );
    }

    function updateTierIncludes( tierKey ) {
        var includes = TIER_INC[ tierKey ];
        if ( ! includes ) return;
        var $list = $( '#lcc-includes-list' );
        $list.html( '' );
        $.each( includes, function ( i, item ) {
            $list.append( '<li>' + escHtml( item ) + '</li>' );
        } );
    }

    /* ============================================================
       FINISH SELECTION
    ============================================================ */
    function bindFinishCards() {
        $calc.on( 'click', '.lcc-finish-card', function () {
            $calc.find( '.lcc-finish-card' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            $( this ).find( 'input' ).prop( 'checked', true );
            $( '#lcc-finish-label' ).text( $( this ).find( '.lcc-finish-card__name' ).text() );
            scheduleCalc();
        } );
    }

    /* ============================================================
       COMPLEXITY SELECTION
    ============================================================ */
    function bindComplexityCards() {
        $calc.on( 'click', '.lcc-complexity-card', function () {
            $calc.find( '.lcc-complexity-card' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            $( this ).find( 'input' ).prop( 'checked', true );
            scheduleCalc();
        } );
    }

    /* ============================================================
       ROOM ROWS
    ============================================================ */
    function bindAddRoom() {
        $( '#lcc-add-room' ).on( 'click', function () {
            addRoomRow( 'living_room', 0 );
        } );
    }

    function addRoomRow( type, area ) {
        var template = $( '#lcc-room-template' ).html();
        if ( ! template ) return;

        var idx  = roomIndex++;
        var html = template.replace( /\{\{INDEX\}\}/g, idx );
        var $row = $( html );

        // Set type
        $row.find( '.lcc-room-type' ).val( type );
        // Set area
        if ( area ) $row.find( '.lcc-room-area' ).val( area );
        // Update name attributes
        $row.find( '.lcc-room-type' ).attr( 'name', 'lcc_rooms[' + idx + '][type]' );
        $row.find( '.lcc-room-area' ).attr( 'name', 'lcc_rooms[' + idx + '][area]' );

        // Show remove button
        $row.find( '.lcc-remove-room' ).css( 'visibility', 'visible' );

        $( '#lcc-rooms-list' ).append( $row );
        updateTotalArea();
        scheduleCalc();
    }

    function bindRoomEvents() {
        // Remove row
        $( '#lcc-rooms-list' ).on( 'click', '.lcc-remove-room', function () {
            var $rows = $( '#lcc-rooms-list .lcc-room-row' );
            if ( $rows.length <= 1 ) return; // Keep at least one
            $( this ).closest( '.lcc-room-row' ).remove();
            updateTotalArea();
            scheduleCalc();
        } );

        // Area input change
        $( '#lcc-rooms-list' ).on( 'input change', '.lcc-room-area', function () {
            updateTotalArea();
            scheduleCalc();
        } );

        // Room type change → update typical size hint + recalculate
        $( '#lcc-rooms-list' ).on( 'change', '.lcc-room-type', function () {
            scheduleCalc();
        } );

        // Typical size button
        $( '#lcc-rooms-list' ).on( 'click', '.lcc-typical-btn', function () {
            var $row     = $( this ).closest( '.lcc-room-row' );
            var type     = $row.find( '.lcc-room-type' ).val();
            var roomData = ROOMS[ type ];
            if ( roomData && roomData.typical_sqft ) {
                $row.find( '.lcc-room-area' ).val( roomData.typical_sqft ).trigger( 'input' );
            }
        } );
    }

    function updateTotalArea() {
        var total = 0;
        $( '#lcc-rooms-list .lcc-room-area' ).each( function () {
            total += parseFloat( $( this ).val() ) || 0;
        } );
        $( '#lcc-total-area' ).text( total );
        return total;
    }

    /* ============================================================
       PRESET BUTTONS
    ============================================================ */
    function bindPresets() {
        $calc.on( 'click', '.lcc-preset-btn', function () {
            var rooms;
            try {
                rooms = JSON.parse( $( this ).attr( 'data-preset' ) );
            } catch (e) { return; }

            // Clear existing rows
            var $list = $( '#lcc-rooms-list' );
            $list.empty();
            roomIndex = 0;

            $.each( rooms, function ( i, room ) {
                addRoomRow( room.type, room.area );
            } );

            updateTotalArea();
            scheduleCalc();
        } );
    }

    /* ============================================================
       ADD-ON TOGGLING
    ============================================================ */
    function bindAddonCards() {
        $calc.on( 'click', '.lcc-addon-card', function () {
            $( this ).toggleClass( 'active' );
            $( this ).find( '.lcc-addon-check' ).prop( 'checked', $( this ).hasClass( 'active' ) );
            scheduleCalc();
        } );
    }

    /* ============================================================
       DEBOUNCED CALCULATION
    ============================================================ */
    function scheduleCalc() {
        clearTimeout( calcTimer );
        $calc.addClass( 'lcc-calculating' );
        calcTimer = setTimeout( recalculate, 420 );
    }

    function recalculate() {
        var postData = buildPostData();

        // Quick client-side check
        var hasArea = false;
        $.each( postData.rooms, function ( i, r ) {
            if ( r.area > 0 ) { hasArea = true; return false; }
        } );
        if ( ! hasArea ) {
            resetDisplay();
            $calc.removeClass( 'lcc-calculating' );
            return;
        }

        $.post( AJAX_URL, {
            action  : 'lcc_calculate',
            nonce   : NONCE,
            tier    : postData.tier,
            finish  : postData.finish,
            complexity: postData.complexity,
            rooms   : postData.rooms,
            addons  : postData.addons,
        }, function ( res ) {
            $calc.removeClass( 'lcc-calculating' );
            if ( res.success ) {
                lastResult = res.data;
                renderResult( res.data );
            }
        } ).fail( function () {
            $calc.removeClass( 'lcc-calculating' );
        } );
    }

    function buildPostData() {
        var tier       = $calc.find( 'input[name="lcc_tier"]:checked' ).val() || 'full_design';
        var finish     = $calc.find( 'input[name="lcc_finish"]:checked' ).val() || 'standard';
        var complexity = $calc.find( 'input[name="lcc_complexity"]:checked' ).val() || 'simple';

        var rooms = [];
        $( '#lcc-rooms-list .lcc-room-row' ).each( function () {
            var type = $( this ).find( '.lcc-room-type' ).val();
            var area = parseFloat( $( this ).find( '.lcc-room-area' ).val() ) || 0;
            rooms.push( { type: type, area: area } );
        } );

        var addons = [];
        $calc.find( '.lcc-addon-check:checked' ).each( function () {
            addons.push( $( this ).val() );
        } );

        return { tier: tier, finish: finish, complexity: complexity, rooms: rooms, addons: addons };
    }

    /* ============================================================
       RENDER RESULT
    ============================================================ */
    function renderResult( data ) {
        // Price range
        animateValue( '#lcc-price-low',  data.low_total,   data.symbol );
        animateValue( '#lcc-price-high', data.high_total,  data.symbol );

        // Metrics
        $( '#lcc-avg-rate' ).text( data.symbol + formatNum( data.avg_rate ) );
        $( '#lcc-total-sqft' ).text( formatNum( data.total_area ) );
        $( '#lcc-finish-label' ).text( data.finish.label );
        $( '#lcc-tier-label' ).text( data.tier.label );

        // Summary rows
        $( '#lcc-subtotal' ).text( data.symbol + formatNum( data.subtotal ) );
        $( '#lcc-grand-total' ).text( data.symbol + formatNum( data.grand_total ) );
        if ( $( '#lcc-vat' ).length ) {
            $( '#lcc-vat' ).text( data.symbol + formatNum( data.vat_amount ) );
        }

        // Add-ons
        if ( data.addons_total > 0 ) {
            $( '#lcc-addons-row' ).prop( 'hidden', false );
            $( '#lcc-addons-total' ).text( data.symbol + formatNum( data.addons_total ) );
        } else {
            $( '#lcc-addons-row' ).prop( 'hidden', true );
        }

        // Add-ons summary
        renderAddonsSummary( data );

        // Room breakdown
        renderBreakdown( data );

        // Below minimum warning
        if ( data.below_minimum && data.min_budget_msg ) {
            $( '#lcc-min-warning-msg' ).text( data.min_budget_msg );
            $( '#lcc-min-warning' ).prop( 'hidden', false );
        } else {
            $( '#lcc-min-warning' ).prop( 'hidden', true );
        }

        // Per-room cost labels in room rows
        updateRoomCostLabels( data );
    }

    function renderBreakdown( data ) {
        var $tbody = $( '#lcc-breakdown-rows' );
        $tbody.empty();

        if ( ! data.rooms || ! data.rooms.length ) {
            $tbody.html( '<tr class="lcc-breakdown-empty"><td colspan="4">Add rooms above to see breakdown</td></tr>' );
            return;
        }

        $.each( data.rooms, function ( i, room ) {
            if ( ! room.area ) return;
            $tbody.append(
                '<tr>' +
                '<td><span class="lcc-room-icon-cell">' + escHtml( room.icon ) + '</span> ' + escHtml( room.label ) + '</td>' +
                '<td>' + formatNum( room.area ) + '</td>' +
                '<td>' + data.symbol + formatNum( room.rate ) + '</td>' +
                '<td>' + data.symbol + formatNum( room.total ) + '</td>' +
                '</tr>'
            );
        } );

        // Complexity surcharge row
        if ( data.complexity.surcharge > 0 ) {
            var surcharge = data.rooms.reduce( function( s, r ) { return s + r.complexity; }, 0 );
            $tbody.append(
                '<tr style="opacity:0.65;">' +
                '<td colspan="3">Complexity surcharge (+' + data.complexity.surcharge + '%)</td>' +
                '<td>' + data.symbol + formatNum( surcharge ) + '</td>' +
                '</tr>'
            );
        }
    }

    function renderAddonsSummary( data ) {
        var $wrap = $( '#lcc-addons-summary' );
        if ( ! data.addons || ! data.addons.length ) {
            $wrap.prop( 'hidden', true ).empty();
            return;
        }
        $wrap.empty().prop( 'hidden', false );
        $.each( data.addons, function ( i, addon ) {
            $wrap.append(
                '<div class="lcc-addon-summary-row">' +
                '<span>' + escHtml( addon.icon + ' ' + addon.label ) + '</span>' +
                '<span>+' + data.symbol + formatNum( addon.amount ) + '</span>' +
                '</div>'
            );
        } );
    }

    function updateRoomCostLabels( data ) {
        $( '#lcc-rooms-list .lcc-room-row' ).each( function ( i ) {
            var roomData = data.rooms ? data.rooms[ i ] : null;
            var $label = $( this ).find( '.lcc-room-cost-val' );
            if ( roomData && roomData.total ) {
                $label.text( data.symbol + formatNum( roomData.total ) );
            } else {
                $label.text( '—' );
            }
        } );
    }

    function resetDisplay() {
        $( '#lcc-price-low, #lcc-price-high' ).text( SYMBOL + '0' );
        $( '#lcc-avg-rate' ).text( SYMBOL + '0' );
        $( '#lcc-total-sqft' ).text( '0' );
        $( '#lcc-subtotal, #lcc-grand-total' ).text( SYMBOL + '0' );
        $( '#lcc-breakdown-rows' ).html( '<tr class="lcc-breakdown-empty"><td colspan="4">Add rooms above to see breakdown</td></tr>' );
    }

    /* ============================================================
       BREAKDOWN TOGGLE
    ============================================================ */
    function bindBreakdownToggle() {
        $( '#lcc-breakdown-toggle' ).on( 'click', function () {
            var $btn  = $( this );
            var $body = $( '#lcc-breakdown-body' );
            var open  = $btn.attr( 'aria-expanded' ) === 'true';
            $btn.attr( 'aria-expanded', ! open ? 'true' : 'false' );
            $body.toggle( ! open );
        } );
    }

    /* ============================================================
       LEAD CAPTURE
    ============================================================ */
    function bindLeadCapture() {
        $( '#lcc-save-lead' ).on( 'click', function () {
            var $btn   = $( this );
            var name   = $( '#lcc-lead-name' ).val().trim();
            var email  = $( '#lcc-lead-email' ).val().trim();
            var phone  = $( '#lcc-lead-phone' ).val().trim();
            var $msg   = $( '#lcc-lead-msg' );

            if ( ! name ) { showLeadMsg( 'Please enter your name.', 'error' ); return; }
            if ( ! email || ! isEmail( email ) ) { showLeadMsg( 'Please enter a valid email address.', 'error' ); return; }
            if ( ! lastResult ) { showLeadMsg( 'Please configure your rooms to generate an estimate first.', 'error' ); return; }

            $btn.prop( 'disabled', true ).text( 'Sending…' );

            $.post( AJAX_URL, {
                action         : 'lcc_save_lead',
                nonce          : NONCE,
                name           : name,
                email          : email,
                phone          : phone,
                tier           : lastResult.tier.label,
                finish         : lastResult.finish.label,
                total_area     : lastResult.total_area,
                estimate_total : lastResult.symbol + formatNum( lastResult.grand_total ),
            }, function ( res ) {
                $btn.prop( 'disabled', false ).text( 'Get a Detailed Quote →' );
                if ( res.success ) {
                    showLeadMsg( res.data.message || 'Thank you! We\'ll be in touch shortly.', 'success' );
                    $( '#lcc-lead-name, #lcc-lead-email, #lcc-lead-phone' ).val( '' );
                } else {
                    showLeadMsg( res.data || 'Something went wrong. Please try again.', 'error' );
                }
            } ).fail( function () {
                $btn.prop( 'disabled', false ).text( 'Get a Detailed Quote →' );
                showLeadMsg( 'Connection error. Please try again.', 'error' );
            } );
        } );

        // Email button (non-lead-capture mode)
        $( document ).on( 'click', '#lcc-email-btn', function () {
            var email = prompt( 'Enter your email address to receive this estimate:' );
            if ( ! email || ! isEmail( email ) ) return;
            if ( ! lastResult ) { alert( 'Please configure your rooms first.' ); return; }

            var summary = buildTextSummary( lastResult );
            $.post( AJAX_URL, {
                action  : 'lcc_email_estimate',
                nonce   : NONCE,
                email   : email,
                summary : summary,
            }, function ( res ) {
                alert( res.data ? res.data.message || res.data : 'Sent!' );
            } );
        } );
    }

    function showLeadMsg( text, type ) {
        var $msg = $( '#lcc-lead-msg' );
        $msg.text( text )
            .removeClass( 'lcc-lead-msg--success lcc-lead-msg--error' )
            .addClass( type === 'error' ? 'lcc-lead-msg--error' : 'lcc-lead-msg--success' )
            .prop( 'hidden', false );
    }

    function buildTextSummary( data ) {
        var lines = [];
        lines.push( 'Service: ' + data.tier.label );
        lines.push( 'Finish Level: ' + data.finish.label );
        lines.push( 'Total Area: ' + formatNum( data.total_area ) + ' ' + UNIT );
        lines.push( '' );
        $.each( data.rooms, function ( i, r ) {
            if ( r.area ) lines.push( r.label + ': ' + formatNum(r.area) + ' ' + UNIT + ' = ' + data.symbol + formatNum(r.total) );
        } );
        lines.push( '' );
        lines.push( 'Subtotal: ' + data.symbol + formatNum( data.subtotal ) );
        if ( data.addons_total ) lines.push( 'Add-ons: ' + data.symbol + formatNum( data.addons_total ) );
        if ( data.vat_amount )   lines.push( 'VAT: ' + data.symbol + formatNum( data.vat_amount ) );
        lines.push( 'Estimated Total: ' + data.symbol + formatNum( data.grand_total ) );
        lines.push( '' );
        lines.push( 'Range: ' + data.symbol + formatNum( data.low_total ) + ' – ' + data.symbol + formatNum( data.high_total ) );
        return lines.join( '\n' );
    }

    /* ============================================================
       ANIMATED NUMBER COUNTER
    ============================================================ */
    function animateValue( selector, targetRaw, prefix ) {
        var $el     = $( selector );
        var current = parseRaw( $el.text(), prefix );
        var target  = targetRaw;
        var steps   = 18;
        var step    = 0;
        var diff    = target - current;
        var timer   = setInterval( function () {
            step++;
            var progress = step / steps;
            var ease     = 1 - Math.pow( 1 - progress, 3 ); // ease-out cubic
            var val      = Math.round( current + diff * ease );
            $el.text( formatWithPrefix( val, prefix ) );
            if ( step >= steps ) {
                clearInterval( timer );
                $el.text( formatWithPrefix( target, prefix ) );
            }
        }, 20 );
    }

    function formatWithPrefix( num, prefix ) {
        if ( prefix === '৳' && num >= 100000 ) {
            return '৳' + ( num / 100000 ).toFixed( 2 ) + ' L';
        }
        return prefix + formatNum( num );
    }

    function parseRaw( text, prefix ) {
        var clean = text.replace( prefix, '' ).replace( /,/g, '' ).replace( ' L', '' );
        var n = parseFloat( clean );
        if ( text.indexOf( ' L' ) !== -1 ) n = n * 100000;
        return isNaN(n) ? 0 : n;
    }

    /* ============================================================
       UTILITIES
    ============================================================ */
    function formatNum( n ) {
        var num = Math.round( parseFloat( n ) || 0 );
        return num.toLocaleString( 'en-IN' );
    }

    function isEmail( str ) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( str );
    }

    function escHtml( str ) {
        return String( str )
            .replace( /&/g, '&amp;' )
            .replace( /</g, '&lt;' )
            .replace( />/g, '&gt;' )
            .replace( /"/g, '&quot;' );
    }

    /* ============================================================
       BOOT
    ============================================================ */
    $( init );

} )( jQuery );
