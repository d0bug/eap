<?php
abstract class StudentCommAction extends AppCommAction {
    protected function notNeedLogin() {
        return array();
    }
}
?>