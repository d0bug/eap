<?php
class GradeYearModel {
    const GRADE_PRIMARY = 1;
    const GRADE_MIDDLE = 2;
    const GRADE_HIGH = 4;
    const GRADE_ALL = 7;
    
    
    public function getGradeYears($type = 7) {
        $gradeCaptions = array('幼儿园', '小学一年级', '小学二年级', '小学三年级', '小学四年级', '小学五年级', '小学六年级', '初中一年级', '初中二年级', '初中三年级', '高中一年级', '高中二年级', '高中三年级');
        $year = date('Y');
        $year += 1 ;
        $curMonth = date('n');
        if($curMonth < 9) {
            $year -= 1;
        }
        $gradeArray = array();
        foreach($gradeCaptions as $grade) {
            $gradeArray[$year] = $grade;
            $year -- ;
        }
        if($type == self::GRADE_ALL) {
            return $gradeArray;
        }
        $gradeYears = array();
        $i = 0;
        foreach ($gradeArray as $year=>$grade) {
            if($i <=6 && $type & self::GRADE_PRIMARY) {
                $gradeYears[$year] = $grade;
            } else if($i > 6 && $i <=9 && $type & self::GRADE_MIDDLE ) {
                $gradeYears[$year] = $grade;
            } else if($i >9 && $type & self::GRADE_HIGH) {
                $gradeYears[$year] = $grade;
            }
        }
        return $gradeYears;
    }
}
?>