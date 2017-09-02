<?php
/**
 * Common_Slider_Settings class
 *
 * @since 2.6.0
 *
 * @package WordPress
 *
 */
class Common_Function {
    
     /**
     * Return html for text type is number
     * @return textbox
     */
    public function generate_numberbox($name, $id, $class, $max, $min=0, $value){
        
        $html = "<input class='" . $class . "' id='" . $id . "' type='number' name='" . $name . "' min='" .$min ."' max='" . $max . "' value='" . $value ."'>";
        echo  $html;
    }
    
    
     /**
     * Return html for text type is number
     * @return radio
     */
    public function generate_radiobox($name, $id, $class, $checked){
        
        if(1 == $checked){
            $checked1 = "checked";
            $checked0 = '';
        }else{
            $checked0 = "checked";
            $checked1 = '';
        }
        
        $html = "<div class='buttonset'>";
        $html .= "<label class='' for='" . $id . "_0'>NO</label>";
        $html .= "<input class='" . $class . "' id='" . $id . "_0' type='radio' name='" . $name . "' " . $checked0 . " value='0'/>";
        $html .= "<label class='' for='" . $id . "_1'>Yes</label>";                                        
        $html .= "<input class='" . $class . "' id='" .  $id . "_1' type='radio' name='" . $name . "' " . $checked1 . " value='1'/>";
        $html .= "</div>";
        echo $html;
    }
    public function getcarousel_settings($array,$element){
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'save'){
            $settings = unserialize($array);
            return $settings[$element];
        }
        else
        {
            return FALSE;
        }
    }
    public function generate_selectbox($name, $options , $class, $value){
        if(1 == $value){
            $checked1 = "checked";
            $checked0 = '';
        }else{
            $checked0 = "checked";
            $checked1 = '';
        }
        
        $html = "<select class='" . $class . "' name='" . $name ."' id='" . $name . "' >";
        foreach($options as $option){
            $selected = '';
            if($value === $option){
                $selected = "selected='selected'";
            }
            $html .= "<option value=" . $option  . " " . $selected . ">" . $option . "</option>";
        }
        $html .= "</select>";
        echo $html;
    }
}



?>