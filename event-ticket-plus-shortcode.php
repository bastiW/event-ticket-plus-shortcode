<?php

/*
Plugin Name: Event Ticket Plus Shortcode
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: bastiW
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
*/



/*
$post = $_POST;

highlight_string("<?php\n\$pst =\n" . var_export($post, true) . ";\n?>");
*/



function event_tickets_plus_shortcode( $atts ) {

    if(is_admin()) {
       return;
    }

    global $woocommerce;


    //Default value for clear the woo commerce cart
    $emptyCart = true;

    $atts = shortcode_atts( array(
        'id' => null,
        'emptycart' => $emptyCart
    ), $atts, 'tribe_tickets' );

    $tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $atts[id] );

    $is_there_any_product         = false;
    $is_there_any_product_to_sell = false;
    //$unavailability_messaging     = is_callable( array( $this, 'do_not_show_tickets_unavailable_message' ) );

    if ( ! empty( $tickets ) ) {
        $tickets = tribe( 'tickets.handler' )->sort_tickets_by_menu_order( $tickets );
    }


    if($atts['emptycart'] === true ||  strtolower($atts['emptycart']) === 'true' || $atts['emptycart'] == 1) {
        $emptyCart = (bool) true;
    } elseif ($atts['emptycart'] === false || strtolower($atts['emptycart']) === 'false' || $atts['emptycart'] == 0 ) {
        $emptyCart = (bool) false;
    }



    if($emptyCart == true) {
        $woocommerce->cart->empty_cart();
    }


    ob_start();

    /**
     * Filter classes on the Cart Form
     *
     * @since  4.3.2
     *
     * @param array $cart_classes
     */
    $cart_classes = (array) apply_filters( 'tribe_events_tickets_woo_cart_class', array( 'cart' ) );
    ?>
    <style>
        .tribe-ticket-shortcode {
            margin: auto;
            max-width: 900px;
        }

        .total-quantity {
            background: #f8f8f8;
            text-align: center;
            border-bottom: 1px solid #dfdfdf;
            color: #464646;
            font-size: 15px;
            padding: 16px 10px;
            font-weight: bold;
        }

        .tribe-events-tickets .tribe-tickets-remaining {
            display: none;
        }

        button.tribe-button {
            /* border-color: #E62B1E; */
            /* background: #E62B1E; */
            color: #fff;
            font-family: Poppins, Helvetica, Arial, sans-serif;
            display: inline-block;
            outline: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            padding: .6em 2.5em .6em 2.5em;
            color: #fff;
            background: #E62B1E;
            border: 2px solid #E62B1E;
            font-size: 15px;
            font-family: 'Poppins', 'Helvetica Neue', Arial,Verdana,sans-serif;
            text-shadow: none;
            -webkit-appearance: none;
            box-shadow: 0 0 0 0;
            line-height: 1.5 !important;
            border-radius: 5px;
            font-style: normal;
            font-weight: 500;
            -webkit-transition: color .2s linear, background .1s linear, opacity .2s linear;
            -moz-transition: color .2s linear, background .1s linear, opacity .2s linear;
            -ms-transition: color .2s linear, background .1s linear, opacity .2s linear;
            -o-transition: color .2s linear, background .1s linear, opacity .2s linear;
            transition: color .2s linear, background .1s linear, opacity .2s linear;
        }

        .tribe-event-tickets-plus-meta {
            display: none;
        }
        .tribe-events-tickets td.woocommerce, .tribe-events-tickets .quantity, .tribe-events-tickets .tickets_name, .tribe-events-tickets .tickets_price, .tribe-events-tickets .woocommerce {
        //border-bottom: none;
        }

        @media (max-width: 600px) {
            button.tribe-button {
                width: 100%
            }
            .tribe-events-tickets, .tribe-events-tickets tbody {
                display: table !important;
                width: 100%;
            }

            .tribe-events-style-full .tribe-events-tickets td, .tribe-events-tickets td {
                display: table-cell !important;
            }
            .tribe-events-tickets tr {
                display: table-row !important;
            }
        }





    </style>
    <div class="tribe-ticket-shortcode">
        <div class="tribe-ticket-inner-shortcode">
            <form
                id="buy-tickets"
                action="
                <?php
                    echo esc_url(  $woocommerce->cart->get_cart_url() )
                ?>"
                class="<?php echo esc_attr( implode( ' ', $cart_classes ) ); ?>"
                method="post"
                enctype='multipart/form-data'>



                <div class="total-quantity">


                    <?php

                    if( $tickets[0]->stock() <= 0 ) {
                        echo "Ausverkauft! Du kannst dir nach dem Event die Vorträge auf Youtube ansehen. Melde dich zum Newsletter an. Dann geben wir bescheid, wenn die Videos verfügbar sind.";
                    } elseif ($tickets[0]->stock() <= 20) {
                        echo "Nur noch " .  $tickets[0]->stock() . " Tickets verfügbar";
                    } else {
                        echo "Noch " .  $tickets[0]->stock() . " Tickets verfügbar";
                    };

                    ?>


                </div>
                <table class="tribe-events-tickets">








                    <?php
                    /**
                     * Reorder the tickets per the admin interface order
                     *
                     * @since 4.6
                     */
                    foreach ( $tickets as $ticket ) :
                        /**
                         * Changing any HTML to the `$ticket` Arguments you will need apply filters
                         * on the `wootickets_get_ticket` hook.
                         */

                        /**
                         * @var Tribe__Tickets__Ticket_Object $ticket
                         * @var WC_Product $product
                         */
                        global $product;

                        if ( class_exists( 'WC_Product_Simple' ) ) {
                            $product = new WC_Product_Simple( $ticket->ID );
                        } else {
                            $product = new WC_Product( $ticket->ID );
                        }


                        $is_there_any_product = true;
                        $data_product_id      = '';

                        if ( $ticket->date_in_range( current_time( 'timestamp' ) ) ) {

                            $is_there_any_product = true;

                            echo sprintf( '<input type="hidden" name="product_id[]" value="%d">', esc_attr( $ticket->ID ) );

                            /**
                             * Filter classes on the Price column
                             *
                             * @since  4.3.2
                             *
                             * @param array $column_classes
                             * @param int $ticket->ID
                             */
                            $column_classes = (array) apply_filters( 'tribe_events_tickets_woo_quantity_column_class', array( 'woocommerce' ), $ticket->ID );

                            // Max quantity will be left open if backorders allowed, restricted to 1 if the product is
                            // constrained to be sold individually or else set to the available stock quantity
                            $max_quantity = $product->backorders_allowed() ? '' : $product->get_stock_quantity();
                            $max_quantity = $product->is_sold_individually() ? 1 : $max_quantity;
                            $available    = $ticket->available();

                            /**
                             * Filter classes on the row
                             *
                             * @since  4.5.5
                             *
                             * @param array $row_classes
                             * @param int $ticket->ID
                             */
                            $row_classes = (array) apply_filters( 'tribe_events_tickets_row_class', array( 'woocommerce', 'tribe-tickets-form-row' ), $ticket->ID );
                            echo '<tr class="' . esc_attr( implode( ' ', $row_classes ) ) . '" data-product-id="' . esc_attr( $ticket->ID ) . '">';

                            /**
                             * Filter classes on the Price column
                             *
                             * @since  4.3.2
                             *
                             * @param array $column_classes
                             */
                            $column_classes = (array) apply_filters( 'tribe_events_tickets_woo_quantity_column_class', array( 'woocommerce' ) );
                            echo '<td class="' . esc_attr( implode( ' ', $column_classes ) ) . '" data-product-id="' . esc_attr( $ticket->ID ) . '">';

                            if ( 0 !== $available ) {
                                // Max quantity will be left open if backorders allowed, restricted to 1 if the product is
                                // constrained to be sold individually or else set to the available stock quantity
                                $stock        = $ticket->stock();
                                $max_quantity = $product->backorders_allowed() ? '' : $stock;
                                $max_quantity = $product->is_sold_individually() ? 1 : $max_quantity;
                                $available    = $ticket->available();

                                woocommerce_quantity_input( array(
                                    'input_name'  => 'quantity_' . $ticket->ID,
                                    'input_value' => 0,
                                    'min_value'   => 0,
                                    'max_value'   => $must_login ? 0 : $max_quantity, // Currently WC does not support a 'disable' attribute
                                ) );

                                $is_there_any_product_to_sell = true;

                                if ( $available ) {
                                    ?>
                                    <span class="tribe-tickets-remaining">
							<?php
                            $readable_amount = tribe_tickets_get_readable_amount( $available, null, false );
                            echo sprintf( esc_html__( '%1$s available', 'event-tickets-plus' ),
                                '<span class="available-stock" data-product-id="' . esc_attr( $ticket->ID ) . '">' . esc_html( $readable_amount ) . '</span>'
                            );
                            ?>
							</span>
                                    <?php
                                }

                                do_action( 'wootickets_tickets_after_quantity_input', $ticket, $product );
                            } else {
                                echo '<span class="tickets_nostock">' . esc_html__( 'Out of stock!', 'event-tickets-plus' ) . '</span>';
                            }

                            echo '</td>';

                            echo '<td class="tickets_name">' . $ticket->name . '</td>';

                            echo '<td class="tickets_price">';

                            if ( method_exists( $product, 'get_price' ) && $product->get_price() ) {
                                echo $product->get_price_html( $product );

                            } else {
                                esc_html_e( 'Free', 'event-tickets-plus' );
                            }

                            echo '</td>';

                            //echo '<td class="tickets_description">' . ( $ticket->show_description() ? $ticket->description : '' ) . '</td>';

                            echo '</tr>';

                            if ( $product->is_in_stock() ) {
                                /**
                                 * Use this filter to hide the Attendees List Optout
                                 *
                                 * @since 4.5.2
                                 *
                                 * @param bool
                                 */
                                $hide_attendee_list_optout = apply_filters( 'tribe_tickets_plus_hide_attendees_list_optout', false );
                                if ( ! $hide_attendee_list_optout
                                    && class_exists( 'Tribe__Tickets_Plus__Attendees_List' )
                                    && ! Tribe__Tickets_Plus__Attendees_List::is_hidden_on( get_the_ID() )
                                ) { ?>
                                    <tr class="tribe-tickets-attendees-list-optout">
                                        <td colspan="4">
                                            <input
                                                type="checkbox"
                                                name="optout_<?php echo esc_attr( $ticket->ID ); ?>"
                                                id="tribe-tickets-attendees-list-optout-edd"
                                            >
                                            <label for="tribe-tickets-attendees-list-optout-edd"><?php esc_html_e( "Don't list me on the public attendee list", 'event-tickets-plus' ); ?></label>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                include Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'meta.php' );
                            }
                        }

                    endforeach; ?>

                    <?php if ( $is_there_any_product_to_sell ) : ?>
                        <tr>
                            <td colspan="4" class="woocommerce add-to-cart">
                                <?php if ( $must_login ) : ?>
                                    <?php include Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'login-to-purchase' ); ?>
                                <?php else: ?>
                                    <button
                                        type="submit"
                                        name="wootickets_process"
                                        value="1"
                                        class="tribe-button"
                                    >
                                        <?php esc_html_e( 'Add to cart', 'event-tickets-plus' );?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <noscript>
                        <tr>
                            <td class="tribe-link-tickets-message">
                                <div class="no-javascript-msg"><?php esc_html_e( 'You must have JavaScript activated to purchase tickets. Please enable JavaScript in your browser.', 'event-tickets-plus' ); ?></div>
                            </td>
                        </tr>
                    </noscript>
                </table>
            </form>

        </div>
    </div>
    <script>
        document.getElementsByClassName("cw_qty")[0].value = 1
    </script>
    <?php

    $output = ob_get_contents();
    ob_end_clean();
    return $output;





    //return var_dump($tickets);
    //return  var_dump(tribe_events_has_soldout($atts['id']));
    //tribe_tickets_parent_post($atts['id']);
}
add_shortcode( 'evtp-tickets', 'event_tickets_plus_shortcode' );

