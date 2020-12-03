<?php
/**
Plugin Name: Front end post
Description: create post from front end
Author: Vilas Bondre
Version: 1.0
**/
function add_custom_post(){
	

$categories = get_categories();
//print_r($categories);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<form method="post" name="postform" id="postform" enctype="multipart/form-data" >
<input type="hidden" name="action" value="submitform">
<input type="hidden" name="site_url" value="<?php echo home_url();?>" id="site_url">
<div>Title: <br>
	<input type="text" name="title">
</div>
<div>Content: <br>
	<textarea name="content"></textarea>
</div>
<div>Image:<br>
	<input type="file" name="image" id="image">
</div>
<div>Date: (DD/MM/YYYY)<br>
	<select name="day">
		<option value="0">Select Date</option>
		<?php for($i=1;$i <=31;$i++){ ?>
		<option value="<?php echo $i;?>" <?php if($i==date('d')){echo "selected";} ?>><?php echo $i;?></option>
		<?php }?>
	</select>
	<select name="month">
		<option value="0">Select Month</option>
		<?php for($i=1;$i <=12;$i++){ ?>
		<option value="<?php echo $i;?>" <?php if($i==date('m')){echo "selected";} ?>><?php echo $i;?></option>
		<?php }?>
	</select>
	<select name="year">
		<option value="0">Select Year</option>
		<?php for($i=date('Y')-5;$i <=date('Y')+5;$i++){ ?>
		<option value="<?php echo $i;?>" <?php if($i==date('Y')){echo "selected";} ?>><?php echo $i;?></option>
		<?php }?>
	</select>
</div>
<div>
Category:<br>
	<select name="category">
		<option value="0">Please category</option>
		<?php foreach($categories as $category){ ?>
		<option value="<?php echo $category->term_id;?>"><?php echo $category->name;?></option>
		<?php }?>
	</select>
</div>
<div><br>
<input type="button" value="Submit" name="submit" id="button">
</div>
</form>
<script>
//jQuery(document).ready(function(){
jQuery("#button").click(function(){ alert("hello");
var site_url =jQuery("#site_url").val();
var dataString = jQuery('#postform').serialize();
	jQuery.ajax({
        url: site_url+"/wp-admin/admin-ajax.php",
		data:dataString,
        type: 'POST',
        success: function(data){
            //alert(data);
		}
    });
		
    });  
//})


</script>
<?php
}
add_shortcode('frontendcustompost','add_custom_post');

function submitform(){
	global $wpdb;
	print_r($_REQUEST);
	//print_r($_FILES);
	
		$post = $wpdb->prefix."posts";
		$title = $_REQUEST['title'];
		$content = $_REQUEST['content'];
		 $date = $_REQUEST['year']."-".$_REQUEST['month']."-".$_REQUEST['day'];
		$category = $_POST['category']; 
    $data=array( 'post_title' => $title, 'post_content' => $content,'post_date'=>$date,'post_category' => array($category));

echo $post_id = wp_insert_post( $data );
     
		wp_set_post_categories( $post_id, array( $category ) );
		
		print_r($_FILES);
		$file = $_FILES["image"]["name"];
		$filename = basename($file);
		$upload_file = wp_upload_bits($filename, null, file_get_contents($file));
		if (!$upload_file['error']) {
		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_parent' => $post_id,
			'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$upload_file = wp_upload_bits($filename, null, @file_get_contents($_FILES["image"]["tmp_name"]));
				$attachment_id = wp_insert_attachment( $attachment, $upload_file['image'], $post_id );
		}
	
	exit;
}
add_action('wp_ajax_submitform','submitform');
add_action('wp_ajax_nopriv_submitform','submitform');
?>