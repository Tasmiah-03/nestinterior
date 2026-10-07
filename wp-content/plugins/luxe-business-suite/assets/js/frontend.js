/**
 * Luxe Business Suite — Frontend JS v1.0.0
 * Modules: Booking slots, FAQ accordion, AJAX search,
 *          Newsletter subscribe, TOC toggle, Scroll reveal
 */
( function ( $ ) {
    'use strict';

    var AJAX_URL = lbsData.ajaxUrl;
    var NONCE    = lbsData.nonce;

    /* ============================================================
       MODULE 2 — BOOKING: Dynamic slot loading
    ============================================================ */
    function initBooking() {
        var $form      = $( '.lbs-booking-form' );
        if ( ! $form.length ) return;

        var $dateInput  = $( '#lbs_booking_date' );
        var $container  = $( '#lbs-slots-container' );
        var $slotInput  = $( '#lbs_slot_id' );
        var $vDateInput = $( '#lbs_v_date' );
        var $vTimeInput = $( '#lbs_v_time' );

        function loadSlots() {
            var date    = $dateInput.val();
            var service = $form.find( 'input[name="lbs_service"]:checked' ).val();

            if ( ! date || ! service ) {
                $container.html( '<p class="lbs-hint">Select a service and date above to see available times.</p>' );
                return;
            }

            $container.html( '<p class="lbs-slots-loading"><span class="lbs-loading-dot"></span> Checking availability…</p>' );

            $.ajax( {
                url     : AJAX_URL,
                type    : 'POST',
                timeout : 10000,
                data    : {
                    action  : 'lbs_get_slots',
                    nonce   : NONCE,
                    date    : date,
                    service : service,
                },
                success : function ( res ) {
                    if ( ! res.success || ! res.data || ! res.data.length ) {
                        $container.html(
                            '<p class="lbs-slots-empty">'
                            + '⚠ No available times on this date. '
                            + '<a href="#" class="lbs-try-another-date">Try another date</a>'
                            + '</p>'
                        );
                        $slotInput.val( '' );
                        return;
                    }

                    var html = '<div class="lbs-slots-grid">';
                    $.each( res.data, function ( i, slot ) {
                        var raw24  = slot.time || '--:--';
                        var time   = lbs_format_time( raw24 );
                        var spots  = slot.spots > 1 ? ' <span class="lbs-slot-spots">(' + slot.spots + ' spots)</span>' : '';
                        var dur    = slot.duration ? ' &middot; ' + slot.duration + ' min' : '';
                        var vDate  = slot.virtual ? slot.v_date : '';
                        var vTime  = slot.virtual ? slot.v_time : '';
                        html += '<button type="button" class="lbs-slot-btn"'
                            + ' data-slot-id="'  + slot.id   + '"'
                            + ' data-virtual="'  + ( slot.virtual ? '1' : '0' ) + '"'
                            + ' data-v-date="'   + vDate + '"'
                            + ' data-v-time="'   + vTime + '"'
                            + '>' + time + dur + spots + '</button>';
                    } );
                    html += '</div>';
                    $container.html( html );
                },
                error : function () {
                    $container.html( '<p class="lbs-slots-empty">Could not load time slots. Please refresh the page and try again.</p>' );
                    $slotInput.val( '' );
                }
            } );
        }

        $form.on( 'change', 'input[name="lbs_service"]', loadSlots );
        $dateInput.on( 'change', loadSlots );

        // When a slot button is clicked, mark it selected and fill hidden fields
        $container.on( 'click', '.lbs-slot-btn', function () {
            $container.find( '.lbs-slot-btn' ).removeClass( 'selected' );
            $( this ).addClass( 'selected' );
            $slotInput.val( $( this ).data( 'slot-id' ) );
            // For virtual slots, populate hidden date/time fields
            if ( $( this ).data( 'virtual' ) == '1' ) {
                $vDateInput.val( $( this ).data( 'v-date' ) );
                $vTimeInput.val( $( this ).data( 'v-time' ) );
            } else {
                $vDateInput.val( '' );
                $vTimeInput.val( '' );
            }
            // Clear slot validation error when a slot is chosen
            $( '#lbs-error-slot' ).remove();
        } );

        // Try another date link
        $container.on( 'click', '.lbs-try-another-date', function ( e ) {
            e.preventDefault();
            $dateInput.focus();
        } );

        // ── Client-side validation before submit ──────────────────
        $form.on( 'submit', function ( e ) {
            // Remove previous inline errors
            $form.find( '.lbs-inline-error' ).remove();
            $form.find( '.lbs-input-error' ).removeClass( 'lbs-input-error' );
            var valid = true;

            function showError( $field, msg, id ) {
                if ( id && $( '#' + id ).length ) return; // avoid duplicates
                var $err = $( '<p class="lbs-inline-error" id="' + ( id || '' ) + '">' + msg + '</p>' );
                $field.after( $err );
                $field.addClass( 'lbs-input-error' );
                valid = false;
            }

            // 1. Service
            if ( ! $form.find( 'input[name="lbs_service"]:checked' ).val() ) {
                showError( $form.find( '.lbs-service-options' ), '⚠ Please select a service.', 'lbs-error-service' );
            }

            // 2. Date
            if ( ! $dateInput.val() ) {
                showError( $dateInput, '⚠ Please choose a date.', 'lbs-error-date' );
            }

            // 3. Time slot
            if ( ! $container.find( '.lbs-slot-btn.selected' ).length ) {
                var $slotsWrap = $container.find( '.lbs-slots-grid' );
                var $target    = $slotsWrap.length ? $slotsWrap : $container;
                var $err = $( '<p class="lbs-inline-error" id="lbs-error-slot">⚠ Please select an available time slot.</p>' );
                $target.append( $err );
                valid = false;
            }

            // 4. Name
            var $nameInput = $form.find( '[name="lbs_name"]' );
            if ( ! $nameInput.val().trim() ) {
                showError( $nameInput, '⚠ Please enter your full name.', 'lbs-error-name' );
            }

            // 5. Email
            var $emailInput = $form.find( '[name="lbs_email"]' );
            var emailVal    = $emailInput.val().trim();
            var emailOk     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( emailVal );
            if ( ! emailVal || ! emailOk ) {
                showError( $emailInput, '⚠ Please enter a valid email address.', 'lbs-error-email' );
            }

            if ( ! valid ) {
                e.preventDefault();
                // Scroll to first error
                var $first = $form.find( '.lbs-inline-error' ).first();
                if ( $first.length ) {
                    $( 'html, body' ).animate( { scrollTop: $first.offset().top - 120 }, 400 );
                }
            }
        } );
    }

    /* Helper: convert 24-hr "HH:MM" to 12-hr "H:MM AM/PM" */
    function lbs_format_time( t ) {
        if ( ! t || t === '--:--' ) return t;
        var parts = t.split(':');
        var h = parseInt( parts[0], 10 );
        var m = parts[1];
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return h + ':' + m + ' ' + ampm;
    }

    /* ============================================================
       MODULE 4 — PRICING: FAQ Accordion
    ============================================================ */
    function initFaqAccordion() {
        $( document ).on( 'click', '.lbs-faq-question', function () {
            var $btn    = $( this );
            var $item   = $btn.closest( '.lbs-faq-item' );
            var $answer = $item.find( '.lbs-faq-answer' );
            var isOpen  = $btn.attr( 'aria-expanded' ) === 'true';

            if ( isOpen ) {
                $btn.attr( 'aria-expanded', 'false' );
                $answer.attr( 'hidden', true );
            } else {
                // Close others
                $btn.closest( '.lbs-faq-accordion' ).find( '.lbs-faq-question' ).not( $btn )
                    .attr( 'aria-expanded', 'false' )
                    .closest( '.lbs-faq-item' ).find( '.lbs-faq-answer' ).attr( 'hidden', true );
                $btn.attr( 'aria-expanded', 'true' );
                $answer.removeAttr( 'hidden' );
            }
        } );
    }

    /* ============================================================
       MODULE 5 — JOURNAL: Live Search
    ============================================================ */
    function initJournalSearch() {
        var $input   = $( '#lbs-search-input' );
        var $results = $( '#lbs-search-results' );
        if ( ! $input.length ) return;

        var timer;

        $input.on( 'input', function () {
            clearTimeout( timer );
            var term = $( this ).val().trim();

            if ( term.length < 2 ) {
                $results.attr( 'hidden', true ).empty();
                return;
            }

            timer = setTimeout( function () {
                $.post( AJAX_URL, {
                    action : 'lbs_journal_search',
                    nonce  : NONCE,
                    term   : term,
                }, function ( res ) {
                    $results.empty();
                    if ( ! res.success || ! res.data.length ) {
                        $results.removeAttr( 'hidden' ).html( '<div class="lbs-search-empty">' + lbsData.strings.noResults + '</div>' );
                        return;
                    }
                    var html = '';
                    $.each( res.data, function ( i, post ) {
                        var thumb = post.thumb
                            ? '<img src="' + post.thumb + '" alt="" class="lbs-search-result__thumb" loading="lazy">'
                            : '';
                        html += '<a href="' + post.url + '" class="lbs-search-result">'
                            + thumb
                            + '<div class="lbs-search-result__body">'
                            + '<span class="lbs-search-result__title">' + post.title + '</span>'
                            + '<span class="lbs-search-result__excerpt">' + post.excerpt + '</span>'
                            + '<span class="lbs-search-result__date">' + post.date + '</span>'
                            + '</div></a>';
                    } );
                    $results.removeAttr( 'hidden' ).html( html );
                } );
            }, 320 );
        } );

        // Close on outside click
        $( document ).on( 'click', function ( e ) {
            if ( ! $( e.target ).closest( '.lbs-journal-search' ).length ) {
                $results.attr( 'hidden', true );
            }
        } );

        // Keyboard navigation
        $input.on( 'keydown', function ( e ) {
            var $links = $results.find( '.lbs-search-result' );
            if ( ! $links.length ) return;
            if ( e.key === 'ArrowDown' ) { e.preventDefault(); $links.first().trigger( 'focus' ); }
            if ( e.key === 'Escape' )    { $results.attr( 'hidden', true ); }
        } );
    }

    /* ============================================================
       MODULE 5 — JOURNAL: Newsletter Subscribe (AJAX)
    ============================================================ */
    function initNewsletter() {
        $( document ).on( 'submit', '.lbs-newsletter-form', function ( e ) {
            e.preventDefault();
            var $form  = $( this );
            var $email = $form.find( '[name="lbs_nl_email"]' );
            var $msg   = $form.closest( '.lbs-nl-cta' ).find( '.lbs-nl-msg' );
            var $btn   = $form.find( '.lbs-nl-btn' );

            if ( ! $email.val() ) return;

            $btn.prop( 'disabled', true ).text( '…' );

            $.post( AJAX_URL, {
                action : 'lbs_newsletter_subscribe',
                nonce  : NONCE,
                email  : $email.val(),
            }, function ( res ) {
                $btn.prop( 'disabled', false ).text( 'Subscribe' );
                $msg.removeAttr( 'hidden' ).text( res.data || '' );
                $msg.css( 'color', res.success ? '#10b981' : '#ef4444' );
                if ( res.success ) { $email.val( '' ); }
            } );
        } );
    }

    /* ============================================================
       MODULE 5 — JOURNAL: Table of Contents toggle
    ============================================================ */
    function initTocToggle() {
        $( document ).on( 'click', '.lbs-toc__toggle', function () {
            var $toc  = $( this ).closest( '.lbs-toc' );
            var $list = $toc.find( '.lbs-toc__list' );
            var open  = $list.is( ':visible' );
            $list.toggle( ! open );
            $( this ).text( open ? '+' : '–' );
        } );

        // Highlight active TOC item on scroll
        if ( $( '.lbs-toc' ).length && 'IntersectionObserver' in window ) {
            var headings = document.querySelectorAll( '[id^="lbs-heading-"]' );
            if ( ! headings.length ) return;
            var obs = new IntersectionObserver( function ( entries ) {
                entries.forEach( function ( entry ) {
                    if ( entry.isIntersecting ) {
                        var id = entry.target.id;
                        $( '.lbs-toc__list a' ).css( 'color', '' );
                        $( '.lbs-toc__list a[href="#' + id + '"]' ).css( 'color', 'var(--color-accent)' );
                    }
                } );
            }, { rootMargin: '-30% 0px -60% 0px' } );
            headings.forEach( function ( h ) { obs.observe( h ); } );
        }
    }

    /* ============================================================
       MODULE 5 — JOURNAL: Lead status quick update
    ============================================================ */
    function initLeadStatus() {
        $( document ).on( 'change', '.lbs-status-select', function () {
            var $select  = $( this );
            var lead_id  = $select.data( 'lead-id' );
            var status   = $select.val();
            $.post( AJAX_URL, {
                action  : 'lbs_update_lead_status',
                nonce   : NONCE,
                lead_id : lead_id,
                status  : status,
            } );
        } );
    }

    /* ============================================================
       SCROLL REVEAL
    ============================================================ */
    function initReveal() {
        if ( ! ( 'IntersectionObserver' in window ) ) return;
        var obs = new IntersectionObserver( function ( entries ) {
            entries.forEach( function ( e ) {
                if ( e.isIntersecting ) {
                    e.target.classList.add( 'revealed' );
                    obs.unobserve( e.target );
                }
            } );
        }, { threshold: 0.1 } );
        document.querySelectorAll( '[data-lbs-reveal]' ).forEach( function ( el ) {
            obs.observe( el );
        } );
    }

    /* ============================================================
       ADMIN: Image picker (used by team/press/moodboard)
    ============================================================ */
    function initAdminImagePicker() {
        if ( typeof wp === 'undefined' || ! wp.media ) return;

        $( document ).on( 'click', '.lbs-pick-image, .lbs-pick-file', function ( e ) {
            e.preventDefault();
            var targetId = $( this ).data( 'target' );
            var $input   = $( '#' + targetId );
            var isFile   = $( this ).hasClass( 'lbs-pick-file' );

            var frame = wp.media( {
                title   : isFile ? 'Select File' : 'Select Image',
                button  : { text: isFile ? 'Use This File' : 'Use This Image' },
                multiple: false,
                library : isFile ? {} : { type: 'image' },
            } );

            frame.on( 'select', function () {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                $input.val( attachment.id );

                if ( ! isFile ) {
                    var thumbUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                    $input.siblings( '.lbs-img-preview, .lbs-doc-preview' ).html( '<img src="' + thumbUrl + '" style="max-width:100%;max-height:100px;object-fit:contain;">' );
                } else {
                    var fileName = attachment.filename || attachment.url.split('/').pop();
                    $input.siblings( '.lbs-doc-preview' ).html( '📎 ' + fileName );
                }
            } );

            frame.open();
        } );

        // Mood board multi-select gallery
        $( document ).on( 'click', '.lbs-pick-gallery', function ( e ) {
            e.preventDefault();
            var $list  = $( '#lbs-mb-gallery-list' );
            var $input = $( '#lbs_mb_images' );

            var frame = wp.media( {
                title   : 'Select Mood Board Images',
                button  : { text: 'Add to Mood Board' },
                multiple: 'add',
                library : { type: 'image' },
            } );

            frame.on( 'select', function () {
                var existing = $list.find( 'li[data-id]' ).map( function () { return parseInt( $( this ).data('id'), 10 ); } ).get();
                frame.state().get('selection').each( function ( attachment ) {
                    var id = attachment.id;
                    if ( existing.indexOf(id) !== -1 ) return;
                    var thumbUrl = attachment.attributes.sizes && attachment.attributes.sizes.thumbnail ? attachment.attributes.sizes.thumbnail.url : attachment.attributes.url;
                    var li = $( '<li data-id="' + id + '" style="position:relative;"></li>' );
                    li.html( '<img src="' + thumbUrl + '" style="width:80px;height:80px;object-fit:cover;display:block;"><button type="button" style="position:absolute;top:0;right:0;background:rgba(0,0,0,.6);color:#fff;border:none;cursor:pointer;width:20px;height:20px;font-size:12px;line-height:20px;text-align:center;padding:0;" onclick="this.parentElement.remove();lbsUpdateMbIds();">✕</button>' );
                    $list.append( li );
                    existing.push( id );
                } );
                // Update hidden input
                var ids = $list.find( 'li[data-id]' ).map( function () { return $( this ).data('id'); } ).get();
                $input.val( ids.join(',') );
            } );

            frame.open();
        } );
    }

    /* ============================================================
       BOOT
    ============================================================ */
    $( function () {
        initBooking();
        initFaqAccordion();
        initJournalSearch();
        initNewsletter();
        initTocToggle();
        initLeadStatus();
        initReveal();
        initAdminImagePicker();
    } );

} )( jQuery );
