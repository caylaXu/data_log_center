<?php include('layout/header.php'); ?>

<!--Page Related styles-->
<link href="/resource/css/dataTables.bootstrap.css" rel="stylesheet"/>

<?php include('layout/body_top.php'); ?>

<!-- Page Content -->
<div class="page-content">
	<div class="page-header position-relative">
		<div class="header-title">
			<h1>
				配置文件下载
			</h1>
		</div>
		<!--Header Buttons-->
		<div class="header-buttons">
			<a class="sidebar-toggler" href="#">
				<i class="fa fa-arrows-h"></i>
			</a>
			<a class="refresh" id="refresh-toggler" href="">
				<i class="glyphicon glyphicon-refresh"></i>
			</a>
			<a class="fullscreen" id="fullscreen-toggler" href="#">
				<i class="glyphicon glyphicon-fullscreen"></i>
			</a>
		</div>
		<!--Header Buttons End-->
	</div>
	<!-- Page Body -->
	<div class="page-body">
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="widget">
					<div class="widget-body">
						<div id="download-form">
							<div class="form-group">
								系统：
								<select type="text" id="system_id">
									<?php if (is_array($systems) && count($systems) > 0)
									{ ?>
										<?php foreach ($systems as $system): ?>
										<option value="<?php echo $system['Id']; ?>"
											<?php echo (isset($system_id) && $system_id === intval($system['Id'])) ? 'selected' : ''; ?>>
											<?php echo $system['Name']; ?>
										</option>
									<?php endforeach ?>
									<?php }
									else
									{ ?>
										<option value="0">未定义</option>
									<?php } ?>
								</select>
							</div>
							<a id="config_download" href="#" class="btn btn-primary">下载</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Page Body -->
</div>

<!-- /Page Content -->

<?php include('layout/scripts.php'); ?>

<!--Page Related Scripts-->
<script>
	$(document).ready(function ()
	{
		//下载
		$('#config_download').click(function ()
		{
			var system_id = $('#system_id').val();
			location.href = '/system/do_download?system_id=' + system_id;
		});

		/*$('#config_download').bind('click', function ()
		{
			var system_id = $('#system_id').val();
			var param = {system_id: system_id};
			$.get('/system/do_download', param, function (data){});
		});*/
	});
</script>