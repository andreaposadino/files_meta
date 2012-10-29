<div id="meta">

<?php

if( isset( $_['message'] ) ) {


	if( isset($_['path'] ) ) echo('<strong>File: '.$_['path'] ).'</strong><br>';
	echo('<strong>'.$_['message'] ).'</strong><br>';

}else{

echo ("Some Metadata goes here");
}

?>
</div>
