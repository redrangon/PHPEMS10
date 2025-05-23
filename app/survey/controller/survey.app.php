<?php
 namespace PHPEMS;
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class action extends app
{
	public function display()
	{
		$this->basic = ginkgo::make('basic','exam');
		$this->config = \PHPEMS\ginkgo::make('config','survey');
		$this->tpl->assign('cities',$this->config->cities);
		$action = $this->ev->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}
	
	/**
	 *
	 * history值存在，用成功页面，不存在，用失败页面
	 *
	 **/
	public function view()
	{		
		$syhyid = $this->ev->get('syhyid');
		$history = $this->survey->getSurveyHistoryById($syhyid);
		$svyid = $history['syhysvyid'];
		$survey = $this->survey->getSurveyById($svyid);
		$nodes = $this->survey->getSurveyNodesByArgs(array(
			array("AND","synesvyid = :synesvyid","synesvyid",$svyid)
		));
		$questions = array();
		foreach($nodes as $node)
		{
			$questions[$node['syneid']] = $this->survey->getSurveyQuestionsByArgs(array(
				array("AND","syqnsyneid = :syqnsyneid","syqnsyneid",$node['syneid'])
			));
		}
		$questypes = $this->basic->getQuestypeList();
		$this->tpl->assign('questypes',$questypes);
		$this->tpl->assign('nodes',$nodes);
		$this->tpl->assign('questions',$questions);
		$this->tpl->assign('survey',$survey);
		$this->tpl->display('survey_view');
	}
	
	public function paper()
	{
		$svyid = $this->ev->get('svyid');
		$survey = $this->survey->getSurveyById($svyid);
		if($survey['svyendtime'] < TIME)
		{
			$message = array(
				'statusCode' => 300,
				"message" => "操作失败，问卷已过期",
				"callbackType" => 'forward',
				"forwardUrl" => "index.php?survey-app-survey"
			);
			ginkgo::R($message);
		}
		if($survey['svytype'])
		{
			$rs = $this->survey->getSurveyHistoryByArgs(array(
				array("AND", "syhyuserid = :syhyuserid", "syhyuserid", $this->_user['sessionuserid']),
				array("AND", "syhysvyid = :syhysvyid", "syhysvyid", $survey['syhyid'])
			));
			$this->tpl->assign('rs',$rs);
		}
		if($this->ev->get('submitpaper'))
		{
			$args = array();
			if($survey['svytype'])
			{
				$args['syhyuserid'] = $this->_user['sessionuserid'];
			}
			$questions = $this->ev->get('question');
			$args['syhysvyid'] = $svyid;
			$args['syhyanswers'] = $questions;
			$args['syhytime'] = TIME;
			$syhyid = $this->survey->addSurveyHistory($args);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功，正在转入目标页面",
			    "callbackType" => 'forward',
			    "forwardUrl" => "index.php?survey-app-survey-view&syhyid={$syhyid}"
			);
			exit(json_encode($message));			
		}
		else
		{
			$nodes = $this->survey->getSurveyNodesByArgs(array(
				array("AND","synesvyid = :synesvyid","synesvyid",$svyid)
			));
			$questions = array();
			foreach($nodes as $node)
			{
				$questions[$node['syneid']] = $this->survey->getSurveyQuestionsByArgs(array(
					array("AND","syqnsyneid = :syqnsyneid","syqnsyneid",$node['syneid'])
				));
			}
			$questypes = $this->survey->getQuestypeList();
			$this->tpl->assign('questypes',$questypes);
			$this->tpl->assign('nodes',$nodes);
			$this->tpl->assign('questions',$questions);
			$this->tpl->assign('survey',$survey);
			$this->tpl->display('survey_paper');
		}
	}

	public function index()
	{
		$page = intval($this->ev->get('page'));
		$args = array();
		$args[] = array("AND","svyendtime > :syendtime","syendtime",TIME);
		$surveies = $this->survey->getSurveyList($args,$page);
		$this->tpl->assign('surveies',$surveies);		
		$this->tpl->display('survey');
	}
}


?>
