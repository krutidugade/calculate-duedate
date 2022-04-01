<?php
/*
Plugin Name:  Calculate due date plugin
Plugin URI:    
Description:  This plugin offers widget that calculates due date of your pregnancy based on Last Menstrual Period 
Version:      1.0
Author:       Kruti Dugade 
Author URI:   h
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  
Domain Path:  /languages
*/

// exit if we try to access it directly
if ( !defined( 'ABSPATH' ) )
exit;

//Registers custom widget
function duedate_register_widget() {
    register_widget( 'duedate_widget' );
}
add_action( 'widgets_init', 'duedate_register_widget' );

// Enqueues scripts and styles for this plugin
add_action('wp_enqueue_scripts', 'due_date_scripts');

function due_date_scripts() {
    wp_register_style( 'calculate-due-date', plugin_dir_url( __FILE__ ) . 'calculate-due-date.css' ) ;
    wp_enqueue_style( 'calculate-due-date' );
}

class duedate_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'duedate_widget',
            __('Due Date Widget', 'duedate_widget_domain'),
            array ( 'description' => __( 'Calculate Due Date Widget', 'duedate_widget_domain' ), )
        ); // widget ID, widget name and widget description
    }

    public function widget( $args, $instance ) {

        $title = apply_filters( 'widget_title', $instance['title'] );

        ?>

        <!-- Creates form to select last menstrual period and calculates due date on the site  -->
        <form method="POST" class="widget dd_widget"> 
            <h2 class="dd_widget_title"><?php echo $title; ?></h2>
            <label for="lmp">Select your LMP (Last Menstrual Period)</label><br/>
            <input type="date" id="lmp" name="lmp" value="dd/mm/yyyy"/>
            <input type="submit" name="calculate" value="Calculate"/>
            
        <?php

        if(isset($_POST['calculate'])){

            //Checks if date is selected
            if ($_POST['lmp']) {
                $new_date = date('Y-m-d', strtotime($_POST['lmp']));
                $due_date = date('Y-m-d', strtotime($new_date. ' + 280 days'));
                
                ?>
                <p>Your estimated due date is <b><?php echo $due_date; ?></b></p>
                <?php
                
            }
            else {
                ?>
                <p>Oops! You missed selecting a date.</p>
                </form>
                <?php
            }
        }      
        
    }
    
    //Creates widget form in backend where you can add custom widget title
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) )
        $title = $instance[ 'title' ];
        else
        $title = __( 'Due Date Calculator', 'duedate_widget_domain' );
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    //Updates widget title if changed
    public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            return $instance;
    }   


}
?>