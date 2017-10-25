<?php
class BdMapWidget extends Widget {
    public function render($data){
        static $mapLoaded = false;
        $mapKey = C('BAIDU_MAP_KEY');
        $script = '';
        $divId = $data['div'];
        $mapName = 'bdMaps.' . $divId;
        $position = $data['position'] ? $data['position'] : '116.404,39.915';
        $position = str_replace(' ', '', $position);
        $posField = $data['posField'];
        $addListener = $data['addListener'];
        $enableMark = true == $data['enableMark'];
        if(false == preg_match('/,/', $position)) $position = '116.404,39.915';
        list($mapLng, $mapLat) = explode(',', $position);
        if(false == $mapLoaded) {
            $mapLoaded = true;
            $initMaps = '
            if(undefined === bdMaps) {
                var bdMaps = {timer:null};
            }';
        }
        $markerScript = '';
        if($addListener) {
        $markerScript = $mapName . '.addEventListener("click",function(e){
                        if(enableMark) {
                            var marker = new BMap.Marker(new BMap.Point(e.point.lng, e.point.lat));
                            jQuery("#' . $posField . '").val(e.point.lng + "," + e.point.lat);
                            ' . $mapName . '.clearOverlays();
                            ' . $mapName . '.addOverlay(marker);
                        }
                    });';
        }
        $script .= '<script type="text/javascript">
            ' . $initMaps . '
            jQuery(function(){
                enableMark = ' . var_export($enableMark,true) . ';
                ' . $mapName .  ' = new BMap.Map("' . $divId . '");
                ' . $mapName . '.addControl(new BMap.NavigationControl());
                ' . $mapName . '.centerAndZoom(new BMap.Point(' . $mapLng . ', ' . $mapLat . '), 13);
                var marker = new BMap.Marker(new BMap.Point(' . $mapLng . ', ' . $mapLat . '));
                ' . $mapName . '.addOverlay(marker);
                ' . $markerScript . '
            })
            
        </script>';
        return $script;
    }
}
?>