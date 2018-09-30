<?

$MOD_NAME="count";

$MOD_CHANGE[0]['file']="engine/inc/addnews.php";
$MOD_CHANGE[0]['replace']='<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:60%;" ></textarea>';
$MOD_CHANGE[0]['string']='<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:60%;" onkeypress="cont_letter()" onchange="cont_letter()"></textarea>
	<input type="text" id="letter_num" style="width:50px;" value="$count_text" />';


$MOD_CHANGE[1]['file']="engine/inc/addnews.php";
$MOD_CHANGE[1]['replace']='function auto_keywords ( key )	{';
$MOD_CHANGE[1]['string']='function cont_letter() {
	tx=document.getElementById(\'full_story\');
	lc=document.getElementById(\'letter_num\');
	var reg=/[^a-zà-ÿ]/ig;
	var temp_str=tx.value.replace(reg, \"\");
	lc.value=temp_str.length;
  }
	
	function auto_keywords ( key )
	{';
	
$MOD_CHANGE[2]['file']="engine/inc/editnews.php";
$MOD_CHANGE[2]['replace']='<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:60%;" ></textarea>';
$MOD_CHANGE[2]['string']='<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:60%;" onkeypress="cont_letter()" onchange="cont_letter()"></textarea>
	<input type="text" id="letter_num" style="width:50px;" value="$count_text" />';


$MOD_CHANGE[3]['file']="engine/inc/editnews.php";
$MOD_CHANGE[3]['replace']='function auto_keywords ( key )	{';
$MOD_CHANGE[3]['string']='function cont_letter() {
	tx=document.getElementById(\'full_story\');
	lc=document.getElementById(\'letter_num\');
	var reg=/[^a-zà-ÿ]/ig;
	var temp_str=tx.value.replace(reg, \"\");
	lc.value=temp_str.length;
  }
	
	function auto_keywords ( key )
	{';

?>