function woocommerce_quantity_input($data = null) {
    global $product;
    if (!$data) {
        $defaults = array(
            'input_name'   => 'quantity',
            'input_value'   => '1',
            'max_value'     => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
            'min_value'     => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
            'step'         => apply_filters( 'woocommerce_quantity_input_step', '1', $product ),
            'style'         => apply_filters( 'woocommerce_quantity_style', 'float:left;', $product )
        );
    } else {
        $defaults = array(

            'input_name'   => $data['input_name'],

            'input_value'   => $data['input_value'],
            'step'         => apply_filters( 'cw_woocommerce_quantity_input_step', '1', $product ),

            'max_value'     => apply_filters( 'cw_woocommerce_quantity_input_max', '', $product ),

            'min_value'     => apply_filters( 'cw_woocommerce_quantity_input_min', '', $product ),

            'style'         => apply_filters( 'cw_woocommerce_quantity_style', 'float:left;', $product )

        );

    }



    if ( ! empty( $defaults['min_value'] ) )

        $min = $defaults['min_value'];

    else $min = 0;



    if ( ! empty( $defaults['max_value'] ) )

        $max = $defaults['max_value'];

    else $max = 4;



    if ( ! empty( $defaults['step'] ) )

        $step = $defaults['step'];

    else $step = 1;



    $options = '';



    for ( $count = $min; $count <= $max; $count = $count+$step ) {

        $selected = $count === $defaults['input_value'] ? ' selected' : '';

        $options .= '<option value="' . $count . '"'.$selected.'>' . $count . '</option>';

    }



    echo '<div class="cw_quantity_select" style="' . $defaults['style'] . '"><select name="' . esc_attr( $defaults['input_name'] ) . '" title="' . _x( 'Qty', 'Product Description', 'woocommerce' ) . '" class="cw_qty">' . $options . '</select></div>';



}

