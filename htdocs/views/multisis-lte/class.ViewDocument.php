<?php
/**
 * Implementation of ViewDocument view
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
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

/**
 * Class which outputs the html page for ViewDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ViewDocument extends SeedDMS_Bootstrap_Style 
{
	
	protected function getAccessModeText($defMode) { /* {{{ */
		switch($defMode) {
			case M_NONE:
				return getMLText("access_mode_none");
				break;
			case M_READ:
				return getMLText("access_mode_read");
				break;
			case M_READWRITE:
				return getMLText("access_mode_readwrite");
				break;
			case M_ALL:
				return getMLText("access_mode_all");
				break;
		}
	} /* }}} */

	protected function printAccessList($obj) { /* {{{ */
		$accessList = $obj->getAccessList();
		if (count($accessList["users"]) == 0 && count($accessList["groups"]) == 0)
			return;

		$content = '';
		for ($i = 0; $i < count($accessList["groups"]); $i++)
		{
			$group = $accessList["groups"][$i]->getGroup();
			$accesstext = $this->getAccessModeText($accessList["groups"][$i]->getMode());
			$content .= $accesstext.": ".htmlspecialchars($group->getName());
			if ($i+1 < count($accessList["groups"]) || count($accessList["users"]) > 0)
				$content .= "<br />";
		}
		for ($i = 0; $i < count($accessList["users"]); $i++)
		{
			$user = $accessList["users"][$i]->getUser();
			$accesstext = $this->getAccessModeText($accessList["users"][$i]->getMode());
			$content .= $accesstext.": ".htmlspecialchars($user->getFullName());
			if ($i+1 < count($accessList["users"]))
				$content .= "<br />";
		}

		if(count($accessList["groups"]) + count($accessList["users"]) > 3) {
			$this->printPopupBox(getMLText('list_access_rights'), $content);
		} else {
			echo $content;
		}
	} /* }}} */

	/**
	 * Output a single attribute in the document info section
	 *
	 * @param object $attribute attribute
	 */
		protected function printAttribute($attribute) { /* {{{ */
		$attrdef = $attribute->getAttributeDefinition();
?>
		    <tr>
					<td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
					<td>
<?php
		switch($attrdef->getType()) {
		case SeedDMS_Core_AttributeDefinition::type_url:
			$attrs = $attribute->getValueAsArray();
			$tmp = array();
			foreach($attrs as $attr) {
				$tmp[] = '<a href="'.htmlspecialchars($attr).'">'.htmlspecialchars($attr).'</a>';
			}
			echo implode('<br />', $tmp);
			break;
		case SeedDMS_Core_AttributeDefinition::type_email:
			$attrs = $attribute->getValueAsArray();
			$tmp = array();
			foreach($attrs as $attr) {
				$tmp[] = '<a mailto="'.htmlspecialchars($attr).'">'.htmlspecialchars($attr).'</a>';
			}
			echo implode('<br />', $tmp);
			break;
		default:
		$impri=implode(', ', $attribute->getValueAsArray());
			$limpia=str_replace("\xC2\x93", "'", $impri);
						$limpia=str_replace("\xC2\x94", "'", $limpia);
						$limpia=str_replace("\xC2\x95", "'", $limpia);
						$limpia=str_replace("\xC2\x96", "-", $limpia);
						$limpia=str_replace("\xC2\x85", "...", $limpia);
			echo htmlspecialchars($limpia);
		}
?>
					</td>
		    </tr>
<?php
	} /* }}} */

	function timelinedata() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$document = $this->params['document'];

		$jsondata = array();
		if($user->isAdmin()) {
			$data = $document->getTimeline();

			foreach($data as $i=>$item) {
				switch($item['type']) {
				case 'add_version':
					$msg = getMLText('timeline_'.$item['type'], array('document'=>htmlspecialchars($item['document']->getName()), 'version'=> $item['version']));
					break;
				case 'add_file':
					$msg = getMLText('timeline_'.$item['type'], array('document'=>htmlspecialchars($item['document']->getName())));
					break;
				case 'status_change':
					$msg = getMLText('timeline_'.$item['type'], array('document'=>htmlspecialchars($item['document']->getName()), 'version'=> $item['version'], 'status'=> getOverallStatusText($item['status'])));
					break;
				default:
					$msg = '???';
				}
				$data[$i]['msg'] = $msg;
			}

			foreach($data as $item) {
				if($item['type'] == 'status_change')
					$classname = $item['type']."_".$item['status'];
				else
					$classname = $item['type'];
				$d = makeTsFromLongDate($item['date']);
				$jsondata[] = array('start'=>date('c', $d)/*$item['date']*/, 'content'=>$item['msg'], 'className'=>$classname);
			}
		}
		header('Content-Type: application/json');
		echo json_encode($jsondata);
	} /* }}} */

	function js() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$document = $this->params['document'];
		$folder = $this->params['folder'];

		header('Content-Type: application/javascript');
		if($user->isAdmin()) {
			$this->printTimelineJs('out.ViewDocument.php?action=timelinedata&documentid='.$document->getID(), 300, '', date('Y-m-d'));
		}
		$this->printDocumentChooserJs("form1");
	
		?>
			$(document).on("ready", function(){
				$("#document-info-widget").on("click", function(){
					$("#document-info").addClass("div-hidden");
					$("#tab-information").removeClass("col-md-8").addClass("col-md-12");
				});

				/* ---- For document previews ---- */

			  $(".preview-doc-btn").on("click", function(){
			  	$("#thedocinfo").hide();
					$("#thetimeline").hide();

			  	var docID = $(this).attr("id");
			  	var version = $(this).attr("rel");
			  	$("#doc-title").text($(this).attr("title"));
			  	$("#document-previewer").show('slow');
			  	$("#iframe-charger").attr("src","../pdfviewer/web/viewer.html?file=..%2F..%2Fop%2Fop.Download.php%3Fdocumentid%3D"+docID+"%26version%3D"+version);
			  });

			  $(".preview-attach-btn").on("click", function(){
			  	$("#thedocinfo").hide();
					$("#thetimeline").hide();

			  	var docID = $(this).attr("id");
			  	var file = $(this).attr("rel");
			  	$("#doc-title").text($(this).attr("title"));
			  	$("#document-previewer").show('slow');
			  	$("#iframe-charger").attr("src","../pdfviewer/web/viewer.html?file=..%2F..%2Fop%2Fop.Download.php%3Fdocumentid%3D"+docID+"%26file%3D"+file);
			  });

			  $(".close-doc-preview").on("click", function(){
			  	$("#document-previewer").hide();
			  	$("#iframe-charger").attr("src","");
			  	$("#thedocinfo").show('slow');
			  	$("#thetimeline").show('slow');
			  });

			});


		<?php

	} /* }}} */


	/**
	 * Show document information
	 */

	function documentInfos() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$document = $this->params['document'];

		?>
		<?php
		$txt = $this->callHook('preDocumentInfos', $document);
		if(is_string($txt))
			echo $txt;
		$txt = $this->callHook('documentInfos', $document);
		if(is_string($txt))
			echo $txt;
		else {
?>
		<table class="table table-striped">
<?php
		/*if($user->isAdmin()) 
		{
			echo "<tr>";
			echo "<td><strong>".getMLText("id").":</strong></td>\n";
			echo "<td>".htmlspecialchars($document->getID())."</td>\n";
			echo "</tr>";
		}*/
?>
		<tr>
		<td><strong><?php printMLText("referencia_resolucion");?>:</strong></td>
		<td><?php print htmlspecialchars($document->getName());?></td>
		</tr>
<?php
		
		if($user->isAdmin()) 
		{
		echo "<tr>";

		echo "<td><strong>";
		printMLText("fecha_subida_sistema");
		echo ":</strong></td>";
		echo "<td>".getLongReadableDate($document->getDate())."</td>";
		echo "</tr>";
		}
?>

		<!-- <tr>
		<td><strong><?php printMLText("tamano_archivo");?>:</strong></td>
		<td><?php print SeedDMS_Core_File::format_filesize($document->getUsedDiskSpace());?></td>
		</tr> -->

		
<?php
		if($document->expires()) {
?>
		<tr>
		<td><strong><?php printMLText("expires");?>:</strong></td>
		<td><?php print getReadableDate($document->getExpires()); ?></td>
		</tr>
<?php
		}
		if($document->getKeywords()) {
?>
		<tr>
		<td><strong><?php printMLText("keywords");?>:</strong></td>
		<td><?php print htmlspecialchars($document->getKeywords());?></td>
		</tr>
<?php
		}
		if($cats = $document->getCategories()) {
?>
		<tr>
		<td><strong><?php printMLText("categories");?>:</strong></td>
		<td>
		<?php
			$ct = array();
			foreach($cats as $cat)
				$ct[] = htmlspecialchars($cat->getName());
			echo implode(', ', $ct);
		?>
		</td>
		</tr>
<?php
		}
?>
		<?php
		$attributes = $document->getAttributes();
		if($attributes) {
			foreach($attributes as $attribute) {
				$arr = $this->callHook('showDocumentAttribute', $document, $attribute);
				if(is_array($arr)) {
					echo "<tr>";
					echo "<td><strong>".$arr[0].":</strong></td>";
					echo "<td>".$arr[1]."</td>";
					echo "</tr>";
				} else {
					$this->printAttribute($attribute);
				}
			}
		}
?>
		</table>
<?php
		}
		$txt = $this->callHook('postDocumentInfos', $document);
		if(is_string($txt))
			echo $txt;
		//$this->contentContainerEnd();
	} /* }}} */

	function preview() { /* {{{ */
		$dms = $this->params['dms'];
		$document = $this->params['document'];
		$timeout = $this->params['timeout'];
		$showfullpreview = $this->params['showFullPreview'];
		$converttopdf = $this->params['convertToPdf'];
		$cachedir = $this->params['cachedir'];

		if(!$showfullpreview)
			return;

		$latestContent = $document->getLatestContent();
		$txt = $this->callHook('preDocumentPreview', $latestContent);
		if(is_string($txt))
			echo $txt;
		else {
			switch($latestContent->getMimeType()) {
			case 'audio/mpeg':
			case 'audio/mp3':
			case 'audio/ogg':
			case 'audio/wav':
				$this->contentHeading(getMLText("preview"));
	?>
			<audio controls style="width: 100%;">
			<source  src="../op/op.Download.php?documentid=<?php echo $document->getID(); ?>&version=<?php echo $latestContent->getVersion(); ?>" type="audio/mpeg">
			</audio>
	<?php
				break;
			case 'application/pdf':
				$this->contentHeading(getMLText("preview"));
	?>
				<iframe id="este-es-el-id" src="../pdfviewer/web/viewer.html?file=<?php echo urlencode('../../op/op.Download.php?documentid='.$document->getID().'&version='.$latestContent->getVersion()); ?>" width="100%" height="700px"></iframe>
	<?php
				break;
			case 'image/svg+xml':
				$this->contentHeading(getMLText("preview"));
	?>
				<img src="../op/op.Download.php?documentid=<?php echo $document->getID(); ?>&version=<?php echo $latestContent->getVersion(); ?>" width="100%">
	<?php
				break;
			default:
				$txt = $this->callHook('additionalDocumentPreview', $latestContent);
				if(is_string($txt))
					echo $txt;
				break;
			}
		}
		$txt = $this->callHook('postDocumentPreview', $latestContent);
		if(is_string($txt))
			echo $txt;

		if($converttopdf) {
			$pdfpreviewer = new SeedDMS_Preview_PdfPreviewer($cachedir, $timeout);
			if($pdfpreviewer->hasConverter($latestContent->getMimeType())) {
				$this->contentHeading(getMLText("preview"));
?>
				<iframe src="../pdfviewer/web/viewer.html?file=<?php echo urlencode('../../op/op.PdfPreview.php?documentid='.$document->getID().'&version='.$latestContent->getVersion()); ?>" width="100%" height="700px"></iframe>
<?php
			}
		}
	} /* }}} */

	function show() { /* {{{ */
		parent::show();
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$accessop = $this->params['accessobject'];
		$viewonlinefiletypes = $this->params['viewonlinefiletypes'];
		$enableownerrevapp = $this->params['enableownerrevapp'];
		$workflowmode = $this->params['workflowmode'];
		$cachedir = $this->params['cachedir'];
		$previewwidthlist = $this->params['previewWidthList'];
		$previewwidthdetail = $this->params['previewWidthDetail'];
		$documentid = $document->getId();
		$currenttab = $this->params['currenttab'];
		$timeout = $this->params['timeout'];

		$versions = $document->getContent();

		$this->htmlAddHeader('<link href="../styles/'.$this->theme.'/plugins/timeline/timeline.css" rel="stylesheet">'."\n", 'css');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/timeline/timeline-min.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/timeline/timeline-locales.js"></script>'."\n", 'js');


		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-collapse sidebar-mini");

		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();		
		//print  "<iframe name=\"iFrameDocumento\" id=\"iFrameDocumento\" src=\"http://www.pdf995.com/samples/pdf.pdf\" style=\"height: 500px; width: 100%;\"></iframe>";
		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		echo "<div class=\"row\" id=\"thedocinfo\">";

		//$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		//$this->globalNavigation($folder);
		//$this->contentStart();
		//$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

		if ($document->isLocked()) {
			$lockingUser = $document->getLockingUser();
			$txt = $this->callHook('documentIsLocked', $document, $lockingUser);
			if(is_string($txt))
				echo $txt;
			else {
?>
	<div class="col-md-12">
		<div class="callout callout-warning alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			<?php printMLText("lock_message", array("email" => $lockingUser->getEmail(), "username" => htmlspecialchars($lockingUser->getFullName())));?>
		</div>
	</div>
<?php
			}
		}

		/* Retrieve attacheѕ files */
		$files = $document->getDocumentFiles();

		/* Retrieve linked documents */
		$links = $document->getDocumentLinks();
		$links = SeedDMS_Core_DMS::filterDocumentLinks($user, $links, 'target');

		/* Retrieve reverse linked documents */
		$reverselinks = $document->getReverseDocumentLinks();
		$reverselinks = SeedDMS_Core_DMS::filterDocumentLinks($user, $reverselinks, 'source');

		/* Retrieve latest content */
		$latestContent = $document->getLatestContent();
		$needwkflaction = false;
		if($workflowmode == 'traditional' || $workflowmode == 'traditional_only_approval') {
		} else {
			$workflow = $latestContent->getWorkflow();
			if($workflow) {
				$workflowstate = $latestContent->getWorkflowState();
				$transitions = $workflow->getNextTransitions($workflowstate);
				$needwkflaction = $latestContent->needsWorkflowAction($user);
			}
		}

		if($needwkflaction) {
			echo "<div class='col-md-12'>";
			$this->infoMsg(getMLText('needs_workflow_action'));
			echo "</div>";
		}

		$status = $latestContent->getStatus();
		$reviewStatus = $latestContent->getReviewStatus();
		$approvalStatus = $latestContent->getApprovalStatus();
?>

<!--<div class="col-md-4" id="document-info">-->
<?php
		// Document information -----------------------------------------------------------------------------------------------
		//$this->documentInfos();
		// End Document information -------------------------------------------------------------------------------------------
		//$this->preview();
?>
<!--</div>-->
<?php // Tabs information ------------------------------------------------------------------------------------------------- ?>
<div class="col-md-12" id="tab-information">
	<div class="nav-tabs-custom">
    	<ul class="nav nav-tabs" id="docinfotab">

    	<li class="<?php if(!$currenttab || $currenttab == 'document-info') echo 'active'; ?>"><a href="#document-info" data-toggle="tab" aria-expanded="false"><?php printMLText('ficha_resolucion'); ?></a></li>

    	<li class="<?php if($currenttab == 'links') echo 'active'; ?>"><a href="#links" data-toggle="tab" aria-expanded="false"><?php printMLText('vista_previa'); echo (count($links)) ? " (".count($links).")" : ""; ?></a></li>

		<li class="<?php if($currenttab == 'docinfo') echo 'active'; ?>"><a href="#docinfo" data-toggle="tab" aria-expanded="false"><?php printMLText('informacion_fichero'); ?></a></li>

		<?php if (count($versions)>1) { ?>
		  		
<?php
			}
			if($workflowmode == 'traditional' || $workflowmode == 'traditional_only_approval') {
				if((is_array($reviewStatus) && count($reviewStatus)>0) ||
					(is_array($approvalStatus) && count($approvalStatus)>0)) {
?>
		  	<li class="<?php if($currenttab == 'revapp') echo 'active'; ?>"><a href="#revapp" data-toggle="tab" aria-expanded="false"><?php if($workflowmode == 'traditional') echo getMLText('reviewers')."/"; echo getMLText('approvers'); ?></a></li>
<?php
				}
			} else {
				if($workflow) {
?>
		  		<li class="<?php if($currenttab == 'workflow') echo 'active'; ?>"><a href="#workflow" data-toggle="tab" aria-expanded="false"><?php echo getMLText('workflow'); ?></a></li>
<?php
				}
			}
?>
		<li class="<?php if($currenttab == 'attachments') echo 'active'; ?>"><a href="#attachments" data-toggle="tab" aria-expanded="false"><?php printMLText('linked_files'); echo (count($files)) ? " (".count($files).")" : ""; ?></a></li>

			
		</ul>

		<div class="tab-content">

			<div class="tab-pane <?php if(!$currenttab || $currenttab == 'document-info') echo 'active'; ?>" id="document-info">
				<?php $this->documentInfos(); ?>
			</div>

		  	<div class="tab-pane <?php if($currenttab == 'docinfo') echo 'active'; ?>" id="docinfo">
<?php
		if(!$latestContent) {
			print getMLText('document_content_missing');
			$this->contentEnd();
			$this->mainFooter();		
			$this->containerEnd();
			$this->htmlEndPage();
			exit;
		}

		// verify if file exists
		$file_exists=file_exists($dms->contentDir . $latestContent->getPath());

		print "<div class=\"table-responsive\">";
		print "<table class=\"table table-striped\">";
		print "<thead>\n<tr>\n";
		print "<th width='*' class='align-center th-info-background'></th>\n";
		print "<th width='*' class='align-center th-info-background'>".getMLText("file")."</th>\n";
		//print "<th width='25%' class='align-center th-info-background'>".getMLText("comment_for_current_version")."</th>\n";
		//quitado por Mario
		//print "<th width='15%' class='align-center th-info-background'>".getMLText("status")."</th>\n";
		print "<th width='20%' class='align-center th-info-background'>".getMLText("actions")."</th>\n";
		print "</tr></thead><tbody>\n";
		print "<tr>\n";
		print "<td>";
		
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidthdetail, $timeout);
		$previewer->createPreview($latestContent);
		if ($file_exists && !$document->isLocked()) {
			print "<a href=\"../op/op.Download.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\">";
			/*if ($viewonlinefiletypes && in_array(strtolower($latestContent->getFileType()), $viewonlinefiletypes)) {
				print "<a target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=". $latestContent->getVersion()."\">";
			} else {
				print "<a href=\"../op/op.Download.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\">";
			}*/
		}

		if($previewer->hasPreview($latestContent)) {

				print("<img class=\"mimeicon\" width=\"".$previewwidthdetail."\" src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=".$previewwidthdetail."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">");

		} else {
			print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
		}
		if ($file_exists && !$document->isLocked()) {
			print "</a>";
		}
		print "</td>\n";
		print "<td><ul class=\"actions unstyled\">\n";
		print "<li class=\"wordbreak\">".$latestContent->getOriginalFileName() ."</li>\n";
		//print "<li>".getMLText('version').": ".$latestContent->getVersion()."</li>\n";

		if ($file_exists)
			print "<li>". SeedDMS_Core_File::format_filesize($latestContent->getFileSize()) .", ".htmlspecialchars($latestContent->getMimeType())."</li>";
		else print "<li><span class=\"warning\">".getMLText("document_deleted")."</span></li>";

		$updatingUser = $latestContent->getUser();
		//print "<li>".getMLText("uploaded_by")." <a href=\"mailto:".$updatingUser->getEmail()."\">".htmlspecialchars($updatingUser->getFullName())."</a></li>";
		print "<li>".getLongReadableDate($latestContent->getDate())."</li>";

    print "</ul></td>\n";
/*
    print "<td>";
    print $latestContent->getComment();
    print "</td>";*/

		print "<ul class=\"actions unstyled\">\n";
		$attributes = $latestContent->getAttributes();
		if($attributes) {
      print "<td>";
			foreach($attributes as $attribute) {
				$arr = $this->callHook('showDocumentContentAttribute', $latestContent, $attribute);
				if(is_array($arr)) {
					print "<li>".$arr[0].": ".$arr[1]."</li>\n";
				} else {
					$attrdef = $attribute->getAttributeDefinition();
					print "<li>".htmlspecialchars($attrdef->getName()).": ".htmlspecialchars(implode(', ', $attribute->getValueAsArray()))."</li>\n";
				}
			}
      print "</ul></td>\n";
		}

		//quitado por Mario
	/*	print "<td width='10%'>";
		print getOverallStatusText($status["status"]);
		if ( $status["status"]==S_DRAFT_REV || $status["status"]==S_DRAFT_APP || $status["status"]==S_IN_WORKFLOW || $status["status"]==S_EXPIRED ){
			print "<br><span".($document->hasExpired()?" class=\"warning\" ":"").">".(!$document->getExpires() ? getMLText("does_not_expire") : getMLText("expires").": ".getReadableDate($document->getExpires()))."</span>";
		}
		print "</td>";*/

		print "<td class='align-center'>";

		// Document actions --------------------------------------------------------------------------------------------------------------- 

		echo "<div class=\"btn-group-horizontal\">";

		/* Block for allow view/download the file*/
		//$theaccessMode2 = $folder->getAccessMode($this->params['user']);
		if ($document->getAccessMode($user) >= M_READWRITE) { // If the user have read-write
				if ($file_exists)
				{
					print "<a type=\"button\" class=\"btn btn-primary btn-action\" href=\"../op/op.Download.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("download")."\"><i class=\"fa fa-download\"></i></a>";
	print "<a type=\"button\" class=\"btn btn-warning btn-action\" href=\"../op/op.Descargartxt.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\""."Descargar versión de la resolución en formato TXT"."\"><i class=\"fa fa-file-text\"></i></a>";
					if (htmlspecialchars($latestContent->getMimeType()) == 'application/pdf' ) {
						print '<a type="button" class="btn btn-info preview-doc-btn btn-action" id="'.$documentid.'" rel="'.$latestContent->getVersion().'" title="'.htmlspecialchars($document->getName()).' - '.getMLText('current_version').': '.$latestContent->getVersion().'"><i class="fa fa-eye"></i></a>';
					} else {
						print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=". $latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("preview")."\"><i class=\"fa fa-eye\"></i></a>";
					}
					
						//print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=". $latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("preview")."\"><i class=\"fa fa-eye\"></i></a>";
				}
		}

		if ($document->getAccessMode($user) == M_READ) { // If the user only can read
			// TODO: GET final status of the document
			if($status["status"] == S_RELEASED && !($document->isLocked())) {
				if ($file_exists){
					print "<a type=\"button\" class=\"btn btn-primary btn-action\" href=\"../op/op.Download.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("download")."\"><i class=\"fa fa-download\"></i></a>";
print "<a type=\"button\" class=\"btn btn-warning btn-action\" href=\"../op/op.Descargartxt.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\""."Descargar versión de la resolución en formato TXT"."\"><i class=\"fa fa-file-text\"></i></a>";
					if (htmlspecialchars($latestContent->getMimeType()) == 'application/pdf' ) {
						print '<a type="button" class="btn btn-info preview-doc-btn btn-action" id="'.$documentid.'" rel="'.$latestContent->getVersion().'" title="'.htmlspecialchars($document->getName()).' - '.getMLText('current_version').': '.$latestContent->getVersion().'"><i class="fa fa-eye"></i></a>';
					} else {
						print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=". $latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("preview")."\"><i class=\"fa fa-eye\"></i></a>";
					}

					//if ($viewonlinefiletypes && in_array(strtolower($latestContent->getFileType()), $viewonlinefiletypes))
					//	print '<a type="button" class="btn btn-info preview-doc-btn btn-action" id="'.$documentid.'" rel="'.$latestContent->getVersion().'" title="'.htmlspecialchars($document->getName()).' - '.getMLText('current_version').': '.$latestContent->getVersion().'"><i class="fa fa-eye"></i></a>';

						//print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=". $latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("preview")."\"><i class=\"fa fa-eye\"></i></a>";
				}
			}
		}
		
		// Option to edit online the version
		/*if ($theaccessMode2 >= M_READ) {
			if ($file_exists){
				if($accessop->mayEditVersion()) {
					print "<a type=\"button\" class=\"btn btn-warning btn-action\" href=\"../out/out.EditOnline.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_online")."\"><i class=\"fa fa-edit\"></i></a>";
				}
			}
		}*/

		/* Only admin has the right to remove version in any case or a regular
		 * user if enableVersionDeletion is on
		 */
		if($accessop->mayRemoveVersion()) 
		{
			//print "<a type=\"button\" class=\"btn btn-danger btn-action\" href=\"../out/out.RemoveVersion.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("rm_version")."\"><i class=\"fa fa-times\"></i></a>";
		}
		// if($accessop->mayOverwriteStatus()) 
		// {
		// 	if($status["status"] != -2){
		// 		print "<a type=\"button\" class=\"btn btn-warning btn-action\" href='../out/out.OverrideContentStatus.php?documentid=".$documentid."&version=".$latestContent->getVersion()."' data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("change_status")."\"><i class=\"fa fa-align-justify\"></i></a>";
		// 	}
		// }

		// if($workflowmode == 'traditional' || $workflowmode == 'traditional_only_approval') {
		// 	// Allow changing reviewers/approvals only if not reviewed
		// 	if($accessop->maySetReviewersApprovers()) {
		// 		print "<a type=\"button\" class=\"btn btn-success btn-action\" href='../out/out.SetReviewersApprovers.php?documentid=".$documentid."&version=".$latestContent->getVersion()."' data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("change_assignments")."\"><i class=\"fa fa-edit\"></i></a>";
		// 	}
		// } else {
		// 	if($accessop->maySetWorkflow()) {
		// 		if($status["status"] != -2){
		// 			if(!$workflow) {
		// 				print "<a type=\"button\" class=\"btn btn-warning btn-action\" href='../out/out.SetWorkflow.php?documentid=".$documentid."&version=".$latestContent->getVersion()."' data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("set_workflow")."\"><i class=\"fa fa-random\"></i></a>";
		// 			}
		// 		}
		// 	}
		// }

		// if($accessop->mayEditComment()) {
			// print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditComment.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_comment")."\"><i class=\"fa fa-comment\"></i></a>";
		// }

		$theaccessMode3 = $folder->getAccessMode($this->params['user']);
		if ($theaccessMode3 >= M_READWRITE || ($document->getAccessMode($user) >= M_READWRITE)) {
			if (!$this->params['user']->isGuest()) {
				if($status["status"] != -2){
					//print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditAttributes.php?documentid=".$documentid."&version=".$latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_attributes")."\"><i class=\"fa fa-edit\"></i></a>";
				}
			}
		}

		if ($theaccessMode3 >= M_READWRITE || ($document->getAccessMode($user) >= M_READWRITE)) {
			if (!$this->params['user']->isGuest()) {
				if ($status["status"] == S_RELEASED) 
				{


					print "<a type=\"button\" class=\"btn btn-primary btn-action\" href=\"out.UpdateDocument.php?documentid=".$documentid."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("update_document")."\"><i class=\"fa fa-refresh\"></i></a>";

					print "<a type=\"button\" class=\"btn btn-danger btn-action\" href=\"out.EliminarResolucion.php?documentid=".$documentid."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\""."Eliminar resolución"."\"><i class=\"fa fa-eraser\"></i></a>";
					//print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditAttributes.php?documentid=".$document->getID()."&version=".$version->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_attributes")."\"><i class=\"fa fa-edit\"></i></a>";
					print '<a type="button" href="/out/out.EditDocument.php?documentid='.$documentid.'&showtree=1" class="btn btn-success btn-sm btn-action" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("edit_document_props").'"><i class="fa fa-pencil"></i></a>';
					
				}
			}
		}

		echo "</div>";
		print "</td>";
		print "</tr></tbody>\n</table>\n";
		print "</div>";

		/* ------------------------------------------------------------------------------------------------ */


		/*if($user->isAdmin()) {
			$this->contentHeading(getMLText("status"));
			$this->contentContainerStart();
			$statuslog = $latestContent->getStatusLog();
			echo "<table class=\"table table-condensed\"><thead>";
			echo "<th>".getMLText('date')."</th><th>".getMLText('status')."</th><th>".getMLText('user')."</th><th>".getMLText('comment')."</th></tr>\n";
			echo "</thead><tbody>";
			foreach($statuslog as $entry) {
				if($suser = $dms->getUser($entry['userID']))
					$fullname = $suser->getFullName();
				else
					$fullname = "--";
				echo "<tr><td>".$entry['date']."</td><td>".getOverallStatusText($entry['status'])."</td><td>".$fullname."</td><td>".$entry['comment']."</td></tr>\n";
			}
			print "</tbody>\n</table>\n";
			$this->contentContainerEnd();

			$wkflogs = $latestContent->getWorkflowLog();
			if($wkflogs) {
				$this->contentHeading(getMLText("workflow_summary"));
				$this->contentContainerStart();
				echo "<table class=\"table table-condensed\"><thead>";
				echo "<th>".getMLText('date')."</th><th>".getMLText('action')."</th><th>".getMLText('user')."</th><th>".getMLText('comment')."</th></tr>\n";
				echo "</thead><tbody>";
				foreach($wkflogs as $wkflog) {
					echo "<tr>";
					echo "<td>".$wkflog->getDate()."</td>";
					echo "<td>".$wkflog->getTransition()->getAction()->getName()."</td>";
					$loguser = $wkflog->getUser();
					echo "<td>".$loguser->getFullName()."</td>";
					echo "<td>".$wkflog->getComment()."</td>";
					echo "</tr>";
				}
				print "</tbody>\n</table>\n";
				$this->contentContainerEnd();
			}
		}*/
?>
		</div> <!-- End first tab -->

<?php
		if($workflowmode == 'traditional' || $workflowmode == 'traditional_only_approval') {
			if((is_array($reviewStatus) && count($reviewStatus)>0) ||
				(is_array($approvalStatus) && count($approvalStatus)>0)) {
?>
		  <div class="tab-pane <?php if($currenttab == 'revapp') echo 'active'; ?>" id="revapp">
<?php
		$this->contentContainerstart();
		print "<table class=\"table-condensed\">\n";

		/* Just check fo an exting reviewStatus, even workflow mode is set
		 * to traditional_only_approval. There may be old documents which
		 * are still in S_DRAFT_REV.
		 */
		if (/*$workflowmode != 'traditional_only_approval' &&*/ is_array($reviewStatus) && count($reviewStatus)>0) {

			print "<tr><td colspan=5>\n";
			$this->contentSubHeading(getMLText("reviewers"));
			print "</tr>";

			print "<tr>\n";
			print "<td width='20%'><b>".getMLText("name")."</b></td>\n";
			print "<td width='20%'><b>".getMLText("last_update")."</b></td>\n";
			print "<td width='25%'><b>".getMLText("comment")."</b></td>";
			print "<td width='15%'><b>".getMLText("status")."</b></td>\n";
			print "<td width='20%'></td>\n";
			print "</tr>\n";

			foreach ($reviewStatus as $r) {
				$required = null;
				$is_reviewer = false;
				switch ($r["type"]) {
					case 0: // Reviewer is an individual.
						$required = $dms->getUser($r["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_user")." '".$r["required"]."'";
						}
						else {
							$reqName = htmlspecialchars($required->getFullName()." (".$required->getLogin().")");
							if($required->getId() == $user->getId()/* && ($user->getId() != $owner->getId() || $enableownerrevapp == 1)*/)
								$is_reviewer = true;
						}
						break;
					case 1: // Reviewer is a group.
						$required = $dms->getGroup($r["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_group")." '".$r["required"]."'";
						}
						else {
							$reqName = "<i>".htmlspecialchars($required->getName())."</i>";
							if($required->isMember($user)/* && ($user->getId() != $owner->getId() || $enableownerrevapp == 1)*/)
								$is_reviewer = true;
						}
						break;
				}
				print "<tr>\n";
				print "<td>".$reqName."</td>\n";


				//print "<embed src=\"http://www.pdf995.com/samples/pdf.pdf\" width=\"500\" height=\"375\" type='application/pdf'>";
				print "<td><ul class=\"unstyled\"><li>".$r["date"]."</li>";
				/* $updateUser is the user who has done the review */
				$updateUser = $dms->getUser($r["userID"]);
				print "<li>".(is_object($updateUser) ? htmlspecialchars($updateUser->getFullName()." (".$updateUser->getLogin().")") : "unknown user id '".$r["userID"]."'")."</li></ul></td>";
				print "<td>".htmlspecialchars($r["comment"]);
				if($r['file']) {
					echo "<br />";
					echo "<a href=\"../op/op.Download.php?documentid=".$documentid."&reviewlogid=".$r['reviewLogID']."\" class=\"btn btn-mini\"><i class=\"icon-download\"></i> ".getMLText('download')."</a>";
				}
				print "</td>\n";
				print "<td>".getReviewStatusText($r["status"])."</td>\n";
				print "<td><ul class=\"unstyled\">";

				if($accessop->mayReview()) {
					if ($is_reviewer) {
						if ($r["status"]==0) {
							print "<li><a href=\"../out/out.ReviewDocument.php?documentid=".$documentid."&version=".$latestContent->getVersion()."&reviewid=".$r['reviewID']."\" class=\"btn btn-sm btn-warning\">".getMLText("add_review")."</a></li>";
						} elseif ($accessop->mayUpdateReview($updateUser) && (($r["status"]==1)||($r["status"]==-1))) {
							print "<li><a href=\"../out/out.ReviewDocument.php?documentid=".$documentid."&version=".$latestContent->getVersion()."&reviewid=".$r['reviewID']."\" class=\"btn btn-sm btn-warning\">".getMLText("edit")."</a></li>";
						}
					}
				}

				print "</ul></td>\n";	
				print "</tr>\n";
			}
		}

		if (is_array($approvalStatus) && count($approvalStatus)>0) {

			print "<tr><td colspan=5>\n";
			$this->contentSubHeading(getMLText("approvers"));
			print "</tr>";

			print "<tr>\n";
			print "<td width='20%'><b>".getMLText("name")."</b></td>\n";
			print "<td width='20%'><b>".getMLText("last_update")."</b></td>\n";	
			print "<td width='25%'><b>".getMLText("comment")."</b></td>";
			print "<td width='15%'><b>".getMLText("status")."</b></td>\n";
			print "<td width='20%'></td>\n";
			print "</tr>\n";

			foreach ($approvalStatus as $a) {
				$required = null;
				$is_approver = false;
				switch ($a["type"]) {
					case 0: // Approver is an individual.
						$required = $dms->getUser($a["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_user")." '".$a["required"]."'";
						}
						else {
							$reqName = htmlspecialchars($required->getFullName()." (".$required->getLogin().")");
							if($required->getId() == $user->getId())
								$is_approver = true;
						}
						break;
					case 1: // Approver is a group.
						$required = $dms->getGroup($a["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_group")." '".$a["required"]."'";
						}
						else {
							$reqName = "<i>".htmlspecialchars($required->getName())."</i>";
							if($required->isMember($user)/* && ($user->getId() != $owner->getId() || $enableownerrevapp == 1)*/)
								$is_approver = true;
						}
						break;
				}
				print "<tr>\n";
				print "<td>".$reqName."</td>\n";
				print "<td><ul class=\"unstyled\"><li>".$a["date"]."</li>";
				/* $updateUser is the user who has done the approval */
				$updateUser = $dms->getUser($a["userID"]);
				print "<li>".(is_object($updateUser) ? htmlspecialchars($updateUser->getFullName()." (".$updateUser->getLogin().")") : "unknown user id '".$a["userID"]."'")."</li></ul></td>";	
				print "<td>".htmlspecialchars($a["comment"]);
				if($a['file']) {
					echo "<br />";
					echo "<a href=\"../op/op.Download.php?documentid=".$documentid."&approvelogid=".$a['approveLogID']."\" class=\"btn btn-mini\"><i class=\"icon-download\"></i> ".getMLText('download')."</a>";
				}
				echo "</td>\n";
				print "<td>".getApprovalStatusText($a["status"])."</td>\n";
				print "<td><ul class=\"unstyled\">";

				if($accessop->mayApprove()) {
					if ($is_approver) {
						if ($a['status'] == 0) {
							print "<li><a class=\"btn btn-mini\" href=\"../out/out.ApproveDocument.php?documentid=".$documentid."&version=".$latestContent->getVersion()."&approveid=".$a['approveID']."\">".getMLText("add_approval")."</a></li>";
						} elseif ($accessop->mayUpdateApproval($updateUser) && (($a["status"]==1)||($a["status"]==-1))) {
							print "<li><a class=\"btn btn-mini\" href=\"../out/out.ApproveDocument.php?documentid=".$documentid."&version=".$latestContent->getVersion()."&approveid=".$a['approveID']."\">".getMLText("edit")."</a></li>";
						}
					}
				}

				print "</ul>";
				print "</td>\n</tr>\n";
			}
		}

		print "</table>\n";
		$this->contentContainerEnd();

		if($user->isAdmin()) {
?>

		<div class="row">
			<div class="col-md-12">
<?php
			/* Check for an existing review log, even if the workflowmode
			 * is set to traditional_only_approval. There may be old documents
			 * that still have a review log if the workflow mode has been
			 * changed afterwards.
			 */
			if($latestContent->getReviewStatus(10) /*$workflowmode != 'traditional_only_approval'*/) {
?>
				<div class="col-md-6">
				<?php $this->printProtocol($latestContent, 'review'); ?>
				</div>
<?php
			}
?>
				<div class="col-md-6">
				<?php $this->printProtocol($latestContent, 'approval'); ?>
				</div>
			</div>
		</div>
<?php
		}
?>
		  </div>
<?php
		}
		} else { ///////////////////////////////////////////// Wokflow Advanced ///////////////////////////////////////////////////////////
			if($workflow) {
				/* Check if user is involved in workflow */
				$user_is_involved = false;
				foreach($transitions as $transition) {
					if($latestContent->triggerWorkflowTransitionIsAllowed($user, $transition)) {
						$user_is_involved = true;
					}
				}
?>
		  <div class="tab-pane <?php if($currenttab == 'workflow') echo 'active'; ?>" id="workflow">
<?php
			echo "<div class=\"row\">";

			if(($document->getAccessMode($this->params['user']) >= M_READWRITE) || $user->isAdmin()){

			echo "<div class=\"col-md-12 col-no-padding pull-right\">";
			echo "<div class=\"pull-right delete-wokflow-div\">";

				/* Block for remove the workflow of the document*/

				if($user->isAdmin()) {
					if(SeedDMS_Core_DMS::checkIfEqual($workflow->getInitState(), $latestContent->getWorkflowState())) {
						print "<form action=\"../out/out.RemoveWorkflowFromDocument.php\" method=\"post\">".createHiddenFieldWithKey('removeworkflowfromdocument')."<input type=\"hidden\" name=\"documentid\" value=\"".$documentid."\" /><input type=\"hidden\" name=\"folderid\" value=\"".$folder->getId()."\" /><input type=\"hidden\" name=\"version\" value=\"".$latestContent->getVersion()."\" /><button type=\"submit\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText('rm_workflow')."</button></form>";
					}
				}


				/* Block for rewind the workflow status */

				//$theaccessMode = $folder->getAccessMode($this->params['user']);

				if ($document->getAccessMode($this->params['user']) >= M_READWRITE) {
					if (!$this->params['user']->isGuest()) {
						if(!(SeedDMS_Core_DMS::checkIfEqual($workflow->getInitState(), $latestContent->getWorkflowState()))) {
							print "<form action=\"../out/out.RewindWorkflow.php\" method=\"post\">".createHiddenFieldWithKey('rewindworkflow')."<input type=\"hidden\" name=\"documentid\" value=\"".$documentid."\" /><input type=\"hidden\" name=\"folderid\" value=\"".$folder->getId()."\" /><input type=\"hidden\" name=\"version\" value=\"".$latestContent->getVersion()."\" /><button type=\"submit\" class=\"btn btn-danger\"><i class=\"fa fa-refresh\"></i> ".getMLText('rewind_workflow')."</button></form>";
						}
					}
				}

				echo "</div>";
				echo "</div>";
			}

			echo "<div class=\"col-md-12 col-no-padding\">";
			echo "<div class=\"box box-primary box-solid\" id=\"workflow-information\">";
      echo "<div class=\"box-header with-border\">";
      echo "<h5 class=\"\"><strong>".getMLText("workflow").":</strong> ".$workflow->getName()." - (".$workflowstate->getName().")</h5>";
      echo "</div>";
      echo "<div class=\"box-body\">";

			if($parentworkflow = $latestContent->getParentWorkflow()) {
				echo "<p>Sub workflow of '".$parentworkflow->getName()."'</p>";
			}

			echo "<table class=\"table table-striped\">\n";
			echo "<tr>";
			echo "<td><strong>".getMLText('next_state').":</strong></td>";
			foreach($transitions as $transition) {
				$nextstate = $transition->getNextState();
				$docstatus = $nextstate->getDocumentStatus();
				echo "<td class='td-action-bkg-one'><i class=\"fa fa-arrow-right".($docstatus == S_RELEASED ? " released" : ($docstatus == S_REJECTED ? " rejected" : " in-workflow"))."\"></i> ".$nextstate->getName()."</td>";
			}
			echo "</tr>";
			echo "<tr>";
			echo "<td><strong>".getMLText('action').":</strong></td>";
			foreach($transitions as $transition) {
				$action = $transition->getAction();
				echo "<td class='td-action-bkg-two'>".getMLText('action_'.strtolower($action->getName()), array(), $action->getName())."</td>";
			}
			echo "</tr>";
			echo "<tr>";
			echo "<td><strong>".getMLText('users').":</strong></td>";
			foreach($transitions as $transition) {
				$transusers = $transition->getUsers();
				echo "<td>";
				foreach($transusers as $transuser) {
					$u = $transuser->getUser();
					echo $u->getFullName();
					if($document->getAccessMode($u) < M_READ) {
						echo " (no access)";
					}
					echo "<br />";
				}
				echo "</td>";
			}
			echo "</tr>";
			echo "<tr>";
			echo "<td><strong>".getMLText('groups').":</strong></td>";
			foreach($transitions as $transition) {
				$transgroups = $transition->getGroups();
				echo "<td>";
				foreach($transgroups as $transgroup) {
					$g = $transgroup->getGroup();
					echo getMLText('at_least_n_users_of_group',
						array("number_of_users" => $transgroup->getNumOfUsers(),
							"group" => $g->getName()));
					if ($document->getGroupAccessMode($g) < M_READ) {
						echo " (no access)";
					}
					echo "<br />";
				}
				echo "</td>";
			}
			echo "</tr>";
			echo "<tr class=\"success\">";
			echo "<td>".getMLText('users_done_work').":</td>";
			foreach($transitions as $transition) {
				echo "<td>";
				if($latestContent->executeWorkflowTransitionIsAllowed($transition)) {
					/* If this is reached, then the transition should have been executed
					 * but for some reason the next state hasn't been reached. This can
					 * be causes, if a transition which was previously already executed
					 * is about to be executed again. E.g. there was already a transition
					 * T1 from state S1 to S2 triggered by user U1.
					 * Then there was a second transition T2 from
					 * S2 back to S1. If the state S1 has been reached again, then
					 * executeWorkflowTransitionIsAllowed() will think that T1 could be
					 * executed because there is already a log entry saying, that U1
					 * has triggered the workflow.
					 */
					echo "Done ";
				}
				$wkflogs = $latestContent->getWorkflowLog($transition);
				foreach($wkflogs as $wkflog) {
					$loguser = $wkflog->getUser();
					echo $loguser->getFullName()." (";
					$names = array();
					foreach($loguser->getGroups() as $loggroup) {
						$names[] =  $loggroup->getName();
					}
					echo implode(", ", $names);
					echo ") - ";
					echo $wkflog->getDate();
					echo "<br />";
				}
				echo "</td>";
			}
			echo "</tr>";
			echo "<tr>";
			echo "<td></td>";
			$allowedtransitions = array();
			foreach($transitions as $transition) {
				echo "<td>";
				if($latestContent->triggerWorkflowTransitionIsAllowed($user, $transition)) {
					$action = $transition->getAction();
					print "<form action=\"../out/out.TriggerWorkflow.php\" method=\"post\">".createHiddenFieldWithKey('triggerworkflow')."<input type=\"hidden\" name=\"documentid\" value=\"".$documentid."\" /><input type=\"hidden\" name=\"version\" value=\"".$latestContent->getVersion()."\" /><input type=\"hidden\" name=\"transition\" value=\"".$transition->getID()."\" /><input type=\"submit\" class=\"btn btn-primary\" value=\"".getMLText('action_'.strtolower($action->getName()), array(), $action->getName())."\" /></form>";
					$allowedtransitions[] = $transition;
				}
				echo "</td>";
			}
			echo "</tr>";
			echo "</table>";

			/*$workflows = $dms->getAllWorkflows();
			if($workflows) {
				$subworkflows = array();
				foreach($workflows as $wkf) {
					if($wkf->getInitState()->getID() == $workflowstate->getID()) {
						if($workflow->getID() != $wkf->getID()) {
							$subworkflows[] = $wkf;
						}
					}
				}
				if($subworkflows) {
					echo "<form action=\"../out/out.RunSubWorkflow.php\" method=\"post\">".createHiddenFieldWithKey('runsubworkflow')."<input type=\"hidden\" name=\"documentid\" value=\"".$documentid."\" /><input type=\"hidden\" name=\"version\" value=\"".$latestContent->getVersion()."\" />";
					echo "<select name=\"subworkflow\">";
					foreach($subworkflows as $subworkflow) {
						echo "<option value=\"".$subworkflow->getID()."\">".$subworkflow->getName()."</option>";
					}
					echo "</select>";
					echo "<label class=\"inline\">";
					echo "<input type=\"submit\" class=\"btn btn-info\" value=\"".getMLText('run_subworkflow')."\" />";
					echo "</lable>";
					echo "</form>";
				}
			}
			/* If in a sub workflow, the check if return the parent workflow
			 * is possible.
			 */
			/*if($parentworkflow = $latestContent->getParentWorkflow()) {
				$states = $parentworkflow->getStates();
				foreach($states as $state) {*/
					/* Check if the current workflow state is also a state in the
					 * parent workflow
					 */
					/*if($latestContent->getWorkflowState()->getID() == $state->getID()) {
						echo "Switching from sub workflow '".$workflow->getName()."' into state ".$state->getName()." of parent workflow '".$parentworkflow->getName()."' is possible<br />";
						/* Check if the transition from the state where the sub workflow
						 * starts into the current state is also allowed in the parent
						 * workflow. Checking at this point is actually too late, because
						 * the sub workflow shouldn't be entered in the first place,
						 * but that is difficult to check.
						 */
						/* If the init state has not been left, return is always possible */
						/*if($workflow->getInitState()->getID() == $latestContent->getWorkflowState()->getID()) {
							echo "Initial state of sub workflow has not been left. Return to parent workflow is possible<br />";
							echo "<form action=\"../out/out.ReturnFromSubWorkflow.php\" method=\"post\">".createHiddenFieldWithKey('returnfromsubworkflow')."<input type=\"hidden\" name=\"documentid\" value=\"".$documentid."\" /><input type=\"hidden\" name=\"version\" value=\"".$latestContent->getVersion()."\" />";
							echo "<input type=\"submit\" class=\"btn btn-info\" value=\"".getMLText('return_from_subworkflow')."\" />";
							echo "</form>";
						} else {
							/* Get a transition from the last state in the parent workflow
							 * (which is the initial state of the sub workflow) into
							 * current state.
							 */
							/*echo "Check for transition from ".$workflow->getInitState()->getName()." into ".$latestContent->getWorkflowState()->getName()." is possible in parentworkflow ".$parentworkflow->getID()."<br />";
							$transitions = $parentworkflow->getTransitionsByStates($workflow->getInitState(), $latestContent->getWorkflowState());
							if($transitions) {
								echo "Found transitions in workflow ".$parentworkflow->getID()."<br />";
								foreach($transitions as $transition) {
									if($latestContent->triggerWorkflowTransitionIsAllowed($user, $transition)) {
										echo "Triggering transition is allowed<br />";
										echo "<form action=\"../out/out.ReturnFromSubWorkflow.php\" method=\"post\">".createHiddenFieldWithKey('returnfromsubworkflow')."<input type=\"hidden\" name=\"documentid\" value=\"".$documentid."\" /><input type=\"hidden\" name=\"version\" value=\"".$latestContent->getVersion()."\" /><input type=\"hidden\" name=\"transition\" value=\"".$transition->getID()."\" />";
										echo "<input type=\"submit\" class=\"btn btn-info\" value=\"".getMLText('return_from_subworkflow')."\" />";
										echo "</form>";

									}
								}
							}
						}
					}
				}
			}*/
		
			// ---------------- Show workflow diagram --------------------- //

			/*if($user_is_involved || $user->isAdmin()) { 
				echo "<div class=\"span6\">";
			?>
				<!--<iframe src="out.WorkflowGraph.php?workflow=<?php echo $workflow->getID(); ?><?php if($allowedtransitions) foreach($allowedtransitions as $tr) {echo "&transitions[]=".$tr->getID();} ?>" width="99%" height="661" style="border: 1px solid #AAA;"></iframe>-->
			<?php
				echo "</div>";
			}*/

			echo "</div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";

?>
		</div> <!-- End wokflow tab -->
<?php
			}
		}
		if (count($versions)>1) {
?>
		  <div class="tab-pane <?php if($currenttab == 'previous') echo 'active'; ?>" id="previous">
<?php
			//$this->contentContainerStart();
			print "<div class=\"table-responsive\">";
			print "<table class=\"table table-striped\">";
			print "<thead>\n<tr>\n";
			print "<th width='10%'></th>\n";
			print "<th width='30%'>".getMLText("file")."</th>\n";
			print "<th width='25%'>".getMLText("comment")."</th>\n";
			print "<th width='15%'>".getMLText("status")."</th>\n";
			print "<th width='20%'></th>\n";
			print "</tr>\n</thead>\n<tbody>\n";

			for ($i = count($versions)-2; $i >= 0; $i--) {
				$version = $versions[$i];
				$vstat = $version->getStatus();
				$workflow = $version->getWorkflow();
				$workflowstate = $version->getWorkflowState();

				// verify if file exists
				$file_exists=file_exists($dms->contentDir . $version->getPath());

				print "<tr>\n";
				print "<td nowrap>";
				if($file_exists) {
					if ($viewonlinefiletypes && in_array(strtolower($version->getFileType()), $viewonlinefiletypes)) {
							print "<a target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=".$version->getVersion()."\">";
					} else {
						print "<a href=\"../op/op.Download.php?documentid=".$documentid."&version=".$version->getVersion()."\">";
					}
				}
				$previewer->createPreview($version);
				if($previewer->hasPreview($version)) {
					print("<img class=\"mimeicon\" width=\"".$previewwidthdetail."\" src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$version->getVersion()."&width=".$previewwidthdetail."\" title=\"".htmlspecialchars($version->getMimeType())."\">");
				} else {
					print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($version->getFileType())."\" title=\"".htmlspecialchars($version->getMimeType())."\">";
				}
				if($file_exists) {
					print "</a>\n";
				}
				print "</td>\n";
				print "<td><ul class=\"unstyled\">\n";
				print "<li>".$version->getOriginalFileName()."</li>\n";
				print "<li>".getMLText('version').": ".$version->getVersion()."</li>\n";
				if ($file_exists) print "<li>". SeedDMS_Core_File::format_filesize($version->getFileSize()) .", ".htmlspecialchars($version->getMimeType())."</li>";
				else print "<li><span class=\"warning\">".getMLText("document_deleted")."</span></li>";
				$updatingUser = $version->getUser();
				print "<li>".getMLText("uploaded_by")." <a href=\"mailto:".$updatingUser->getEmail()."\">".htmlspecialchars($updatingUser->getFullName())."</a></li>";
				print "<li>".getLongReadableDate($version->getDate())."</li>";
				print "</ul>\n";
				print "<ul class=\"actions unstyled\">\n";
				$attributes = $version->getAttributes();
				if($attributes) {
					foreach($attributes as $attribute) {
						$arr = $this->callHook('showDocumentContentAttribute', $version, $attribute);
						if(is_array($arr)) {
							print "<li>".$arr[0].": ".$arr[1]."</li>\n";
						} else {
							$attrdef = $attribute->getAttributeDefinition();
							print "<li>".htmlspecialchars($attrdef->getName()).": ".htmlspecialchars(implode(', ', $attribute->getValueAsArray()))."</li>\n";
						}
					}
				}
				print "</ul></td>\n";
				print "<td>".htmlspecialchars($version->getComment())."</td>";
				print "<td>".getOverallStatusText($vstat["status"])."</td>";
				print "<td>";

				print "<div class=\"btn-group-horizontal\">";

				if ($file_exists)
				{
					print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditAttributes.php?documentid=".$document->getID()."&version=".$version->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_attributes")."\"><i class=\"fa fa-edit\"></i></a>";
					print "<a type=\"button\" class=\"btn btn-primary btn-action\" href=\"../op/op.Download.php?documentid=".$documentid."&version=".$version->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("download")."\"><i class=\"fa fa-download\"></i></a>";
					if (htmlspecialchars($latestContent->getMimeType()) == 'application/pdf' ) {
						print '<a type="button" class="btn btn-info preview-doc-btn btn-action" id="'.$documentid.'" rel="'.$version->getVersion().'" title="'.htmlspecialchars($document->getName()).' - '.getMLText('current_version').': '.$version->getVersion().'"><i class="fa fa-eye"></i></a>';
					} else {
						print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&version=". $latestContent->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("preview")."\"><i class=\"fa fa-eye\"></i></a>";
					}

				}
				/* Only admin has the right to remove version in any case or a regular
				 * user if enableVersionDeletion is on
				 */
				if($accessop->mayRemoveVersion()) 
				{
					print "<a type=\"button\" class=\"btn btn-danger btn-action\" href=\"out.RemoveVersion.php?documentid=".$documentid."&version=".$version->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("rm_version")."\"><i class=\"fa fa-remove\"></i></a>";

					

				}
				// if($accessop->mayEditComment()) {
					// print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditComment.php?documentid=".$document->getID()."&version=".$version->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_comment")."\"><i class=\"fa fa-comment\"></i></a>";
				// }
				if($accessop->mayEditAttributes()) 
				{
					print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditAttributes.php?documentid=".$document->getID()."&version=".$version->getVersion()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("edit_attributes")."\"><i class=\"fa fa-edit\"></i></a>";


				}
				print "<a type=\"button\" class=\"btn btn-info btn-action\" href='../out/out.DocumentVersionDetail.php?documentid=".$documentid."&version=".$version->getVersion()."' data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("details")."\"><i class=\"fa fa-list\"></i></a>";

				print "</div>\n";
				print "</td>\n</tr>\n";
			}
			print "</tbody>\n</table>\n";
			print "</div>\n";
			
			//$this->contentContainerEnd();
?>
		  </div>
<?php
		}
