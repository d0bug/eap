<?php
abstract class UserCommAction extends AppCommAction {
    protected function notNeedLogin() {
        return array();
    }
}
?>