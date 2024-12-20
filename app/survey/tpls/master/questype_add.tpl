{x2;if:!$userhash}
{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-2 leftmenu">
				{x2;include:menu}
			</div>
			<div id="datacontent">
{x2;endif}
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?{x2;$_app}-master">{x2;$apps[$_app]['appname']}</a></li>
							<li><a href="index.php?{x2;$_app}-master-questype">题型管理</a></li>
							<li class="active">添加题型</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						添加题型
						<a class="btn btn-primary pull-right" href="index.php?{x2;$_app}-master-questype">题型管理</a>
					</h4>
			        <form action="index.php?{x2;$_app}-master-questype-add" method="post" class="form-horizontal">
						<fieldset>
						<div class="form-group">
							<label for="questype" class="control-label col-sm-2">题型名称：</label>
							<div class="col-sm-4">
								<input class="form-control" name="args[questype]" id="questype" type="text" size="30" value="" needle="needle" alt="请输入题型名称" />
							</div>
						</div>
						<div class="form-group">
							<label for="questsort" class="control-label col-sm-2">题型分类：</label>
							<div class="col-sm-4">
								<select class="combox form-control" id="questsort" name="args[questsort]" onchange="javascript:$('.choicebox').toggle();">
									<option value="1">主观题</option>
		  							<option value="0">客观题</option>
								</select>
							</div>
						</div>
						<div id="choicebox" style="display:none;" class="form-group choicebox">
							<label for="questchoice" class="control-label col-sm-2">答题方式：</label>
							<div class="col-sm-10">
								<label class="radio-inline">
									<input name="args[questchoice]" type="radio" value="1"> 单选
								</label>
								<label class="radio-inline">
									<input name="args[questchoice]" type="radio" value="2"> 多选
								</label>
								<label class="radio-inline">
									<input name="args[questchoice]" type="radio" value="4"> 判断
								</label>
							</div>
						</div>
						<div class="form-group">
						  	<label for="questchoice" class="control-label col-sm-2"></label>
						  	<div class="col-sm-9">
							  	<button class="btn btn-primary" type="submit">提交</button>
							  	<input type="hidden" name="page" value="{x2;$page}"/>
							  	<input type="hidden" name="addquestype" value="1"/>
							</div>
						</div>
						</fieldset>
					</form>
				</div>
			</div>
{x2;if:!$userhash}
		</div>
	</div>
</div>
{x2;include:footer}
</body>
</html>
{x2;endif}