?>
		  <div class="tab-pane <?php if($currenttab == 'attachments') echo 'active'; ?>" id="attachments">
<?php

		//$this->contentContainerStart();  ///////////////////////////////////////////////////////////////////

		if (count($files) > 0) {
			print "<div class=\"table-responsive\">";
			print "<table class=\"table table-striped\">";
			print "<thead>\n<tr>\n";
			print "<th width='20%' class='align-center td-warning-background'></th>\n";
			print "<th width='20%' class='align-center td-warning-background'>".getMLText("file")."</th>\n";
			print "<th width='40%' class='align-center td-warning-background'>".getMLText("comment")."</th>\n";
			print "<th width='20%' class='align-center td-warning-background'>".getMLText("actions")."</th>\n";
			print "</tr>\n</thead>\n<tbody>\n";

			foreach($files as $file) {

				$file_exists=file_exists($dms->contentDir . $file->getPath());

				$responsibleUser = $file->getUser();

				print "<tr>";
				print "<td class='align-center'>";
				$previewer->createPreview($file, $previewwidthdetail);
				if($file_exists) {
						print "<a href=\"../op/op.Download.php?documentid=".$documentid."&file=".$file->getID()."\">";
				}
				if($previewer->hasPreview($file)) {
					print("<img class=\"mimeicon\" width=\"".$previewwidthdetail."\" src=\"../op/op.Preview.php?documentid=".$document->getID()."&file=".$file->getID()."&width=".$previewwidthdetail."\" title=\"".htmlspecialchars($file->getMimeType())."\">");
				} else {
					print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($file->getFileType())."\" title=\"".htmlspecialchars($file->getMimeType())."\">";
				}
				if($file_exists) {
					print "</a>";
				}
				print "</td>";
				
				print "<td>\n";
				print "<span>".htmlspecialchars($file->getName())."</span><br>";
				//print "<li>".htmlspecialchars($file->getOriginalFileName())."</li>\n";
				/*if ($file_exists)
					print "<li>".SeedDMS_Core_File::format_filesize(filesize($dms->contentDir . $file->getPath())) ." bytes, ".htmlspecialchars($file->getMimeType())."</li>";
				else print "<li>".htmlspecialchars($file->getMimeType())." - <span class=\"warning\">".getMLText("document_deleted")."</span></li>";*/

				print "<i>".getMLText("uploaded_by")." <a href=\"mailto:".$responsibleUser->getEmail()."\">".htmlspecialchars($responsibleUser->getFullName())."</a></i>";
				/*print "<li>".getLongReadableDate($file->getDate())."</li>";*/
				print "</td>";
				print "<td>".htmlspecialchars($file->getComment())."</td>";
			
				print "<td>";
				print "<div class=\"btn-group-horizontal\">";

				if ($file_exists) {
					print "<a type=\"button\" class=\"btn btn-primary btn-action\" href=\"../op/op.Download.php?documentid=".$documentid."&file=".$file->getID()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("download")."\"><i class=\"fa fa-download\"></i></a>";

					if (htmlspecialchars($file->getMimeType()) == 'application/pdf' ) {
						print '<a type="button" class="btn btn-info preview-attach-btn btn-action" id="'.$documentid.'" rel="'.$file->getID().'" data-toggle=\"tooltip\" data-placement=\"bottom\" title="'.htmlspecialchars($file->getName()).'"><i class="fa fa-eye"></i></a>';
					} else {
						print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$documentid."&file=".$file->getID()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("view_online")."\"><i class=\"fa fa-eye\"></i></a>";
					}

				}

				if (($document->getAccessMode($user) == M_ALL)||($file->getUserID()==$user->getID()))
					print "<a type=\"button\" class=\"btn btn-danger btn-action\" href=\"out.RemoveDocumentFile.php?documentid=".$documentid."&fileid=".$file->getID()."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("delete")."\"><i class=\"fa fa-remove\"></i></a>";

				print "</div>";		
				print "</td>";			
				print "</tr>";
			}
			print "</tbody>\n</table>\n";	
			print "</div>\n";

		}
		else echo "<p>".getMLText("no_attached_files")."</p>";

		if ($document->getAccessMode($user) >= M_READWRITE){

			print "<a type=\"button\" href=\"../out/out.AddFile.php?documentid=".$documentid."\" class=\"btn btn-success\"><i class=\"fa fa-plus\"></i> ".getMLText("add")."</a>";

		}
			
