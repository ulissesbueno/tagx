<?php

include_once( 'tagx.class.php' );
ob_start();
?>
		<div class='row'>
	            <cols>
	                <div class='col-md-{wcol} {name}'>
	                    <div class="form-group ">
	                    
	                        <label class="control-label">
	                        
	                        {label}
	                        
	                        <if name='require'>
	                            <condition value='yes'>
	                                <span class="required">*</span>
	                            </condition>
	                        </if>

	                        </label> 
	                        
	                        <if name='type'>
	                            
	                            <condition value='email'>
	                                <div class="input-group">
	                                    <span class="input-group-addon">
	                                        <i class="fa fa-envelope"></i>
	                                    </span>
	                                    <input type="text" placeholder="{label}" class="form-control email" id="{name}" name="{table}__tb__{name}" value="[!{name}!]">
	                                </div>
	                            </condition>
	                            
	                            <condition value='onlyready' >
	                                <div class='well field'>[!{name}!]</div>
	                            </condition>
	                            
	                            <condition value='text' >
	                                <input type="text" placeholder="{label}" class="form-control {class}" id="{name}" name="{table}__tb__{name}" value="[!{name}!]" format="{format}" action="{action}" callback="{callback}">
	                            </condition>
	                            
	                            <condition value='file' >
	                                <input type="file" placeholder="{label}" id="{name}" name="{table}__tb__{name}" value="[!{name}!]" action="{action}" callback="{callback}">
	                            </condition>
	                            
	                            <condition value='name' >
	                                <input type="text" placeholder="{label}" class="form-control" id="{name}" name="{table}__tb__{name}" value="[!{name}!]" >
	                            </condition>
	                            
	                            <condition value='select'>
	                                <select class="form-control {class}" id="{name}" name="{table}__tb__{name}" init="[!{name}!]" >
	                                    <option value=''></option>
	                                    <options>
	                                        <option value='{value}' {selected} >{text}</option>
	                                    </options>
	                                </select>
	                            </condition>

	                            <condition value='textarea'>
	                                <textarea rows="3" class="form-control" id="{name}" name="{table}__tb__{name}">[!{name}!]</textarea>
	                            </condition>
	                            
	                            <condition value='switch'>
	                                <input type="checkbox" {checked} value="{value}" id="{name}" name="{table}__tb__{name}" />
	                            </condition>
	                            
	                        </if>
	                        
	                        <if name='note'>
	                            <condition value='' op='!='>
	                                <span class="help-block">
	                                     {note}
	                                </span>
	                            </condition>                                                        
	                        </if>
	                        
	                    </div>
	                </div>                                                
	            </cols>
	    </div>
<?php

$html = ob_get_contents();
ob_clean();

?>
<style type="text/css">
	*{
		font-size: 11px;
		font-family: courier;
	}
</style>
<?php

$tagx = new tagx( $html );
$tagx->write();
echo htmlentities( $tagx->output );
$tagx->echo_ar();
?>