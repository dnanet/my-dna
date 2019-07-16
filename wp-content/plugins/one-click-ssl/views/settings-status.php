<!-- One Click SSL Settings General -->

<?php

$certinfo = $this -> get_certificate_info();

?>

<?php if (!empty($certinfo['isvalid'])) : ?>
	<div style="border:1px #cccccc solid; padding:15px; display:inline-block;">
		<div style="float:left; margin:0 15px 0 0; font-size:100px;">
			<i class="fa fa-lock fa-fw"></i>
		</div>
		<div style="float:left;">
			<p class="">
				<span style="font-weight:bold; font-size:150%;"><?php echo $certinfo['domain']; ?></span><br/>
				<?php echo sprintf(__('Issued by: %s', 'one-click-ssl'), $certinfo['issuer']); ?><br/>
				<?php echo sprintf(__('Expires: %s', 'one-click-ssl'), $this -> gen_date(false, strtotime($certinfo['expiry']))); ?><br/>
				<span class="one-click-ssl-success"><i class="fa fa-check fa-fw"></i> <?php _e('Certificate is valid', 'one-click-ssl'); ?></span>
			</p>
		</div>
		<br class="clear" />
	</div>
<?php else : ?>	
	<div style="border:1px #cccccc solid; padding:15px; display:inline-block;">
		<div style="float:left; margin:0 15px 0 0; font-size:100px;">
			<i class="fa fa-unlock-alt fa-fw"></i>
		</div>
		<div style="float:left;">
			<p class="">
				<span style="font-weight:bold; font-size:150%;"><?php echo $certinfo['domain']; ?></span><br/>
				<?php echo $certinfo['message']; ?><br/>
				<span class="one-click-ssl-error"><i class="fa fa-times fa-fw"></i> <?php _e('Certificate is not valid', 'one-click-ssl'); ?></span>
			</p>
		</div>
		<br class="clear" />
	</div>
<?php endif; ?>	