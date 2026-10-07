<?php
/**
 * Shortcode [lbs_cost_calculator] — inc/shortcode.php
 * Renders the full multi-step calculator UI
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'lbs_cost_calculator', 'lcc_render_calculator' );

function lcc_render_calculator( $atts ) {
    $a = shortcode_atts( array(
        'title'          => __( 'Estimate Your Project Cost', 'luxe-cost-calculator' ),
        'subtitle'       => __( 'Get an instant indicative cost for your interior design project. Adjust the details and see the estimate update in real time.', 'luxe-cost-calculator' ),
        'default_tier'   => 'full_design',
        'default_finish' => 'standard',
        'show_addons'    => 'true',
        'show_header'    => 'true',
    ), $atts );

    $rates    = lcc_get_all_rates();
    $settings = get_option( 'lcc_settings', lcc_default_settings() );
    $sym      = lcc_currency_symbol();
    $unit     = $settings['area_unit'] ?? 'sqft';
    $unit_label = $unit === 'sqm' ? 'm²' : 'sqft';
    $show_addons = $a['show_addons'] === 'true' && ! empty( $settings['show_addons'] );
    $lead_capture = ! empty( $settings['lead_capture'] );

    ob_start();
    ?>
    <div class="lcc-calculator" id="lcc-main" data-unit="<?php echo esc_attr($unit); ?>">

        <!-- HEADER -->
        <?php if ( $a['show_header'] === 'true' ) : ?>
        <div class="lcc-header">
            <span class="lcc-eyebrow"><?php _e( 'Project Cost Estimator', 'luxe-cost-calculator' ); ?></span>
            <h2 class="lcc-title"><?php echo esc_html( $a['title'] ); ?></h2>
            <p class="lcc-subtitle"><?php echo esc_html( $a['subtitle'] ); ?></p>
        </div>
        <?php endif; ?>

        <div class="lcc-body">

            <!-- ══ LEFT PANEL: INPUTS ══════════════════════════ -->
            <div class="lcc-panel lcc-panel--inputs">

                <!-- STEP 1: SERVICE TIER -->
                <div class="lcc-step" id="lcc-step-tier">
                    <div class="lcc-step__header">
                        <span class="lcc-step__num">01</span>
                        <div>
                            <h4 class="lcc-step__title"><?php _e( 'Service Type', 'luxe-cost-calculator' ); ?></h4>
                            <p class="lcc-step__desc"><?php _e( 'What level of involvement do you need?', 'luxe-cost-calculator' ); ?></p>
                        </div>
                    </div>
                    <div class="lcc-tier-grid">
                        <?php foreach ( $rates['tiers'] as $key => $tier ) : ?>
                        <label class="lcc-tier-card <?php echo $key === $a['default_tier'] ? 'active' : ''; ?>" data-tier="<?php echo esc_attr($key); ?>">
                            <input type="radio" name="lcc_tier" value="<?php echo esc_attr($key); ?>" <?php checked( $key, $a['default_tier'] ); ?> class="lcc-sr-only">
                            <span class="lcc-tier-card__icon" style="color:<?php echo esc_attr($tier['color']); ?>;"><?php echo esc_html($tier['icon']); ?></span>
                            <strong class="lcc-tier-card__name"><?php echo esc_html($tier['label']); ?></strong>
                            <span class="lcc-tier-card__desc"><?php echo esc_html($tier['description']); ?></span>
                            <span class="lcc-tier-card__rate"><?php echo $sym . number_format($tier['base_rate']); ?> / <?php echo esc_html($unit_label); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- STEP 2: FINISH LEVEL -->
                <div class="lcc-step" id="lcc-step-finish">
                    <div class="lcc-step__header">
                        <span class="lcc-step__num">02</span>
                        <div>
                            <h4 class="lcc-step__title"><?php _e( 'Finish Level', 'luxe-cost-calculator' ); ?></h4>
                            <p class="lcc-step__desc"><?php _e( 'What quality of materials are you after?', 'luxe-cost-calculator' ); ?></p>
                        </div>
                    </div>
                    <div class="lcc-finish-grid">
                        <?php foreach ( $rates['finishes'] as $key => $finish ) : ?>
                        <label class="lcc-finish-card <?php echo $key === $a['default_finish'] ? 'active' : ''; ?>" data-finish="<?php echo esc_attr($key); ?>">
                            <input type="radio" name="lcc_finish" value="<?php echo esc_attr($key); ?>" <?php checked( $key, $a['default_finish'] ); ?> class="lcc-sr-only">
                            <span class="lcc-finish-card__icon"><?php echo esc_html($finish['icon']); ?></span>
                            <strong class="lcc-finish-card__name"><?php echo esc_html($finish['label']); ?></strong>
                            <span class="lcc-finish-card__desc"><?php echo esc_html($finish['desc']); ?></span>
                            <span class="lcc-finish-card__mult">×<?php echo esc_html($finish['multiplier']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- STEP 3: COMPLEXITY -->
                <div class="lcc-step" id="lcc-step-complexity">
                    <div class="lcc-step__header">
                        <span class="lcc-step__num">03</span>
                        <div>
                            <h4 class="lcc-step__title"><?php _e( 'Space Complexity', 'luxe-cost-calculator' ); ?></h4>
                            <p class="lcc-step__desc"><?php _e( 'How complex is the space layout?', 'luxe-cost-calculator' ); ?></p>
                        </div>
                    </div>
                    <div class="lcc-complexity-grid">
                        <?php foreach ( $rates['complexity'] as $key => $comp ) : ?>
                        <label class="lcc-complexity-card <?php echo $key === 'simple' ? 'active' : ''; ?>">
                            <input type="radio" name="lcc_complexity" value="<?php echo esc_attr($key); ?>" <?php checked( $key, 'simple' ); ?> class="lcc-sr-only">
                            <strong><?php echo esc_html($comp['label']); ?></strong>
                            <?php if ( $comp['surcharge'] > 0 ) : ?>
                            <span class="lcc-complexity-card__surcharge">+<?php echo esc_html($comp['surcharge']); ?>%</span>
                            <?php else : ?>
                            <span class="lcc-complexity-card__surcharge lcc-surcharge--zero"><?php _e('No surcharge','luxe-cost-calculator'); ?></span>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- STEP 4: ROOMS -->
                <div class="lcc-step" id="lcc-step-rooms">
                    <div class="lcc-step__header">
                        <span class="lcc-step__num">04</span>
                        <div>
                            <h4 class="lcc-step__title"><?php _e( 'Room Areas', 'luxe-cost-calculator' ); ?></h4>
                            <p class="lcc-step__desc"><?php printf( __( 'Enter each room\'s area in %s. Use typical sizes or measure your actual space.', 'luxe-cost-calculator' ), $unit_label ); ?></p>
                        </div>
                    </div>

                    <div id="lcc-rooms-list">
                        <!-- Default first room -->
                        <?php lcc_render_room_row( 0, 'living_room', 250, $rates['rooms'], $unit_label ); ?>
                    </div>

                    <div class="lcc-rooms-actions">
                        <button type="button" id="lcc-add-room" class="lcc-btn-add">
                            + <?php _e( 'Add Another Room', 'luxe-cost-calculator' ); ?>
                        </button>
                        <span class="lcc-total-area-label">
                            <?php _e( 'Total:', 'luxe-cost-calculator' ); ?>
                            <strong id="lcc-total-area">0</strong> <?php echo esc_html($unit_label); ?>
                        </span>
                    </div>

                    <!-- Quick preset buttons -->
                    <div class="lcc-presets">
                        <span class="lcc-presets__label"><?php _e( 'Quick Presets:', 'luxe-cost-calculator' ); ?></span>
                        <?php
                        $presets = array(
                            array( 'label' => __( '2-bed Apt', 'luxe-cost-calculator' ),    'rooms' => array( array('type'=>'living_room','area'=>220), array('type'=>'master_bedroom','area'=>180), array('type'=>'bedroom','area'=>140), array('type'=>'kitchen','area'=>100), array('type'=>'bathroom','area'=>55) ) ),
                            array( 'label' => __( '3-bed Home', 'luxe-cost-calculator' ),    'rooms' => array( array('type'=>'living_room','area'=>320), array('type'=>'dining_room','area'=>160), array('type'=>'master_bedroom','area'=>220), array('type'=>'bedroom','area'=>150), array('type'=>'bedroom','area'=>140), array('type'=>'kitchen','area'=>130), array('type'=>'master_bath','area'=>80), array('type'=>'bathroom','area'=>60) ) ),
                            array( 'label' => __( 'Single Room', 'luxe-cost-calculator' ),   'rooms' => array( array('type'=>'living_room','area'=>300) ) ),
                            array( 'label' => __( 'Kitchen Only','luxe-cost-calculator' ),   'rooms' => array( array('type'=>'kitchen','area'=>120) ) ),
                        );
                        foreach ( $presets as $preset ) : ?>
                        <button type="button" class="lcc-preset-btn" data-preset='<?php echo esc_attr( json_encode( $preset['rooms'] ) ); ?>'>
                            <?php echo esc_html( $preset['label'] ); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- STEP 5: ADD-ONS -->
                <?php if ( $show_addons ) : ?>
                <div class="lcc-step" id="lcc-step-addons">
                    <div class="lcc-step__header">
                        <span class="lcc-step__num">05</span>
                        <div>
                            <h4 class="lcc-step__title"><?php _e( 'Optional Add-ons', 'luxe-cost-calculator' ); ?></h4>
                            <p class="lcc-step__desc"><?php _e( 'Select any additional services you\'re interested in.', 'luxe-cost-calculator' ); ?></p>
                        </div>
                    </div>
                    <div class="lcc-addons-grid">
                        <?php foreach ( $rates['addons'] as $key => $addon ) : ?>
                        <label class="lcc-addon-card" data-addon="<?php echo esc_attr($key); ?>">
                            <input type="checkbox" name="lcc_addons[]" value="<?php echo esc_attr($key); ?>" class="lcc-sr-only lcc-addon-check">
                            <span class="lcc-addon-card__icon"><?php echo esc_html($addon['icon']); ?></span>
                            <div class="lcc-addon-card__info">
                                <strong><?php echo esc_html($addon['label']); ?></strong>
                                <span class="lcc-addon-card__price">
                                    <?php if ( $addon['type'] === 'percent' ) : ?>
                                        +<?php echo esc_html($addon['value']); ?>% of total
                                    <?php else : ?>
                                        +<?php echo $sym . number_format( $addon['value'] ); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <span class="lcc-addon-card__check">✓</span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- .lcc-panel--inputs -->

            <!-- ══ RIGHT PANEL: RESULTS ════════════════════════ -->
            <div class="lcc-panel lcc-panel--results" id="lcc-results-panel">

                <!-- Live estimate display -->
                <div class="lcc-estimate-card" id="lcc-estimate-card">
                    <div class="lcc-estimate-card__header">
                        <span class="lcc-estimate-card__eyebrow"><?php _e( 'Your Estimate', 'luxe-cost-calculator' ); ?></span>
                        <span class="lcc-estimate-card__tier" id="lcc-tier-label">Full Interior Design</span>
                    </div>

                    <!-- Main price display -->
                    <div class="lcc-price-display">
                        <div class="lcc-price-range">
                            <span class="lcc-price-from" id="lcc-price-low">৳0</span>
                            <span class="lcc-price-dash">–</span>
                            <span class="lcc-price-to" id="lcc-price-high">৳0</span>
                        </div>
                        <p class="lcc-price-note"><?php _e( 'Estimated investment range', 'luxe-cost-calculator' ); ?></p>
                    </div>

                    <!-- Key metrics bar -->
                    <div class="lcc-metrics">
                        <div class="lcc-metric">
                            <span class="lcc-metric__val" id="lcc-avg-rate">৳0</span>
                            <span class="lcc-metric__label"><?php printf( __('avg / %s', 'luxe-cost-calculator'), $unit_label ); ?></span>
                        </div>
                        <div class="lcc-metric">
                            <span class="lcc-metric__val" id="lcc-total-sqft">0</span>
                            <span class="lcc-metric__label"><?php echo esc_html($unit_label); ?> total</span>
                        </div>
                        <div class="lcc-metric">
                            <span class="lcc-metric__val" id="lcc-finish-label">Standard</span>
                            <span class="lcc-metric__label"><?php _e('finish level','luxe-cost-calculator'); ?></span>
                        </div>
                    </div>

                    <!-- Below minimum warning -->
                    <div class="lcc-min-warning" id="lcc-min-warning" hidden>
                        <span>⚠</span>
                        <p id="lcc-min-warning-msg"></p>
                    </div>

                    <!-- ROOM BREAKDOWN TABLE -->
                    <div class="lcc-breakdown" id="lcc-breakdown" <?php echo empty($settings['show_breakdown']) ? 'hidden' : ''; ?>>
                        <div class="lcc-breakdown__header">
                            <strong><?php _e( 'Room Breakdown', 'luxe-cost-calculator' ); ?></strong>
                            <button type="button" class="lcc-breakdown__toggle" id="lcc-breakdown-toggle" aria-expanded="true">
                                <span>▾</span>
                            </button>
                        </div>
                        <div class="lcc-breakdown__body" id="lcc-breakdown-body">
                            <table class="lcc-breakdown-table">
                                <thead>
                                    <tr>
                                        <th><?php _e('Room','luxe-cost-calculator'); ?></th>
                                        <th><?php echo esc_html($unit_label); ?></th>
                                        <th><?php printf( __('Rate/%s','luxe-cost-calculator'), $unit_label ); ?></th>
                                        <th><?php _e('Cost','luxe-cost-calculator'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="lcc-breakdown-rows">
                                    <tr class="lcc-breakdown-empty">
                                        <td colspan="4"><?php _e( 'Add rooms above to see breakdown', 'luxe-cost-calculator' ); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- COST SUMMARY -->
                    <div class="lcc-summary" id="lcc-summary">
                        <div class="lcc-summary-row">
                            <span><?php _e('Design Fee Subtotal','luxe-cost-calculator'); ?></span>
                            <span id="lcc-subtotal">৳0</span>
                        </div>
                        <div class="lcc-summary-row" id="lcc-addons-row" hidden>
                            <span><?php _e('Add-ons','luxe-cost-calculator'); ?></span>
                            <span id="lcc-addons-total">৳0</span>
                        </div>
                        <?php if ( ! empty( $settings['show_vat'] ) ) : ?>
                        <div class="lcc-summary-row">
                            <span><?php printf( __('VAT (%s%%)', 'luxe-cost-calculator'), $settings['vat_rate'] ); ?></span>
                            <span id="lcc-vat">৳0</span>
                        </div>
                        <?php endif; ?>
                        <div class="lcc-summary-row lcc-summary-row--total">
                            <span><?php _e('Estimated Total','luxe-cost-calculator'); ?></span>
                            <span id="lcc-grand-total">৳0</span>
                        </div>
                    </div>

                    <!-- ADD-ONS SUMMARY -->
                    <div class="lcc-addons-summary" id="lcc-addons-summary" hidden></div>

                    <!-- LEAD CAPTURE / CTA -->
                    <?php if ( $lead_capture ) : ?>
                    <div class="lcc-lead-capture" id="lcc-lead-capture">
                        <p class="lcc-lead-capture__intro"><?php _e( 'Enter your details to save and email this estimate:', 'luxe-cost-calculator' ); ?></p>
                        <div class="lcc-lead-form">
                            <div class="lcc-lead-row">
                                <div class="lcc-lead-field">
                                    <label><?php _e('Your Name *','luxe-cost-calculator'); ?></label>
                                    <input type="text" id="lcc-lead-name" placeholder="Sarah Ahmed" class="lcc-input">
                                </div>
                                <div class="lcc-lead-field">
                                    <label><?php _e('Email Address *','luxe-cost-calculator'); ?></label>
                                    <input type="email" id="lcc-lead-email" placeholder="sarah@example.com" class="lcc-input">
                                </div>
                            </div>
                            <div class="lcc-lead-field">
                                <label><?php _e('Phone (optional)','luxe-cost-calculator'); ?></label>
                                <input type="tel" id="lcc-lead-phone" placeholder="+880 17 XXXX XXXX" class="lcc-input">
                            </div>
                            <button type="button" id="lcc-save-lead" class="lcc-btn-primary">
                                <?php echo esc_html( $settings['cta_text'] ?? __( 'Get a Detailed Quote', 'luxe-cost-calculator' ) ); ?> →
                            </button>
                            <p class="lcc-lead-msg" id="lcc-lead-msg" hidden></p>
                        </div>
                    </div>
                    <?php else : ?>
                    <div class="lcc-cta-wrap">
                        <a href="<?php echo esc_url( $settings['cta_url'] ?? '/contact' ); ?>" class="lcc-btn-primary">
                            <?php echo esc_html( $settings['cta_text'] ?? __( 'Get a Detailed Quote', 'luxe-cost-calculator' ) ); ?> →
                        </a>
                        <button type="button" id="lcc-email-btn" class="lcc-btn-secondary">
                            <?php _e( 'Email This Estimate', 'luxe-cost-calculator' ); ?>
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- DISCLAIMER -->
                    <?php if ( ! empty( $settings['disclaimer'] ) ) : ?>
                    <p class="lcc-disclaimer">
                        <span>ⓘ</span> <?php echo esc_html( $settings['disclaimer'] ); ?>
                    </p>
                    <?php endif; ?>
                </div><!-- .lcc-estimate-card -->

                <!-- TIER INCLUDES (changes with tier selection) -->
                <div class="lcc-tier-includes" id="lcc-tier-includes">
                    <h5><?php _e( 'Included in this service:', 'luxe-cost-calculator' ); ?></h5>
                    <ul id="lcc-includes-list">
                        <?php
                        $default_tier = $rates['tiers'][ $a['default_tier'] ] ?? $rates['tiers']['full_design'];
                        foreach ( $default_tier['includes'] as $item ) : ?>
                        <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div><!-- .lcc-panel--results -->

        </div><!-- .lcc-body -->
    </div><!-- .lcc-calculator -->

    <!-- Room row template (hidden, cloned by JS) -->
    <template id="lcc-room-template">
        <?php lcc_render_room_row( '{{INDEX}}', 'living_room', 0, $rates['rooms'], $unit_label, true ); ?>
    </template>

    <!-- Tier includes data (JSON for JS) -->
    <script>
    window.lccTierIncludes = <?php echo wp_json_encode( array_map( fn($t) => $t['includes'], $rates['tiers'] ) ); ?>;
    window.lccRooms = <?php echo wp_json_encode( $rates['rooms'] ); ?>;
    </script>
    <?php

    return ob_get_clean();
}

/* ── Room Row Helper ─────────────────────────────────────────── */
function lcc_render_room_row( $index, string $default_type, float $default_area, array $rooms, string $unit_label, bool $is_template = false ) {
    $extra = $is_template ? ' data-template="1"' : '';
    ?>
    <div class="lcc-room-row" data-index="<?php echo esc_attr($index); ?>"<?php echo $extra; ?>>
        <div class="lcc-room-row__type">
            <label class="lcc-room-label"><?php _e( 'Room Type', 'luxe-cost-calculator' ); ?></label>
            <select class="lcc-input lcc-room-type" name="lcc_rooms[<?php echo esc_attr($index); ?>][type]">
                <?php foreach ( $rooms as $key => $room ) : ?>
                <option value="<?php echo esc_attr($key); ?>" data-typical="<?php echo esc_attr($room['typical_sqft']); ?>" <?php echo ! $is_template ? selected( $key, $default_type, false ) : ''; ?>>
                    <?php echo esc_html( $room['icon'] . ' ' . $room['label'] ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="lcc-room-row__area">
            <label class="lcc-room-label">
                <?php printf( __( 'Area (%s)', 'luxe-cost-calculator' ), $unit_label ); ?>
            </label>
            <div class="lcc-area-input-wrap">
                <input type="number"
                       class="lcc-input lcc-room-area"
                       name="lcc_rooms[<?php echo esc_attr($index); ?>][area]"
                       value="<?php echo $is_template ? '' : esc_attr($default_area); ?>"
                       placeholder="<?php printf( esc_attr__( 'e.g. %s', 'luxe-cost-calculator' ), '250' ); ?>"
                       min="0"
                       step="1">
                <button type="button" class="lcc-typical-btn" title="<?php esc_attr_e( 'Use typical size', 'luxe-cost-calculator' ); ?>">⟳</button>
            </div>
        </div>

        <div class="lcc-room-row__cost">
            <span class="lcc-room-label"><?php _e( 'Est. Cost', 'luxe-cost-calculator' ); ?></span>
            <span class="lcc-room-cost-val">—</span>
        </div>

        <button type="button" class="lcc-remove-room" <?php echo ( ! $is_template && $index === 0 ) ? 'style="visibility:hidden;"' : ''; ?> aria-label="<?php esc_attr_e( 'Remove room', 'luxe-cost-calculator' ); ?>">✕</button>
    </div>
    <?php
}
