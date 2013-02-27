<?php

class SEE_Widget extends WP_Widget
{

    /**
     * An array of SEE_Widget_Data objects which define all data points for
     * this widget. We use the variable names for keys.
     * @var array Option name => SEE_Widget_Data object
     */
    protected $data = array();

    public function __construct( $class = "", $title = "", $options=array() ) {
        parent::__construct( $class, $title, $options );
    }

    /**
     * Returns an array of key value pairs to be used to update the stored data
     * for this widget.
     * @param  array $newInstance New data to be stored
     * @param  array $oldInstance Currently stored data
     * @return array
     */
    public function update( $newInstance, $oldInstance ) {
        $values = array();
        foreach ( $newInstance as $key => $value ) {
            $values[ $key ] = htmlentities( $value );
        }
        return $values;
    }

    protected function _getWidget( $name, $instance ) {
        $attrs = '';
        foreach ( $instance as $key => $value ) {
            $attrs .= ' data-see-' . $key . '="' . $value . '"';
        }
        $widget = '<h1 class="widget-header">' . $name . '</h1>';
        $widget .= '<div class="see-widget" data-see-type="' . $name . '"' . $attrs . '></div>';
        return $widget;
    }

    public function form( $instance ) {
        foreach( $instance as $key => $value ) {
            if ( isset( $this->data[ $key ] ) ) {
                $this->data[ $key ]->value = $value;
            }
        }
        echo $this->_getForm( $instance );
    }

    protected function _getForm( $instance ) {
        $output = "";
        foreach ( $this->data as $name => $dataObj ) {
            $output .= sprintf( '<label for="%s">%s</label><br />', $this->get_field_id( $dataObj->name ), ucfirst( $dataObj->name ) );
            switch( $dataObj->input_type ) {
                case "select":
                    $output .= self::select_input( $dataObj->name, $dataObj->options, $dataObj->value );
                    break;
                case "text":
                default:
                    $output .= self::text_input( $dataObj->name, $dataObj->value );
                    break;
            }
            $output .= "<br />";
        }
        return $output;
    }

    private function input( $name, $type, $value ) {
        return sprintf( '<input type="%s" name="%s" id="%s" value="%s" />', $type, $this->get_field_name( $name ), $this->get_field_id( $name ), $value );
    }

    private function text_input( $name, $value = "" ) {
        return self::input( $name, "text", $value );
    }

    private function select_input( $name, $options, $selected = null ) {
        $output = sprintf( '<select name="%s" id="%s">', $this->get_field_name( $name ), $this->get_field_id( $name ) );

        foreach( $options as $value => $display ) {
            $is_selected = ( $value == $selected ) ? ' selected="selected"' : "";
            $output .= sprintf( '<option value="%s"%s>%s</option>', $value, $is_selected, $display );
        }

        $output .= "</select>";
        return $output;
    }
}

class SEE_Widget_Data {
    public $name = "";
    public $value = 0;
    public $input_type = "text";
    public $validate = null;

    /**
     * Used for 'select' inputs, a key => value array cont
     * @var array
     */
    public $options = array();

    public function __construct( $name, $input_type, $default = 0, $validate = null ) {
        $this->name = $name;
        $this->input_type = $input_type;
        $this->value = $default;
        $this->validate = $validate;
    }
}

//////////////////////////////////////////////////////////////////////////////////////

/// should this just be part of the main plugin???
class SEEBar extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_bar",
            "Triton SEE Bar",
            array( "description" => "A description goes here..." )
        );

        $this->data["leaderboard"] = new SEE_Widget_Data( "leaderboard", "text", "XP" );
    }

    public function widget( $args, $instance ) {
        echo '<div class="see-widget" data-see-type="Bar"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEELeaderboard extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_leaderboard",
            "Triton SEE Leaderboard",
            array( "description" => "A description goes here..." )
        );

        $this->data["view"] = new SEE_Widget_Data( "view", "select", "all" );
        $this->data["view"]->options = array( 
            "day" => "Day", 
            "week" => "Week", 
            "all" => "All" 
        );
        $this->data["source"] = new SEE_Widget_Data( "source", "text", "XP" );
        $this->data["positions"] = new SEE_Widget_Data( "positions", "text", "5", "numeric" );
        $this->data["userbased"] = new SEE_Widget_Data( "userbased", "select", "false" );
        $this->data["userbased"]->options = array( 
            "true" => "Yes",
            "false" => "No"
        );

    }

    public function widget( $args, $instance ) {
        echo $this->_getWidget( "Leaderboard", $instance );
    }


}

//////////////////////////////////////////////////////////////////////////////////////

