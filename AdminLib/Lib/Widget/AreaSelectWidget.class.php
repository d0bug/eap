<?php 
class AreaSelectWidget extends Widget {
    public function render($data) {
        $areas = $data['areas'];
        $multi = $data['multi'];
        $name = $data['name'];
        $id = $data['id'];
        $showCode = $data['showCode'];
        $size = abs($data['size']);
        if($areas && false == is_array($areas)) {
            $areas = explode(',', $areas);
        }
        $areaModel = D('Area');
        $areaOptions = $areaModel->getAreaOptions();
        $html = '<select';
        if($name) $html .= ' name="' . $name . '"';
        if($id) $html .= ' id="' . $id . '"';
        if($multi) $html .= ' multiple="true"';
        if($size) $html .= ' size="' . $size . '"';
        $html .='>';
        foreach ($areaOptions as $areaCode=>$areaCaption) {
            if($showCode) {
                $areaCaption = '[' . $areaCode . ']' . $areaCaption;
            }
            $html .= '<option value="' . $areaCode . '"';
            if(in_array($areaCode, $areas)) {
                $html .= ' selected="true"';
            }
            $html .= '>' . $areaCaption . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
?>