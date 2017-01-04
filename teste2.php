<?php

require_once( 'class/tagx.class.php' );

ob_start();
?>
		<div>
			<h4 id='title' class='title outher'></h4>
			<p></p>
		</div>
<?php
$html = ob_get_contents();
ob_clean();

?>
<style type="text/css">
	*{
		font-size: 12px;
		font-family: courier;
	}
</style>
<?php

$tagx = new tagx( $html );
//$tagx->write($data);
$tagx	->Selector( '#title')->html('Title Page')
		->output();



?>