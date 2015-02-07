<?php
$url3 = _url(3);
$divId = 'eav'.time();
$startingPage = 1;
$pageRecords = 5;
$defaultColumnToSort = 1;
$defaultSortType = 'ASC';
if(($url3 == 'add' || $url3 == 'new' || $url3 == 'save')){
    $action = array("save", "Save");
}elseif($url3 == 'update'){
    $action = array("update", "Modify");
}else{
    //  Action / DOM #ID / Current Page / Page Records / Default Field to sort / Sorting type
    $action = array("view", $divId, $startingPage, $pageRecords, $defaultColumnToSort, $defaultSortType, '');
}