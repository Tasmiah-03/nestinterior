<?php
/**
 * Admin Settings — inc/settings.php
 * Full rate editor + plugin options
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'lcc_admin_menu' );
add_action( 'admin_init', 'lcc_admin_init' );

function lcc_admin_menu() {
    add_menu_page(
        __( 'Cost Calculator', 'luxe-cost-calculator' ),
        __( '💰 Cost Calculator', 'luxe-cost-calculator' ),
        'manage_options',
        'lcc-settings',
        'lcc_settings_page',
        'dashicons-calculator',
        25
    );
}

function lcc_admin_init() {
    register_setting( 'lcc_settings_group', 'lcc_settings', 'lcc_sanitize_settings' );
    register_setting( 'lcc_rates_group',    'lcc_rates',    'lcc_sanitize_rates' );
}

function lcc_sanitize_settings( $input ) {
    $defaults = lcc_default_settings();
    return array(
        'currency'       => sanitize_text_field( $input['currency']       ?? $defaults['currency'] ),
        'area_unit'      => in_array( $input['area_unit'] ?? '', array('sqft','sqm'), true ) ? $input['area_unit'] : 'sqft',
        'vat_rate'       => absint( $input['vat_rate']     ?? $defaults['vat_rate'] ),
        'show_vat'       => ! empty( $input['show_vat'] ),
        'show_addons'    => ! empty( $input['show_addons'] ),
        'show_breakdown' => ! empty( $input['show_breakdown'] ),
        'disclaimer'     => wp_kses_post( $input['disclaimer'] ?? $defaults['disclaimer'] ),
        'cta_text'       => sanitize_text_field( $input['cta_text'] ?? $defaults['cta_text'] ),
        'cta_url'        => esc_url_raw( $input['cta_url'] ?? $defaults['cta_url'] ),
        'lead_capture'   => ! empty( $input['lead_capture'] ),
        'min_budget'     => absint( $input['min_budget']     ?? $defaults['min_budget'] ),
        'min_budget_msg' => sanitize_text_field( $input['min_budget_msg'] ?? $defaults['min_budget_msg'] ),
    );
}

function lcc_sanitize_rates( $input ) {
    $defaults = lcc_default_rates();
    $clean    = $defaults;

    // Tier base rates
    if ( isset( $input['tiers'] ) && is_array( $input['tiers'] ) ) {
        foreach ( $input['tiers'] as $key => $val ) {
            if ( isset( $defaults['tiers'][ $key ] ) ) {
                $clean['tiers'][ $key ]['base_rate']   = absint( $val['base_rate']   ?? $defaults['tiers'][$key]['base_rate'] );
                $clean['tiers'][ $key ]['label']       = sanitize_text_field( $val['label'] ?? $defaults['tiers'][$key]['label'] );
                $clean['tiers'][ $key ]['description'] = sanitize_text_field( $val['description'] ?? $defaults['tiers'][$key]['description'] );
            }
        }
    }

    // Finish multipliers
    if ( isset( $input['finishes'] ) && is_array( $input['finishes'] ) ) {
        foreach ( $input['finishes'] as $key => $val ) {
            if ( isset( $defaults['finishes'][ $key ] ) ) {
                $clean['finishes'][ $key ]['multiplier'] = (float) ( $val['multiplier'] ?? $defaults['finishes'][$key]['multiplier'] );
            }
        }
    }

    // Room multipliers
    if ( isset( $input['rooms'] ) && is_array( $input['rooms'] ) ) {
        foreach ( $input['rooms'] as $key => $val ) {
            if ( isset( $defaults['rooms'][ $key ] ) ) {
                $clean['rooms'][ $key ]['multiplier']   = (float) ( $val['multiplier']   ?? $defaults['rooms'][$key]['multiplier'] );
                $clean['rooms'][ $key ]['typical_sqft'] = absint( $val['typical_sqft'] ?? $defaults['rooms'][$key]['typical_sqft'] );
            }
        }
    }

    // Addon values
    if ( isset( $input['addons'] ) && is_array( $input['addons'] ) ) {
        foreach ( $input['addons'] as $key => $val ) {
            if ( isset( $defaults['addons'][ $key ] ) ) {
                $clean['addons'][ $key ]['value'] = (float) ( $val['value'] ?? $defaults['addons'][$key]['value'] );
            }
        }
    }

    // VAT
    if ( isset( $input['vat_rate'] ) ) {
        $clean['vat_rate'] = absint( $input['vat_rate'] );
    }

    return $clean;
}

function lcc_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $settings = get_option( 'lcc_settings', lcc_default_settings() );
    $rates    = lcc_get_all_rates();
    $saved    = isset( $_GET['settings-updated'] );
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">💰 <?php _e( 'Cost Calculator Settings', 'luxe-cost-calculator' ); ?></h1>
        <?php if ( $saved ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e( 'Settings saved.', 'luxe-cost-calculator' ); ?></p></div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:1.5rem;">

            <!-- GENERAL SETTINGS -->
            <div>
                <h2><?php _e( 'General Settings', 'luxe-cost-calculator' ); ?></h2>
                <form method="post" action="options.php">
                    <?php settings_fields( 'lcc_settings_group' ); ?>
                    <table class="form-table">
                        <tr>
                            <th><?php _e( 'Currency', 'luxe-cost-calculator' ); ?></th>
                            <td>
                                <select name="lcc_settings[currency]">
                                    <?php foreach ( array( 'BDT' => 'BDT (৳)', 'USD' => 'USD ($)', 'GBP' => 'GBP (£)', 'EUR' => 'EUR (€)', 'AED' => 'AED' ) as $c => $l ) : ?>
                                    <option value="<?php echo esc_attr($c); ?>" <?php selected( $settings['currency'], $c ); ?>><?php echo esc_html($l); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Area Unit', 'luxe-cost-calculator' ); ?></th>
                            <td>
                                <select name="lcc_settings[area_unit]">
                                    <option value="sqft" <?php selected( $settings['area_unit'], 'sqft' ); ?>>Square Feet (sqft)</option>
                                    <option value="sqm"  <?php selected( $settings['area_unit'], 'sqm' ); ?>>Square Metres (m²)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'VAT / Tax Rate (%)', 'luxe-cost-calculator' ); ?></th>
                            <td>
                                <input type="number" name="lcc_settings[vat_rate]" value="<?php echo esc_attr( $settings['vat_rate'] ); ?>" min="0" max="50" style="width:80px;">
                                <label><input type="checkbox" name="lcc_settings[show_vat]" value="1" <?php checked( $settings['show_vat'] ); ?>> Show VAT line item</label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Minimum Budget', 'luxe-cost-calculator' ); ?></th>
                            <td>
                                <input type="number" name="lcc_settings[min_budget]" value="<?php echo esc_attr( $settings['min_budget'] ); ?>" style="width:140px;">
                                <p class="description"><?php _e( 'Show warning if estimate falls below this amount.', 'luxe-cost-calculator' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Min Budget Message', 'luxe-cost-calculator' ); ?></th>
                            <td><input type="text" name="lcc_settings[min_budget_msg]" value="<?php echo esc_attr( $settings['min_budget_msg'] ); ?>" style="width:100%;"></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'CTA Button Text', 'luxe-cost-calculator' ); ?></th>
                            <td><input type="text" name="lcc_settings[cta_text]" value="<?php echo esc_attr( $settings['cta_text'] ); ?>" style="width:300px;"></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'CTA Button URL', 'luxe-cost-calculator' ); ?></th>
                            <td><input type="url" name="lcc_settings[cta_url]" value="<?php echo esc_attr( $settings['cta_url'] ); ?>" style="width:300px;"></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Lead Capture', 'luxe-cost-calculator' ); ?></th>
                            <td><label><input type="checkbox" name="lcc_settings[lead_capture]" value="1" <?php checked( $settings['lead_capture'] ); ?>> Require name & email before showing results</label></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Show Add-ons', 'luxe-cost-calculator' ); ?></th>
                            <td><label><input type="checkbox" name="lcc_settings[show_addons]" value="1" <?php checked( $settings['show_addons'] ); ?>> Show optional add-on services</label></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Show Room Breakdown', 'luxe-cost-calculator' ); ?></th>
                            <td><label><input type="checkbox" name="lcc_settings[show_breakdown]" value="1" <?php checked( $settings['show_breakdown'] ); ?>> Show per-room cost breakdown</label></td>
                        </tr>
                        <tr>
                            <th><?php _e( 'Disclaimer Text', 'luxe-cost-calculator' ); ?></th>
                            <td><textarea name="lcc_settings[disclaimer]" rows="4" style="width:100%;"><?php echo esc_textarea( $settings['disclaimer'] ); ?></textarea></td>
                        </tr>
                    </table>
                    <?php submit_button( __( 'Save General Settings', 'luxe-cost-calculator' ) ); ?>
                </form>
            </div>

            <!-- RATE EDITOR -->
            <div>
                <h2><?php _e( 'Cost Rates', 'luxe-cost-calculator' ); ?></h2>
                <form method="post" action="options.php">
                    <?php settings_fields( 'lcc_rates_group' ); ?>

                    <h3><?php _e( 'Service Tier Base Rates', 'luxe-cost-calculator' ); ?></h3>
                    <p class="description"><?php printf( __( 'Base rates in %s per %s', 'luxe-cost-calculator' ), $settings['currency'], $settings['area_unit'] ); ?></p>
                    <table class="widefat striped" style="margin-bottom:1.5rem;">
                        <thead><tr><th><?php _e('Tier','luxe-cost-calculator'); ?></th><th><?php _e('Base Rate','luxe-cost-calculator'); ?></th><th><?php _e('Description','luxe-cost-calculator'); ?></th></tr></thead>
                        <tbody>
                        <?php foreach ( $rates['tiers'] as $key => $tier ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $tier['icon'] . ' ' . $tier['label'] ); ?></strong></td>
                            <td><input type="number" name="lcc_rates[tiers][<?php echo esc_attr($key); ?>][base_rate]" value="<?php echo esc_attr( $tier['base_rate'] ); ?>" style="width:90px;" min="0"></td>
                            <td><input type="text" name="lcc_rates[tiers][<?php echo esc_attr($key); ?>][description]" value="<?php echo esc_attr( $tier['description'] ); ?>" style="width:100%;"></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3><?php _e( 'Finish Level Multipliers', 'luxe-cost-calculator' ); ?></h3>
                    <table class="widefat striped" style="margin-bottom:1.5rem;">
                        <thead><tr><th><?php _e('Level','luxe-cost-calculator'); ?></th><th><?php _e('Multiplier (×)','luxe-cost-calculator'); ?></th><th><?php _e('Description','luxe-cost-calculator'); ?></th></tr></thead>
                        <tbody>
                        <?php foreach ( $rates['finishes'] as $key => $finish ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $finish['icon'] . ' ' . $finish['label'] ); ?></strong></td>
                            <td><input type="number" step="0.1" name="lcc_rates[finishes][<?php echo esc_attr($key); ?>][multiplier]" value="<?php echo esc_attr( $finish['multiplier'] ); ?>" style="width:70px;" min="0.5" max="10"></td>
                            <td><?php echo esc_html( $finish['desc'] ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3><?php _e( 'Room Type Multipliers', 'luxe-cost-calculator' ); ?></h3>
                    <table class="widefat striped" style="margin-bottom:1.5rem;">
                        <thead><tr><th><?php _e('Room','luxe-cost-calculator'); ?></th><th><?php _e('Multiplier','luxe-cost-calculator'); ?></th><th><?php printf( __('Typical %s','luxe-cost-calculator'), $settings['area_unit'] ); ?></th></tr></thead>
                        <tbody>
                        <?php foreach ( $rates['rooms'] as $key => $room ) : ?>
                        <tr>
                            <td><?php echo esc_html( $room['icon'] . ' ' . $room['label'] ); ?></td>
                            <td><input type="number" step="0.05" name="lcc_rates[rooms][<?php echo esc_attr($key); ?>][multiplier]" value="<?php echo esc_attr( $room['multiplier'] ); ?>" style="width:70px;"></td>
                            <td><input type="number" name="lcc_rates[rooms][<?php echo esc_attr($key); ?>][typical_sqft]" value="<?php echo esc_attr( $room['typical_sqft'] ); ?>" style="width:70px;"></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3><?php _e( 'Add-on Services', 'luxe-cost-calculator' ); ?></h3>
                    <table class="widefat striped" style="margin-bottom:1.5rem;">
                        <thead><tr><th><?php _e('Service','luxe-cost-calculator'); ?></th><th><?php _e('Type','luxe-cost-calculator'); ?></th><th><?php _e('Value','luxe-cost-calculator'); ?></th></tr></thead>
                        <tbody>
                        <?php foreach ( $rates['addons'] as $key => $addon ) : ?>
                        <tr>
                            <td><?php echo esc_html( $addon['icon'] . ' ' . $addon['label'] ); ?></td>
                            <td><?php echo esc_html( $addon['type'] === 'percent' ? '% of subtotal' : 'Fixed amount' ); ?></td>
                            <td><input type="number" name="lcc_rates[addons][<?php echo esc_attr($key); ?>][value]" value="<?php echo esc_attr( $addon['value'] ); ?>" style="width:90px;" min="0"></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php submit_button( __( 'Save Rates', 'luxe-cost-calculator' ) ); ?>
                </form>
            </div>
        </div>

        <!-- SHORTCODE REFERENCE -->
        <div style="background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;padding:1.5rem;margin-top:2rem;">
            <h3><?php _e( 'How to Use', 'luxe-cost-calculator' ); ?></h3>
            <p><?php _e( 'Add this shortcode to any page or widget:', 'luxe-cost-calculator' ); ?></p>
            <code style="background:#fff;padding:0.5rem 1rem;display:inline-block;font-size:14px;">[lbs_cost_calculator]</code>
            <p style="margin-top:0.75rem;"><?php _e( 'Optional attributes:', 'luxe-cost-calculator' ); ?></p>
            <code style="background:#fff;padding:0.5rem 1rem;display:block;font-size:13px;margin-top:0.5rem;">[lbs_cost_calculator title="Estimate Your Project Cost" default_tier="full_design" default_finish="premium" show_addons="true"]</code>
        </div>
    </div>
    <?php
}
