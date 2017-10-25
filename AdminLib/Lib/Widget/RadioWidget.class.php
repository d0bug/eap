<?php
class RadioWidget extends Widget {
    public function render($data){
        $string = '';
        $name = $data['name'];
        $items = $data['items'];
        $checkValue = $data['value'];
        $label = isset($data['label']) ? $data['label'] : true;
        $class = $data['class'] ? ' class="' . $data['class'] . '"' : '';
        $style = $data['style'] ? ' style="' . $data['style'] . '"' : '';
        $disabled = $data['disabled'] ? ' disabled="true" ': '';
        foreach ($items as $key=>$value) {
            $id = $checked = '';
            if(is_array($value)) {
                $id = $value['id'] ? ' id="' . $value['id'] . '"' : '';
                $text = $value['text'];
            } else {
                $text = $value;
            }
            if($key == $checkValue) {
                $checked = 'checked="true"';
            }
            $str = '<input type="radio" '. $checked . $class . $style . $id . $disabled . ' name="' . $name . '" value="' . $key . '" />' . $text;
            if($label) {
                $string .= '<label>' . $str . '</label>';
            } else {
                $string .= $str;
            }
        }
        return $string;
    }
}
?>