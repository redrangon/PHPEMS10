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
		$action = $this->ev->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}

	private function rows()
	{
		$page = $this->ev->get('page');
		$page = $page > 0?$page:1;
		$args = array(array("AND","questionrows.qrstatus = '0'"),array("AND","questionrows.qrid = quest2knows.qkquestionid"),array("AND","quest2knows.qktype = 1"));
		$knows = $this->section->getAllKnowsBySubjects($this->teachsubjects);
		foreach($knows as $p)
		{
			if($p['knowsid'])$tmpknows .= ','.$p['knowsid'];
		}
		$tmpknows = trim($tmpknows,', ');
		if(!$tmpknows)$tmpknows = 0;
		$args[] = array('AND',"find_in_set(quest2knows.qkknowsid,:qkknowsids)","qkknowsids",$tmpknows);
		$questypes = $this->basic->getQuestypeList();
		$questions = $this->exam->getQuestionrowsList($page,20,$args);
		$this->tpl->assign('page',$page);
		$this->tpl->assign('questypes',$questypes);
		$this->tpl->assign('questions',$questions);
		$this->tpl->display('recyle_rowsquestions');
	}

	private function finaldelquestion()
	{
		$page = $this->ev->get('page');
		$questionid = $this->ev->get('questionid');
		$this->exam->fanalDelQuestions($questionid);
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
		    "forwardUrl" => "index.php?exam-teach-recyle&page={$page}"
		);
		exit(json_encode($message));
	}

	private function finaldelrowsquestion()
	{
		$page = $this->ev->get('page');
		$questionid = $this->ev->get('questionid');
		$this->exam->finalDelQuestionRows($questionid);
		$this->exam->fanalDelQuestionsByArgs("questionparent = '{$questionid}'");
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
		    "forwardUrl" => "index.php?exam-teach-recyle-rows&page={$page}"
		);
		exit(json_encode($message));
	}

	private function index()
	{
		$page = $this->ev->get('page');
		$page = $page > 0?$page:1;
		$args = array(array("AND","quest2knows.qkquestionid = questions.questionid"),array("AND","questions.questionstatus = '0'"),array("AND","questions.questionparent = 0"),array("AND","quest2knows.qktype = 0") );
		$knows = $this->section->getAllKnowsBySubjects($this->teachsubjects);
		foreach($knows as $p)
		{
			if($p['knowsid'])$tmpknows .= ','.$p['knowsid'];
		}
		$tmpknows = trim($tmpknows,', ');
		if(!$tmpknows)$tmpknows = 0;
		$args[] = array("AND","find_in_set(quest2knows.qkknowsid,:qkknowsid)",'qkknowsid',$tmpknows);
		$questypes = $this->basic->getQuestypeList();
		$questions = $this->exam->getQuestionsList($page,20,$args);
		$this->tpl->assign('page',$page);
		$this->tpl->assign('questypes',$questypes);
		$this->tpl->assign('questions',$questions);
		$this->tpl->display('recyle_questions');
	}
}


?>
