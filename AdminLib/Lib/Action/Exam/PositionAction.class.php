<?php
class PositionAction extends ExamCommAction{
    public function main() {
        $permValue = $this->permValue;
        $jsonPosUrl = $this->getUrl('jsonPosList');
        $addPosUrl = $this->getUrl('addPosition');
        $posInfoUrl = $this->getUrl('posInfo');
        $delPosUrl = $this->getUrl('delPosition');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function jsonPosList() {
        $this->readCheck($this->getAclKey('main'));
        $posModel = D('Position');
        $posCount = $posModel->getPosCount();
        $posList = $posModel->getPosList(false, abs($_POST['page']), abs($_POST['rows']));
        echo json_encode(array('total'=>$posCount, 'rows'=>$posList));
    }
    
    protected function addPosition() {
        $this->writeCheck($this->getAclKey('main'));
        $url = $_SERVER['REQUEST_URI'];
        $areaArray = C('AREA_ARRAY');
        $areaArray = array_combine($areaArray, $areaArray);
        if($this->isPost()) {
            $resultScript = true;
            $posModel = D('Position');
            $posInfo = $_POST;
            $saveResult = $posModel->save($posInfo);
            $errorMsg = $saveResult['errorMsg'];
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function posInfo() {
        $this->readCheck($this->getAclKey('main'));
        $posCode = SysUtil::safeString($_GET['pos']);
        $posModel = D('Position');
        $url = $_SERVER['REQUEST_URI'];
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('main'));
        $permValue = $permInfo['permValue'];
        if($this->isPost()) {
            $resultScript = true;
            $this->writeCheck($this->getAclKey('main'));
            $posInfo = $_POST;
            $saveResult = $posModel->save($posInfo);
            $errorMsg = $saveResult['errorMsg'];
        } else {
            $posInfo = $posModel->find($posCode);
        }
        $areaArray = C('AREA_ARRAY');
        $areaArray = array_combine($areaArray, $areaArray);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function delPosition() {
        $posCode = SysUtil::safeString($_POST['pos']);
        $this->writeCheck($this->getAclKey('main'));
        $posModel = D('Position');
        $posModel->delete($posCode);
        echo 1;
    }
}
?>
