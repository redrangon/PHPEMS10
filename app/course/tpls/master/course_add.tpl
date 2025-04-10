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
							<li><a href="index.php?{x2;$_app}-master-course&page={x2;$page}">课程管理</a></li>
							<li class="active">添加课程</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						添加课程
						<a class="btn btn-primary pull-right" href="index.php?{x2;$_app}-master-course&page={x2;$page}{x2;$u}">课程管理</a>
					</h4>
					<form action="index.php?course-master-course-add" method="post" class="form-horizontal">
						<div class="form-group">
				            <label for="contenttitle" class="control-label col-sm-2">课程标题：</label>
				            <div class="col-sm-9">
							    <input class="form-control" type="text" id="contenttitle" name="args[cstitle]" needle="needle" msg="您必须输入标题">
					        </div>
				        </div>
				        <div class="form-group">
				            <label for="block" class="control-label col-sm-2">缩略图：</label>
				            <div class="col-sm-9">
					            <script type="text/template" id="pe-template-csthumb">
						    		<div class="qq-uploader-selector" style="width:30%" qq-drop-area-text="可将图片拖拽至此处上传" style="clear:both;">
						            	<div class="qq-upload-button-selector" style="clear:both;">
						                	<ul class="qq-upload-list-selector list-unstyled" aria-live="polite" aria-relevant="additions removals" style="clear:both;">
								                <li class="text-center">
								                    <div class="thumbnail">
														<img class="qq-thumbnail-selector" alt="点击上传新图片">
														<input type="hidden" class="qq-edit-filename-selector" name="args[csthumb]" tabindex="0">
													</div>
								                </li>
								            </ul>
								            <ul class="qq-upload-list-selector list-unstyled" aria-live="polite" aria-relevant="additions removals" style="clear:both;">
									            <li class="text-center">
									                <div class="thumbnail">
														<img class="qq-thumbnail-selector" src="app/core/styles/images/noimage.gif" alt="点击上传新图片">
														<input type="hidden" class="qq-edit-filename-selector" name="args[csthumb]" tabindex="0" value="app/core/styles/images/noimage.gif">
						                			</div>
									            </li>
									        </ul>
						                </div>
						            </div>
						        </script>
						        <div class="fineuploader" attr-type="thumb" attr-template="pe-template-csthumb"></div>
							</div>
				        </div>
				        <div class="form-group">
				            <label for="contentcatid" class="control-label col-sm-2">分类：</label>
				        	<div class="col-sm-9 form-inline">
							    <select id="cscatid" msg="您必须选择一个分类" needle="needle" class="autocombox form-control" name="args[cscatid]" refUrl="index.php?course-master-category-ajax-getchildcategory&catid={value}">
					            	<option value="">选择一级分类</option>
					            	{x2;tree:$parentcat,cat,cid}
					            	<option value="{x2;v:cat['catid']}">{x2;v:cat['catname']}</option>
					            	{x2;endtree}
					            </select>
					        </div>
				        </div>
				        <div class="form-group">
				            <label for="contentcatid" class="control-label col-sm-2">科目：</label>
				        	<div class="col-sm-9 form-inline">
							    <select id="cscatid" msg="请选择科目" needle="needle" class="form-control" name="args[cssubjectid]">
					            	<option value="">请选择科目</option>
					            	{x2;tree:$subjects,subject,sid}
					            	<option value="{x2;v:subject['subjectid']}">{x2;v:subject['subject']}</option>
					            	{x2;endtree}
					            </select>
					            <a href="index.php?exam-master-basic-subject" class="btn btn-primary" target="_blank">编辑科目</a>
					        </div>
				        </div>
				        <div class="form-group">
				            <label for="contentdescribe" class="control-label col-sm-2">简介：</label>
				            <div class="col-sm-10">
							    <textarea id="contentdescribe" name="args[csdescribe]" class="form-control" rows="4"></textarea>
					        </div>
				        </div>
						<div class="form-group">
							<label class="control-label col-sm-2">学习模式</label>
							<div class="col-sm-9">
								<label class="radio-inline">
									<input name="args[cstype]" type="radio" value="0" {x2;if:!$course['cstype']}checked{x2;endif}/>开放模式
								</label>
								<label class="radio-inline">
									<input name="args[cstype]" type="radio" value="1" {x2;if:$course['cstype']}checked{x2;endif}/>解锁模式
								</label>
							</div>
						</div>
				        <div class="form-group">
							<label class="control-label col-sm-2">免费课程</label>
							<div class="col-sm-9">
								<label class="radio-inline">
									<input name="args[csdemo]" type="radio" value="1" {x2;if:$course['csdemo']}checked{x2;endif}/>是
								</label>
								<label class="radio-inline">
									<input name="args[csdemo]" type="radio" value="0" {x2;if:!$course['csdemo']}checked{x2;endif}/>否
								</label>
								<span class="help-block">免费课程无须开通即可学习</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">进度控制</label>
							<div class="col-sm-9">
								<label class="radio-inline">
									<input name="args[csprogress]" type="radio" value="0" {x2;if:!$course['csprogress']}checked{x2;endif}/> 允许调节进度
								</label>
								<label class="radio-inline">
									<input name="args[csprogress]" type="radio" value="1" {x2;if:$course['csprogress']}checked{x2;endif}/> 禁止调节进度
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="basicprice" class="control-label col-sm-2">价格设置</label>
							<div class="col-sm-9">
								<textarea class="form-control" rows="4" name="args[csprice]" id="csprice">{x2;$course['csprice']}</textarea>
							  	<span class="help-block">请按照“时长:开通所需积分”格式填写，每行一个，时长以天为单位，免费课程此设置无效。</span>
							</div>
						</div>
				        <div class="form-group">
				            <label for="contentdescribe" class="control-label col-sm-2"></label>
				            <div class="col-sm-9">
					            <button class="btn btn-primary" type="submit">提交</button>
					            <input type="hidden" name="submit" value="1">
					        </div>
				        </div>
					</form>
				</div>
			</div>
{x2;if:!$userhash}
		</div>
	</div>
</div>
</body>
</html>
{x2;endif}