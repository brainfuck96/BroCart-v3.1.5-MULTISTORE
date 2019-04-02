<h2><?php echo $text_instruction; ?></h2>
<p><b><?php echo $text_description; ?></b></p>
<div class="card">
  <div class="card-body">
    <p><?php echo $bank; ?></p>
    <p><?php echo $text_payment; ?></p>
  </div>
</div>
<div class="d-inline-block pt-2 pd-2 w-100">
  <div class="float-right">
    <button type="button" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_confirm; ?></button>
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/payment/bank_transfer/confirm',
		dataType: 'json',
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function(json) {
			if (json['redirect']) {
				location = json['redirect'];
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
//--></script>
