<?php
 namespace PHPEMS;

class api_content
{
	public $G;

	public function __construct()
	{
		
	}

	public function _init()
	{
		$this->sql = \PHPEMS\ginkgo::make('sql');
		$this->pdosql = \PHPEMS\ginkgo::make('pdosql');
		$this->db = \PHPEMS\ginkgo::make('pepdo');
		$this->tpl = \PHPEMS\ginkgo::make('tpl');
		$this->pg = \PHPEMS\ginkgo::make('pg');
		$this->ev = \PHPEMS\ginkgo::make('ev');
		$this->files = \PHPEMS\ginkgo::make('files');
		$this->category = \PHPEMS\ginkgo::make('category');
		$this->content = \PHPEMS\ginkgo::make('content','content');
		$this->block = \PHPEMS\ginkgo::make('block','content');
		$this->tpl_var = &$this->tpl->tpl_var;
	}

	private function _getBlockContentList($args)
	{
		$catids = $this->category->getChildCategoryString($args['catid']);
		if($args['query'])
		$query = explode("\n",str_replace("\r",'',html_entity_decode($this->ev->stripSlashes($args['query']))));
		else $query = array();
		$query[] = array("AND","find_in_set(contentcatid,:contentcatid)",'contentcatid',$catids);
		$r = $this->content->getContentList($query,1,$args['number']);
		return $r['data'];
	}

	public function parseBlock($blockid)
	{
		$block = $this->block->getBlockById($blockid);
		if($block['blocktype'] == 1)
		{
			echo html_entity_decode($block['blockcontent']['content']);
		}
		elseif($block['blocktype'] == 2)
		{
			if($block['blockcontent']['app'] == 'content')
			{
				$args = array('catid'=>$block['blockcontent']['catid'],'number'=>$block['blockcontent']['number'],'query'=>$block['blockcontent']['query']);
				$blockdata = $this->_getBlockContentList($args);
				$tp = $this->tpl->fetchContent(html_entity_decode($this->ev->stripSlashes($block['blockcontent']['template'])));
				$blockcat = $this->category->getCategoryById($block['blockcontent']['catid']);
				$blockcatchildren = $this->category->getCategoriesByArgs(array(array("AND","catparent = :catparent",'catparent',$block['blockcontent']['catid'])));
				eval(' ?>'.$tp.'<?php
 namespace PHPEMS; ');
			}
			else
			{
				$args = array('catid'=>$block['blockcontent']['catid'],'number'=>$block['blockcontent']['number'],'query'=>$block['blockcontent']['query']);
				$obj = \PHPEMS\ginkgo::make('api',$block['blockcontent']['app']);
				if(method_exists($obj,'parseBlock'))
				$blockdata = $obj->parseBlock($args);
				else
				return false;
			}
			return true;
		}
		elseif($block['blocktype'] == 3)
		{
			if($block['blockcontent']['sql'])
			{
				$sql = array('sql' => str_replace('[TABLEPRE]',DTH,$block['blockcontent']['sql']));
			}
			else
			{
				$tables = array_filter(explode(',',$block['blockcontent']['dbtable']));
				$querys = array_filter(explode("\n",str_replace("\r","",html_entity_decode($this->ev->stripSlashes($block['blockcontent']['query'])))));
				$args = array();
				foreach($querys as $p)
				{
					$a = explode('|',$p);
					if($a[3])
					{
						if($a[3][0] == '$')
						{
							$s = stripos($a[3],'[');
							$k = substr($a[3],1,$s-1);
							$v = substr($a[3],$s,(strlen($a[3]) - $s));
							$execode = "\$a[3] = \"{\$this->tpl_var['$k']$v}\";";
						}
						else
						{
							$k = substr($a[3],2,(strlen($a[3]) - 2));
							$execode = "\$a[3] = \"{\$$k}\";";
						}
						eval($execode);
					}
					$args[] = $a;
				}

				$data = array(false,$tables,$args,false,$block['blockcontent']['order'],$block['blockcontent']['limit']);
				$sql = $this->pdosql->makeSelect($data);
			}
			$blockdata = $this->db->fetchAll($sql,$block['blockcontent']['index']?$block['blockcontent']['index']:false,$block['blockcontent']['serial']?$block['blockcontent']['serial']:false);
			$tp = $this->tpl->fetchContent(html_entity_decode($this->ev->stripSlashes($block['blockcontent']['template'])));
			eval(' ?>'.$tp.'<?php
 namespace PHPEMS; ');
			return true;
		}
		elseif($block['blocktype'] == 4)
		{
			$tp = $this->tpl->fetchContent(html_entity_decode($this->ev->stripSlashes($block['blockcontent']['content'])));
			eval(' ?>'.$tp.'<?php
 namespace PHPEMS; ');
		}
		else
		return false;
	}
}

?>
