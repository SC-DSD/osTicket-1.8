<?php
require('staff.inc.php');
require_once(INCLUDE_DIR.'class.leave.php');

$nav->setTabActive('calendar');
require(STAFFINC_DIR.'header.inc.php');

if($thisstaff->getGroupId() == 5 || $thisstaff->getGroupId() == 6){
	header('Location: tickets.php');
}

$dltsrea = db_query('SELECT * FROM '. CONFIG_TABLE);
while ($row = db_fetch_array($dltsrea)) {
	$conf[] = $row;
}
?>

<h1>Staff Availability</h1>
<div class="clear"><br/><br/></div>
<?php
$dltsres = db_query('SELECT * FROM '. STAFF_TABLE .' staff WHERE group_id = 5 AND isactive = 1 ORDER BY firstname ASC');
	while ($row = db_fetch_array($dltsres)) {
		$ngng[] = $row;
}

foreach($ngng as &$ng){
	$tktres = db_query('SELECT * FROM '. TICKET_TABLE.' ticket WHERE ticket.staff_id='.$ng['staff_id'].' AND status != "closed"');	
	$tk = array();
	while ($row2 = db_fetch_array($tktres)) {
		$tk[] = $row2;
	}
	$ng['ticket'] = $tk;
}

/*foreach($ngng as &$ng){
	$lvs = db_query('SELECT * FROM ost_leave WHERE staff_id='.$ng['staff_id'].' AND start_date <= NOW() AND end_date + 23:59:59.997 >= NOW() AND leave_type != 

"Statutory"');	
	$lv = array();
	while ($row3 = db_fetch_array($lvs)) {
		$lv[] = $row3;
	}
	$ng['leaves'] = $lv;
	var_dump($ng['leaves']);
}*/

$yellow = 0;
$red = 0;

foreach($ngng as &$ng){
	$ng['yellow'] = $yellow;
	$ng['red'] = $red;
	if(Leave::isAway($ng['staff_id'])){
		$ng['red']++;
	} else {
		for($i = 0; $i < count($ng['ticket']);$i++){
			if($ng['ticket'][$i]['status'] == "approval" || $ng['ticket'][$i]['status'] == "approved"
				|| $ng['ticket'][$i]['status'] == "qa_preview" || $ng['ticket'][$i]['status'] == "qa_assignment" || $ng['ticket'][$i]['status'] == 

"qa_preliminary"){
				$ng['yellow']++;
			}
			if($ng['ticket'][$i]['status'] == "progress"){
				$ng['red']++;
			}
		}
	}
}



$j = 0;
echo "<span class='float-left' style='margin-right:100px; margin-left:95px;'><h3>Available</h3><br/>";
foreach($ngng as &$ng){
	$j++;
	$nbtickets = count($ng['ticket']);
	if($ng['red'] == 0 && $ng['yellow'] == 0){
		echo '<a href="#tooltip_box-'.$j.'" class="tooltip_btn"><span style=color:green>'.$ng['firstname'].' '.$ng['lastname'].'</span> ' . ($nbtickets ? 

'('.$nbtickets.')' : '') . '</a>';
		
		echo'<div id="tooltip_box-'.$j.'" class="tooltip_box"><p class="group_title">Tickets assigned</p><ul>';
		if(!$nbtickets)
			echo'<li><em class="light">None</em></li>';
		else {
			for($i = 0; $i < $nbtickets;$i++){
				echo'<li><a target="_blank" href=tickets.php?id='.$ng['ticket'][$i]['ticket_id'].' class="Icon otherTicket ticketPreview" 

title="Preview Ticket">'.$ng['ticket'][$i]['ticketID'].'</a> &rarr; '.$ng['ticket'][$i]['status'].'</li>';
			}
		}
		echo"</ul></div>";	
	}
}
echo "</span>";

echo "<span class='float-left'  style='margin-right:150px;''><h3>On Stand By</h3><br/>";
foreach($ngng as &$ng){
	$j++;
	$nbtickets = count($ng['ticket']);
	if($ng['red'] == 0 && $ng['yellow']){
		echo '<a href="#tooltip_box-'.$j.'" class="tooltip_btn"><span style=color:#ff6400>'.$ng['firstname'].' '.$ng['lastname'].'</span> ' . ($nbtickets ? 

'('.$nbtickets.')' : '') . '</a>';
		
		echo'<div id="tooltip_box-'.$j.'" class="tooltip_box"><p class="group_title">Tickets assigned</p><ul>';
		if(!$nbtickets)
			echo'<li><em class="light">None</em></li>';
		else {
			for($i = 0; $i < $nbtickets;$i++){
				echo'<li><a target="_blank" href=tickets.php?id='.$ng['ticket'][$i]['ticket_id'].' class="Icon otherTicket ticketPreview" 

title="Preview Ticket">'.$ng['ticket'][$i]['ticketID'].'</a> &rarr; '.$ng['ticket'][$i]['status'].'</li>';
			}
		}
		echo"</ul></div>";
	}
}
echo "</span>";

echo "<span class='float-left'><h3>Unavailable</h3><br/>";
foreach($ngng as &$ng){
	$j++;
	$nbtickets = count($ng['ticket']);
	if($ng['red'] > 0){
		echo '<a href="#tooltip_box-'.$j.'" class="tooltip_btn"><span style=color:red>'.$ng['firstname'].' '.$ng['lastname'].'</span> ' . ($nbtickets ? 

'('.$nbtickets.')' : '') . '</a>';
		
		echo'<div id="tooltip_box-'.$j.'" class="tooltip_box"><p class="group_title">Tickets assigned</p><ul>';
		if(!$nbtickets)
			echo'<li><em class="light">None</em></li>';
		else {
			for($i = 0; $i < $nbtickets;$i++){
				echo'<li><a target="_blank" href=tickets.php?id='.$ng['ticket'][$i]['ticket_id'].' class="Icon otherTicket ticketPreview" 

title="Preview Ticket">'.$ng['ticket'][$i]['ticketID'].'</a> &rarr; '.$ng['ticket'][$i]['status'].'</li>';
			}
		}
		echo"</ul></div>";
	} 
}
echo "</span><div class='clear'><br/><br/><br/><br/><br/><br/><br/></div>";
?>

<!-- JQuery Simple Effects Import -->
<script type="text/javascript" src="js/simpleEffects.js"></script>

<?php
include(STAFFINC_DIR.'footer.inc.php');
?>