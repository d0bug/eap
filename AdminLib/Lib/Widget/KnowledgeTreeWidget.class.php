<?php
class KnowledgeTreeWidget extends Widget {
    public function render($data) {
        $subject = $data['subject'];
        $module = $data['module'];
        $attr = $data['attr'];
        $knowledgeModel = D('Knowledge');
        $value = $data['value'];
        if(false == is_array($value)) $value = array($value);
        $dao = $knowledgeModel->dao;
        $condition = '1=1';
        if($module) {
            $condition .= ' AND knowledge.module_code=' . $dao->quote($module);
        } else if($subject) {
            $condition .= ' AND module_subject=' . $dao->quote($subject);
        }
        $knowledgeList = $knowledgeModel->getList($condition);
        foreach ($knowledgeList as $key=>$knowledge) {
            $knowledgeList[$key]['code_caption'] = '[' . $knowledge['knowledge_code'] . ']' . $knowledge['knowledge_caption'];
        }
        $knowledgeTree = SysUtil::buildTree($knowledgeList, 'knowledge_code', 'parent_code', '0');
        $knowledgeOptions = SysUtil::treeOptions($knowledgeTree, 'knowledge_code', 'code_caption');
        
        $options = '';
        #└├│
        $extends = array();
        foreach ($knowledgeOptions as $option) {
            $icon = $option['last'] ? '└' : '├';
            $extends[$option['deep']] = $icon == '└' ? '　' : '│';
            $prefix = '';
            for ($i=0;$i<$option['deep'];$i++) {
            	$prefix .= $extends[$i];
            }
            $prefix .= $icon;
            $options .= '<option value="' . $option['value'] . '"';
            if($option['hasChild']) {
                #$options .= ' disabled="true"';
            }
            if($option['deep'] == 0) {
                $options .= ' style="font-weight:bold;background:#ddd"';
            }
            if(in_array($option['value'], $value)) {
                $options .= ' selected="true"';
            }
            $options .= '>' . $prefix . $option['caption'] . '</option>';
        }
        return  '<select ' . $attr . '><option value="0">==知=识=点=选=择==</option>' . $options . '</select>';
    }
}
?>