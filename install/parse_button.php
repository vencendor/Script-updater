<?

# Добавление кнопки парсера в админку DLE 

$MOD_NAME = "PARSE_BUTTON";

$MOD_FILES[0]['source']="files/parse_video.php";
$MOD_FILES[0]['destin']="engine/modules/parse_video.php";
$MOD_FILES[1]['source']="files/video.gif";
$MOD_FILES[1]['destin']="engine/skins/bbcodes/images/video.gif";

$MOD['file']="engine/inc/inserttag.php";
$MOD['replace']='<div id="b_color" class="editor_button" onclick="ins_color( );" ><img src="engine/skins/bbcodes/images/color.gif" width="23" height="25" border="0"></div>';
$MOD['string']='<div id="b_color" class="editor_button" onclick="ins_color(this);"><img src="engine/skins/bbcodes/images/color.gif" width="23" height="25" border="0"></div>
<div id="b_color" class="editor_button" onclick="ins_video_parse();"><img src="engine/skins/bbcodes/images/video.gif" width="23" height="25" border="0"></div>';
$MOD_CHANGE[]=$MOD; unset($MOD) ; 


$MOD['file']="engine/inc/inserttag.php";
$MOD['replace']='function ins_color(  )
{';
$MOD['string']='function ins_video_parse(){
	dt=document.getElementById(\'parser_div\');
	dt.style.display=\'block\';
	dt.style.left=(document.width-parseInt(dt.style.width))/2;
	dt.innerHTML=\'<div style="text-align:right"><a href="javascript:void(0);" onclick="ins_video_close()" > Закрыть</a></div>\';
	dt.innerHTML+=\'<iframe src="engine/modules/parse_video.php" width="640px" height="480px" style="border:none;"></iframe>\';
}

function ins_video_close(){
	dt=document.getElementById(\'parser_div\');
	dt.style.display=\'none\';
}

function ins_color(  )
{';
$MOD_CHANGE[]=$MOD; unset($MOD) ; 

/*
$MOD['file']="engine/inc/addnews.php";
$MOD['replace']='	echo " <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";';
$MOD['string']='echo "
	<div id=\'parser_div\' style=\'position:absolute;border:solid 4px; width:650px; height:500px; margin: 200px auto; display:none; background:#FFFFFF\'></div>
    <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";';
$MOD_CHANGE[]=$MOD; unset($MOD) ; 
/**/

/*
$MOD['file']="engine/inc/editnews.php";
$MOD['replace']='	echo "
    <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";';
$MOD['string']='echo "
	<div id=\'parser_div\' style=\'position:absolute;border:solid 4px; width:650px; height:500px; margin: 200px auto; display:none; background:#FFFFFF\'></div>
    <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";';
$MOD_CHANGE[]=$MOD; unset($MOD) ; 

/**/

?>