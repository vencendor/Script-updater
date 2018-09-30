<?

$MOD_NAME="CAT_DESCR";

$MOD_FILES[0]['source']="seo.php";
$MOD_FILES[0]['destin']="seo.php";

$MOD_CHANGE[0]['file']="index.php";
$MOD_CHANGE[0]['replace']='$tpl->set ( \'{AJAX}\', $ajax );';
$MOD_CHANGE[0]['string']='if(!isset($_GET[\'newsid\']) and !isset($_GET[\'cstart\']) ) {
	if(isset($category_id) and $category_id>0)
	$CATEGORY_INFO=$CATEGORY_DESCR[$category_id];
	elseif($_SERVER[\'REQUEST_URI\']==="/") 
	$CATEGORY_INFO=$CATEGORY_DESCR["index"];
	else 
	$CATEGORY_INFO="";
	
	if($CATEGORY_INFO!=="") $CATEGORY_INFO="<div class=\'catDescr\'>$CATEGORY_INFO</div>";
}
  
$tpl->set ( \'{CATEGORY_INFO}\', $CATEGORY_INFO );
$tpl->set ( \'{AJAX}\', $ajax );';


$MOD_CHANGE[1]['file']="index.php";
$MOD_CHANGE[1]['replace']='require_once ROOT_DIR.\'/engine/init.php\';';
$MOD_CHANGE[1]['string']='require_once ROOT_DIR.\'/seo.php\';
require_once ROOT_DIR.\'/engine/init.php\';';

?>