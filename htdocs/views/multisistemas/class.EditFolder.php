<?php
/**
 * Implementation of EditFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for EditFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditFolder extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
		header('Content-Type: application/javascript; charset=UTF-8');
?>
function checkForm()
{
	msg = new Array();
	if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
<?php
	if ($strictformcheck) {
?>
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
<?php
	}
?>
	if (msg != "") {
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
  	});
		return false;
	}
	else
		return true;
}
$(document).ready(function() {
/*
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
*/
	$("#form1").validate({
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>"
		},
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$attrdefs = $this->params['attrdefs'];
		$rootfolderid = $this->params['rootfolderid'];
		$strictformcheck = $this->params['strictformcheck'];
		$orderby = $this->params['orderby'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true), "view_folder", $folder);
		$this->contentHeading(getMLText("edit_folder_props"));
		$this->contentContainerStart();
?>
<form class="form-horizontal" action="../op/op.EditFolder.php" id="form1" name="form1" method="post">
		<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
		<input type="hidden" name="showtree" value="<?php echo showtree();?>">
		<div class="control-group">
			<label class="control-label"><?php printMLText("name");?>:</label>
			<div class="controls">
				<input type="text" name="name" value="<?php print htmlspecialchars($folder->getName());?>" size="60" required>
			</div>
		</div>	
		<div class="control-group">
			<label class="control-label"><?php printMLText("comment");?>:</label>
			<div class="controls">
				<textarea name="comment" rows="4" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>><?php print htmlspecialchars($folder->getComment());?></textarea>
			</div>
		</div>
<?php
		$parent = ($folder->getID() == $rootfolderid) ? false : $folder->getParent();
		if ($parent && $parent->getAccessMode($user) > M_READ) {
			print "<div class=\"control-group\">";
			print "<label class=\"control-label\">" . getMLText("sequence") . ":</label>";
			print "<div class=\"controls\">";
			$this->printSequenceChooser($parent->getSubFolders('s'), $folder->getID());
			if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off'); 
			print "</div></div>\n";
		}

		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
				$arr = $this->callHook('folderEditAttribute', $folder, $attrdef);
				if(is_array($arr)) {
					echo $txt;
					echo "<div class=\"control-group\">";
					echo "<label class=\"control-label\">".$arr[0]."</label>";
					echo "<div class=\"controls\">".$arr[1]."</div>";
					echo "</div>";
				} else {
?>
			<div class="control-group">
				<label class="control-label"><?php echo htmlspecialchars($attrdef->getName()); ?>:</label>
				<div class="controls">
					<?php $this->printAttributeEditField($attrdef, $folder->getAttribute($attrdef)) ?>
				</div>
			</div>
<?php
				}
			}
		}
?>
			<div class="controls">
				<button type="submit" class="btn btn-success"><i class="icon-save"></i> <?php printMLText("save"); ?></button>
			</div>
</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
