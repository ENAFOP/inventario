<?php
/**
 * Implementation of RemoveEvent view
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
 * Class which outputs the html page for RemoveEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveEvent extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$event = $this->params['event'];

		$this->htmlStartPage(getMLText("delete"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

?>
<div class="gap-15"></div>
<div class="row">
<div class="col-md-12">
<?php $this->startBoxDanger(getMLText("delete")); ?>

<form action="../op/op.RemoveEvent.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('removeevent'); ?>
	<input type="hidden" name="eventid" value="<?php echo intval($event["id"]); ?>">
	<p><?php printMLText("confirm_rm_event", array ("name" => htmlspecialchars($event["name"])));?></p>
	<button class="btn btn-danger" type="submit"><i class="fa fa-times"></i> <?php printMLText("delete");?></button>
</form>
<?php $this->endsBoxSuccess(); ?>
</div>
</div>
<?php
		
		echo "</div>";

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
