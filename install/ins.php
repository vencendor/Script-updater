<?

$MOD_NAME="REDACTOR_EXER";


$MOD['file']="engine/inc/addnews.php";
$MOD['replace']='<textarea rows="16" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:98%;"></textarea>';
$MOD['string']='<table width="100%"><tr> <td style="width:37%;"> Форма для кода плеера и короткого описания</td><td style="width:60%;"> Форма для описания </td> </tr></table><textarea rows="19" onclick="setFieldName(this.name)" name="full_story_obj" id="full_story_obj" style="width:37%;" ></textarea>
	<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:60%;" ></textarea>';
$MOD_CHANGE[]=$MOD; unset($MOD) ; 


$MOD['file']="engine/inc/addnews.php";
$MOD['replace']='onsubmit=\"if ( document.addnews.title.value == \'\' || document.addnews.short_story.value == \'\' ) { alert(\'$lang[addnews_alert]\'); return false}\" ';
$MOD['string']='onsubmit=\"if(document.addnews.title.value == \'\' || document.addnews.short_story.value == \'\' ){alert(\'$lang[addnews_alert]\', \'$lang[p_info]\');return false} else if( document.addnews.full_story.value.length < 300 ) { alert(\'Слишком короткое описание, минимум 300 символов\', \'$lang[p_info]\');return false} else {  if (document.addnews.full_story_obj.value.indexOf(\'<object\') == -1) {alert(\'Не введен код для видео\', \'$lang[p_info]\');return false} } \"';
$MOD_CHANGE[]=$MOD;  unset($MOD) ;

/*
$MOD['file']="engine/inc/addnews.php";
$MOD['begin']="\$_POST['short_story'] = strip_tags (\$_POST['short_story']);";
$MOD['end']="\$_POST['full_story'] = strip_tags (\$_POST['full_story']);";
$MOD['string']="\$_POST['full_story_obj'] = strip_tags (\$_POST['full_story_obj']);";
$MOD_CHANGE[]=$MOD; unset($MOD) ; 
*/

$MOD['file']="engine/inc/addnews.php";
$MOD['begin']="\$short_story = \$parse->process( \$_POST['short_story'] );";
$MOD['end']="\$full_story = \$parse->process( \$_POST['full_story'] );";
$MOD['string']="\$full_story_obj = \$parse->process( \$_POST['full_story_obj'] );
\$full_story=\$full_story_obj.\"<br/><!-- metka -->\".\$full_story;";
$MOD_CHANGE[]=$MOD; unset($MOD) ;

/**/

// Установка для editnews.php 

$MOD['file']="engine/inc/editnews.php";
$MOD['replace']='if ( $config[\'allow_admin_wysiwyg\'] == "yes" ) {
		include (ENGINE_DIR . \'/editor/fullnews.php\');';
$MOD['string']='	// Разделение fullstory на obj часть и описаание 
	$pos_metka=strpos($row[\'full_story\'],"&lt;!-- metka --&gt;");
	if($pos_metka!==false) {
		$row[\'full_story_obj\']=substr($row[\'full_story\'],0,$pos_metka);
		$row[\'full_story\']=substr($row[\'full_story\'],$pos_metka+strlen("&lt;!-- metka --&gt;"));
	}
	if( $config[\'allow_admin_wysiwyg\'] == "yes" ) {
		
		include (ENGINE_DIR . \'/editor/fullnews.php\');
	';
$MOD_CHANGE[]=$MOD; unset($MOD) ;

$MOD['file']="engine/inc/editnews.php";
$MOD['replace']='<textarea rows="16" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:98%;">{$row[\'full_story\']}</textarea>';
$MOD['string']='<table width="100%"><tr> <td style="width:37%;"> Форма для кода плеера и короткого описания</td><td style="width:60%;"> Форма для описания </td> </tr></table><textarea rows="19" onclick="setFieldName(this.name)" name="full_story_obj" id="full_story_obj" style="width:37%;" >{$row[\'full_story_obj\']}</textarea>
	<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:60%;" >{$row[\'full_story\']}</textarea>';
$MOD_CHANGE[]=$MOD; unset($MOD) ;



$MOD['file']="engine/inc/editnews.php";
$MOD['replace']='onsubmit=\"if(document.addnews.title.value == \'\' || document.addnews.short_story.value == \'\'){alert(\'$lang[addnews_alert]\');return false}\"';
$MOD['string']='onsubmit=\"if(document.addnews.title.value == \'\' || document.addnews.short_story.value == \'\' ){alert(\'$lang[addnews_alert]\', \'$lang[p_info]\');return false} else if( document.addnews.full_story.value.length < 300 ) { alert(\'Слишком короткое описание, минимум 300 символов\', \'$lang[p_info]\');return false} else {  if (document.addnews.full_story_obj.value.indexOf(\'<object\') == -1) {alert(\'Не введен код для видео\', \'$lang[p_info]\');return false} } \"';
$MOD_CHANGE[]=$MOD; unset($MOD) ;

/*
$MOD['file']="engine/inc/editnews.php";
$MOD['begin']="\$_POST['short_story'] = strip_tags (\$_POST['short_story']);";
$MOD['end']="\$_POST['full_story'] = strip_tags (\$_POST['full_story']);";
$MOD['string']="\$_POST['full_story_obj'] = strip_tags (\$_POST['full_story_obj']);";
$MOD_CHANGE[]=$MOD; unset($MOD) ;
*/

$MOD['file']="engine/inc/editnews.php";
$MOD['begin']="\$short_story = \$parse->process( \$_POST['short_story'] );";
$MOD['end']="\$full_story = \$parse->process( \$_POST['full_story'] );";
$MOD['string']="\$full_story_obj = \$parse->process( \$_POST['full_story_obj'] );
\$full_story=\$full_story_obj.\"<br/><!-- metka -->\".\$full_story;";
$MOD_CHANGE[]=$MOD; unset($MOD) ;

/**/

?>