?>
		</div> <!-- Ends attachment tab -->

		<div class="tab-pane <?php if($currenttab == 'links') echo 'active'; ?>" id="links">
<?php
			//$versionsita=$document->getVersion();
			//print  "<iframe name=\"iFrameDocumento\" id=\"iFrameDocumento\" src=\"http://www.pdf995.com/samples/pdf.pdf\" style=\"height: 500px; width: 100%;\"></iframe>";
?>
			<iframe id="este-es-el-id" src="../pdfviewer/web/viewer.html?file=<?php echo urlencode('../../op/op.Download.php?documentid='.$document->getID().'&version='.$latestContent->getVersion()); ?>" width="100%" height="700px"></iframe>


		

				</div>
			</div>
		</div>
	</div>
</div> <!-- Ends All Tabs -->

<?php
if($user->isAdmin()) 
{
$timeline = $document->getTimeline(); ?>
<div class="row" id="thetimeline">
	<div class="col-md-12">
		<div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?php echo getMLText("timeline"); ?></h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      <div class="box-body">
      	<?php
      		if($timeline) {
						foreach($timeline as &$item) {
							switch($item['type']) {
							case 'add_version':
								$msg = getMLText('timeline_'.$item['type'], array('document'=>$item['document']->getName(), 'version'=> $item['version']));
								break;
							case 'add_file':
								$msg = getMLText('timeline_'.$item['type'], array('document'=>$item['document']->getName()));
								break;
							case 'status_change':
								$msg = getMLText('timeline_'.$item['type'], array('document'=>$item['document']->getName(), 'version'=> $item['version'], 'status'=> getOverallStatusText($item['status'])));
								break;
							default:
								$msg = $this->callHook('getTimelineMsg', $document, $item);
								if(!is_string($msg))
									$msg = '???';
							}
							$item['msg'] = $msg;
						}
		//				$this->printTimeline('out.ViewDocument.php?action=timelinedata&documentid='.$document->getID(), 300, '', date('Y-m-d'));
						$this->printTimelineHtml(300);
					}
				?>
      </div>
	</div>
</div>
</div>
<?php } ?>
		
<?php
		//// Document preview ////
		echo "<div class=\"row div-hidden\" id=\"document-previewer\">";
		echo "<div class=\"col-md-12\">";
		echo "<div class=\"box box-info\">";
		echo "<div class=\"box-header with-border box-header-doc-preview\">";
    echo "<span id=\"doc-title\" class=\"box-title\"></span>";
    echo "<span class=\"pull-right\">";
    //echo "<a class=\"btn btn-sm btn-primary\"><i class=\"fa fa-chevron-left\"></i></a>";
    //echo "<a class=\"btn btn-sm btn-primary\"><i class=\"fa fa-chevron-right\"></i></a>";
    echo "<a class=\"close-doc-preview btn btn-box-tool\"><i class=\"fa fa-times\"></i></a>";
    echo "</span>";
    echo "</div>";
    echo "<div class=\"box-body\">";
    echo "<iframe id=\"iframe-charger\" src=\"\" width=\"100%\" height=\"700px\"></iframe>";
    echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</div>"; // End document preview
		
		echo "</div>"; // Ends content wraper

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
