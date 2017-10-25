<?php
class ArraySelectWidget extends Widget {
    public function render($data) {
        $array = $data['options'];
        $value = $data['value'];
        $attr = $data['attr'];
        $html = '<select ' . $attr . '>';
        foreach ($array as $key=>$val) {
        	$html .= '<option value="' . $key . '"';
        	if(is_array($value)) {
        	   if(in_array($key, $value)) {
        	       $html .= ' selected="true"';
        	   }
        	} else if($key == $value){
        	   $html .= ' selected="true"';
        	}
        	$html .= '>' . $val . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
?>