class SEETopScore extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_topscore",
            "Triton SEE Top Score",
            array( "description" => "A description goes here..." )
        );

        $this->data["source"] = new SEE_Widget_Data( "source", "text", "XP" );
    }

    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">Top Score</h1>';
        echo '<div class="see-widget" data-see-type="TopScore" data-see-source="XP"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEEScoreBoard extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_scoreboard",
            "Triton SEE Score Board",
            array( "description" => "A description goes here..." )
        );
    }

    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">Scoreboards</h1>';
        echo '<div class="see-widget" data-see-type="Scoreboards"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEEActivity extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_activity",
            "Triton SEE Activity",
            array( "description" => "A description goes here..." )
        );
    }

    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">Activity</h1>';
        echo '<div class="see-widget" data-see-type="Activity"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEENextlevel extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_nextlevel",
            "Triton SEE Nextlevel",
            array( "description" => "A description goes here..." )
        );

        $this->data["source"] = new SEE_Widget_Data( "source", "text", "XP" );
    }

    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">NextLevel</h1>';
        echo '<div class="see-widget" data-see-type="NextLevel" data-see-source="XP"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEEProfile extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_profile",
            "Triton SEE Profile",
            array( "description" => "A description goes here..." )
        );
    }

    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">Profile</h1>';
        echo '<div class="see-widget" data-see-type="Profile"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEEAchievments extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_achievments",
            "Triton SEE Achievments",
            array( "description" => "A description goes here..." )
        );

        $this->data["view"] = new SEE_Widget_Data( "view", "select", "all" );
        $this->data["view"]->options = array(
            "earned" => "Earned",
            "unearned" => "Unearned",
            "all" => "All"
        );

        // TODO: Would be great if we could specify a custom validation function.
        // For limit, we should accept either an integer or 'all', for example..
        $this->data["limit"] = new SEE_Widget_Data( "limit", "text", "all" );
        $this->data["sort"] = new SEE_Widget_Data( "sort", "text" );
    }

    // override attrs will be grabbed by see.js
    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">Achievments</h1>';
        echo '<div class="see-widget" data-see-type="Achievements" data-see-view="all"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

class SEECheckin extends SEE_Widget
{
    public function __construct() {
        parent::__construct(
            "see_checkin",
            "Triton SEE Checkin",
            array( "description" => "A description goes here..." )
        );
    }

    public function widget( $args, $instance ) {
        echo '<h1 class="widget-header">Checkin</h1>';
        echo '<div class="see-widget" data-see-type="Checkin"></div>';
    }
}

//////////////////////////////////////////////////////////////////////////////////////

// class SEELeaderboard extends WP_Widget
// {
//     public function __construct() {
//         parent::__construct(
//             "see_leaderboard",
//             "Triton SEE Leaderboard",
//             array("description" => "A description goes here...")
//         );
//     }

//     // public function form($instance) {
//     //     $view = "";
//     //     $source = "";
//     //     // if instance is defined, populate the fields
//     //     if (!empty($instance)) {
//     //         $view = $instance["view"];
//     //         $source = $instance["source"];
//     //     }

//     //     $fieldId = $this->get_field_id("view");
//     //     $fieldName = $this->get_field_name("view");
//     //     echo '<label for="' . $fieldId . '">view</label><br>';
//     //     echo '<input id="' . $fieldId . '" type="source" name="' .
//     //         $fieldName . '" value="' . $view . '"><br>';

//     //     $sourceId = $this->get_field_id("source");
//     //     $sourceName = $this->get_field_name("source");
//     //     echo '<label for="' . $sourceId . '">source</label><br>';
//     //     echo '<sourcearea id="' . $sourceId . '" name="' . $sourceName .
//     //         '">' . $source . '</sourcearea>';
//     // }

//     // public function update($newInstance, $oldInstance) {
//     //     $values = array();
//     //     $values["view"] = htmlentities($newInstance["view"]);
//     //     $values["source"] = htmlentities($newInstance["source"]);
//     //     return $values;
//     // }

//     public function widget($args, $instance) {
//         //$view = $instance["view"];
//         //$source = $instance["source"];

//         echo '<h1 class="widget-header">Leaderboard</h1>';
//         echo '<div class="see-widget" data-see-type="Leaderboard" data-see-userbased="true" data-see-source="XP"></div>';
//         echo '<h1 class="widget-header">Top Score</h1>';
//         echo '<div class="see-widget" data-see-type="TopScore" data-see-source="XP"></div>';
//         echo '<h1 class="widget-header">Scoreboards</h1>';
//         echo '<div class="see-widget" data-see-type="Activity"></div>';
//         echo '<h1 class="widget-header">NextLevel</h1>';
//         echo '<div class="see-widget" data-see-type="NextLevel" data-see-source="XP"></div>';
//         echo '<h1 class="widget-header">Profile</h1>';
//         echo '<div class="see-widget" data-see-type="Profile"></div>';
//         echo '<h1 class="widget-header">Achievments</h1>';
//         echo '<div class="see-widget" data-see-type="Achievements" data-see-view="all"></div>';
//         echo '<h1 class="widget-header">Checkin</h1>';
//         echo '<div class="see-widget" data-see-type="Checkin"></div>';


//        // echo '<h1 class="widget-header">Leaderboard</h1>';
//         //echo '<div class="see-widget" data-see-type="Leaderboard" data-see-userbased="true" data-see-source="XP"></div>';

//     }
// }
