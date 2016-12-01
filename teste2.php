<?php

include_once( 'tagx.class.php' );
ob_start();
?>
		<div>
			<h4>{title}</h4>
			<ul>
			<category>
				<li>{name}
					<subategory>
						<ul>
							<li>{name}</li>
						</ul>
					</subategory>
				</li>
			</category>
			</ul>
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

$data = array(	'title'=> 'Produtos', 
				'description' => 'sdf sdf sdgf sdg sdggkajhajsghgt khfdgshgd khsd gs',
				'Nome'=>'Ulisses', 'Idade'=> '35 anos','Description'=>'oi',
				'options' => array( array('value'=>'1', 'text' => 'Cliente'),
									array('value'=>'2', 'text' => 'Empresa') ),
				'category'	=> 	array( 	array( 'name' 	=> 'Pizza', 'subategory' => array( 	array('name'=>'Mussarela'),
																							array('name'=>'Calabreza') ), 'tam' => "Grande" ),
										array( 'name' 	=> 'Lanche' ) ) );

$tagx = new tagx( $html );

$tagx->write($data);
$tagx->Selector( 'category > subategory' );


/*echo $tagx->output;
echo "<hr>";
echo htmlentities( $tagx->output );
$tagx->echo_ar();*/
//
?>