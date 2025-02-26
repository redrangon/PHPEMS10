{x2;include:header}
<body>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="pages">
            {x2;include:coursenav}
			<div class="content">
				<div class="col-xs-9">
					<div class="content-box padding">
						<h2 class="title">{x2;$content['coursetitle']}</h2>
						<ul class="list-img list-unstyled">
                            <li class="border padding">
								<div class="desc" id="videoPlayer" style="width: 100%;" data-id="{x2;$content['courseid']}"></div>
							</li>
							<li class="border padding">
								<div class="desc">
                                    {x2;realhtml:$content['coursedescribe']}
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-xs-3 nopadding">
					<div class="content-box padding">
						<h2 class="title">
							{x2;$course['cstitle']}
						</h2>
						<ul class="list-unstyled list-txt courselist">
                            {x2;tree:$contents['data'],content,cid}
                            {x2;if:v:content['courseid'] == $content['courseid']}
							<li class="border active">
								<a data-toggle="tooltip" data-placement="top" title="{x2;if:$logs[v:content['courseid']] && $logs[v:content['courseid']]['logstatus'] == 1}已学完{x2;elseif:$logs[v:content['courseid']] && $logs[v:content['courseid']]['logstatus'] == 0}上次学到{x2;eval: echo intval($logs[v:content['courseid']]['logprogress'] / 60)}分{x2;eval: echo $logs[v:content['courseid']]['logprogress'] % 60}秒{x2;else}未学习{x2;endif}" href="index.php?course-app-course&csid={x2;$course['csid']}&contentid={x2;v:content['courseid']}">
									<span class="badge">播放中</span>
                                    {x2;v:content['coursetitle']}
								</a>
							</li>
							{x2;else}
                            {x2;if:$cdata['lock'][v:content['courseid']]}
							<li class="border">
								<a data-toggle="tooltip" data-placement="top" title="必须学完上个课程才能解锁">
									<span class="badge">待解锁</span>
                                    {x2;v:content['coursetitle']}
								</a>
							</li>
							{x2;else}
							<li class="border">
								<a data-toggle="tooltip" data-placement="top" title="{x2;if:$logs[v:content['courseid']] && $logs[v:content['courseid']]['logstatus'] == 1}已学完{x2;elseif:$logs[v:content['courseid']] && $logs[v:content['courseid']]['logstatus'] == 0}上次学到{x2;eval: echo intval($logs[v:content['courseid']]['logprogress'] / 60)}分{x2;eval: echo $logs[v:content['courseid']]['logprogress'] % 60}秒{x2;else}未学习{x2;endif}" href="index.php?course-app-course&csid={x2;$course['csid']}&contentid={x2;v:content['courseid']}">
									<span class="badge{x2;if:$logs[v:content['courseid']] && $logs[v:content['courseid']]['logstatus'] == 1} finish{x2;endif}">待播放</span>
                                    {x2;v:content['coursetitle']}
								</a>
							</li>
                            {x2;endif}
                            {x2;endif}
                            {x2;endtree}
						</ul>
					</div>
				</div>
			</div>
            {x2;include:footer}
		</div>
	</div>
</div>
<script>
$(function(){
    $('[data-toggle="tooltip"]').tooltip();
	var options = {
		source:'{x2;$content['course_files']}',
		poster:'{x2;$course['csthumb']}',
		controls:true,
		loop:false,
		volume:1,
		width:'960px',
		height: '560px',
		language:'zh-CN',
		disablePictureInPicture:true,
		children:{
			"mediaLoader":true,
			"posterImage":true,
			"titleBar":true,
			"textTrackDisplay":true,
			"loadingSpinner":true,
			"bigPlayButton":true,
			"liveTracker":true,
			"controlBar":{
				progressControl:true
			},
			"errorDisplay":true,
			"textTrackSettings":true,
			"resizeManager":true
		}
	};
	var source = document.createElement('source');
	source.setAttribute('src',options.source);
	var video = document.createElement('video');
	video.setAttribute('class', "video-js vjs-default-skin");
	video.setAttribute('webkit-playsinline', 'true');
	video.setAttribute('playsinline', 'true');
	video.appendChild(source);
	document.getElementById('videoPlayer').appendChild(video);
	var player = videojs(video,options);
	{x2;if:$logs[$content['courseid']]['logprogress']}
	player.currentTime({x2;$logs[$content['courseid']]['logprogress']});
	{x2;endif}
	{x2;if:$course['csprogress']}
	var ctime = 0;
	var stime = 0;
	player.on('timeupdate',function(){
		var time = parseInt(player.currentTime());
		ctime = time;
		if(stime == 0)
		{
			stime = ctime;
		}
		else
		{
			if(ctime - stime > 1 || ctime - stime < -1)
			{
				player.currentTime(stime);
				return ;
			}
			else
			{
				stime = ctime;
			}
		}
	})
	{x2;endif}
	$.recordVideo = setInterval(function(){
		$.get('index.php?course-app-course-recordtime&courseid={x2;$content['courseid']}&time='+player.currentTime()+'&userhash='+Math.random());
	},20000);	
	player.on('ended',function(){
		$.get('index.php?course-app-course-endstatus&courseid={x2;$content['courseid']}&'+Math.random(),function(){
			window.location.reload();
		});
	})
});
</script>
</body>
</html>