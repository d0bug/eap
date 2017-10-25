<?php
require_once(dirname(__FILE__) . '/config.inc.php');
return array(
    'HW_DEPT_ARRAY' => array(
        'math'=>array('tkDeptId'=>3, 'tkKwId'=>1, 'deptCode'=>'DPBJ001', 'deptName'=>'小学数学', 'nxuebu'=>1, 'nxueke'=>1),
        'penglish'=>array('tkDeptId'=>6, 'tkKwId'=>9, 'deptCode'=>'DPBJ003', 'deptName'=>'小学英语', 'nxuebu'=>1, 'nxueke'=>3),
        'menglish'=>array('tkDeptId'=>6, 'tkKwId'=>9, 'deptCode'=>'DPBJ027', 'deptName'=>'初中英语', 'nxuebu'=>2, 'nxueke'=>3),
        'henglish'=>array('tkDeptId'=>6, 'tkKwId'=>10, 'deptCode'=>'DPBJ038', 'deptName'=>'高中英语', 'nxuebu'=>3, 'nxueke'=>3)
    ),

    'HW_EN_CLASSTYPES'=>array(
        'math'=>array(),
        'english'=>array(),
    ),

    'HW_DEPT_USERS'=>array(
        'penglish'=>$pEnglishUsers,
        'menglish'=>$mEnglishUsers,
        'henglish'=>$hEnglishUsers,
        'both'=>$bothUsers,
    ),

);