<?php

if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly

if (current_user_can('administrator') && isset($_POST['save'])) {
	$un_theme1 = isset($_POST['un_theme1']) ? (esc_html($_POST['un_theme1'])) : ("");
	update_option('un_theme1', $un_theme1);

}

?>

<br />
<div class="row">
   <div class="col-md-11">
      <div class="panel panel-primary">
         <!-- Default panel contents -->
         <div class="panel-heading"><h4>Construction Design</h4></div>
         <div class="panel-body">
            <form method="POST" name="myform" id="post">
                <div class="row">
                  <div class="col-md-12">

                     <?php $un_theme1 = get_option('un_theme1');
$un_theme1s = array('textarea_name' => 'un_theme1', 'editor_class' => 'txtDropTarget ui-droppable', 'drag_drop_upload' => true, 'textarea_rows' => 5);
wp_editor(html_entity_decode(stripcslashes($un_theme1)), 'un_theme1', $un_theme1s);
?>
                  </div>
               </div>
               <br/>
               <input type="submit" class="btn btn-success" name="save" value="Save Changes">
            </form>
         </div>
         <!-- Table -->
      </div>
   </div>
</div>

