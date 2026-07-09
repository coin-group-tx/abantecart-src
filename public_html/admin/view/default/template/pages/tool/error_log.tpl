<?php include($tpl_common_dir . 'action_confirm.tpl'); 

?>

<div class="tab-content">
	<div class="panel-heading">
        <div class="primary_content_actions pull-left">            
            <?php if($log_list){ ?>
                <div class="input-group-prepend">
                    <?php
                    echo $this->html->buildElement(
                        [
                            'type'  => 'button',
                            'name'  => 'download',
                            'icon'  => 'fa fa-download',
                            'title' => $this->language->get('button_download'),
                            'style' => 'btn btn-info',                            
                        ]
                    );
                    ?>
                </div>
                <div class="btn-group">
                    <?php echo $log_list; ?>
                </div>
                <?php
            }
            if($button_clear){ ?>
                <div class="btn-group">
                    <a href="<?php echo $clear_url; ?>" class="btn btn-primary lock-on-click" id="clear"><i
                                class="fa fa-trash"></i> <?php echo $button_clear; ?></a>
                </div>
            <?php } ?>
        </div>
	</div>

	<div class="panel-body panel-body-nopadding">
		<div class="error-log">
		<?php if( $log ) { ?>
			<table class="table table-striped">
			<?php
				foreach($log as $line){ ?>
					<tr><td><?php echo $line; ?></td></tr>
			<?php } ?>
			</table>
		<?php } else { ?>
			<div class="text-center">
			<h1><i class="fa fa-thumbs-o-up fa-lg"></i></h1>
			</div>
		<?php } ?>
		</div>
	</div>
</div>
<script type="text/javascript">
    $('select#filename').on('change', function(){
        location = '<?php echo $main_url; ?>&filename='+ $(this).val();
    });
    
    $('#download').on('click', function(e){
        e.preventDefault();
        const filename = $('select#filename').val();
        if(filename){
            location = '<?php echo $download_url; ?>&filename='+ filename;
        } else {
            warning_alert('Please select a file to download');
        }
    });    
</script>
