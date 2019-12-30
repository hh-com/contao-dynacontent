<?php
namespace Hhcom\ContaoDynaContent;
use Contao\Database\Result;
use Contao\Model;
use Doctrine\Common\Collections\Collection;
#use Doctrine\Collections;


class DynaContent extends \Controller
{
	protected $strTemplate = 'mod_dynacontent';
	public static $pageList = array();
	
	
	public function generate()
    {
		
		$message = array('error'=> false);
		$objTemplate = new \BackendTemplate($this->strTemplate);
		$contents = array();
		$contentArray = array();
		$articles = array();
		/** Generate Navigation */
		$this->getOrderedPageArrayByRoot();
		$objTemplate->dynanavigation = self::$pageList;

		$objUser = \BackendUser::getInstance();
		$pageId = \Contao\Input::get('page');
		$columnId = \Contao\Input::get('column')?\Contao\Input::get('column'):'main';
		$objArticle = \ArticleModel::findByPid($pageId);


		if (\Contao\Input::get('act') == "delete") {
			$this->deleteContentElement(\Contao\Input::get('id'));
			\Controller::redirect($this->addToUrl("act="));
		}

		if (\Contao\Input::get('act') == "copy") {
			$this->copyContentElement(\Contao\Input::get('id'), \Contao\Input::get('pid'));
			\Controller::redirect($this->addToUrl("act="));
		}
		

		if(!$pageId) {
			$message = array('error'=> true, "errorCode" => "noPage");
			$objTemplate->message = $message;
			
		} elseif (!$objUser->hasAccess($pageId, "pagemounts")) {
			$message = array('error'=> true, "errorCode" => "noAccess");
			$objTemplate->message = $message;
		}

		

		elseif(!$objArticle) {
			$message = array('error'=> true, "errorCode" => "noArticle");
			$objTemplate->message = $message;
		} else {
			/**
			 * Get Content 
			 */
			foreach ($objArticle as $article) {

				$inColumn = $article->inColumn;
				
				$option = array(
					'table' => 'tl_content', 
					'column' => array("tl_content.pid=?"), 
					'value' => array($article->id),
					'order' => 'sorting ASC'
				);
				$articles[$article->inColumn] = $article->inColumn;
				$objTemplate->articlesColumns = $articles;

				$contentModels = \ContentModel::findAll( $option );
				
				if ($contentModels != null) {
					foreach ($contentModels as $contentModel ){
						#var_dump($contentModel->id);
						$contentArray[$contentModel->id]['editTitle'] = $this->generateEditTitle($contentModel);
						$contents[$article->inColumn][] = $contentModel;
					}
				}
			}

			if (empty($contents)) {
				$message = array('error'=> true, "errorCode" => "noContent");
				$objTemplate->message = $message;
			}
		}
		
		if ($message['error'] == false )
		{
			
			if (!$contents[$columnId]){
				\Controller::redirect($this->addToUrl("column=".array_key_first($contents)));
			}

			foreach ($contents[$columnId] as $content)
			{
				
				#var_dump($content->id);
				
			}

			$objTemplate->contents = $contents[$columnId];
			$objTemplate->contentArray = $contentArray;
		}
		

		$objPage = \PageModel::findByPk( $pageId );
		$objTemplate->pageId = $pageId;
		$objTemplate->pageObj = $objPage;
		$objTemplate->columnId = $columnId;
		$objTemplate->articleObj = $objArticle;
        return $objTemplate->parse();
		
	}
	
	
	/**
	 * Copy a Contentelement
	 */
	public function copyContentElement($id, $pid) {
		echo "<pre>";
		$objContent = \ContentModel::findByPk($id);
		$objCopied = clone $objContent;
		
		var_dump($objCopied);

		#$objCopied->save();

		echo "</pre>";
		exit;
	}
	/**
	 * Delete a Contentelement
	 */
	public function deleteContentElement($id) {
		$objContent = \ContentModel::findByPk($id);
		$objContent->delete();
	}
	
	/* generate left col navigation with HOOK getUserNavigation */
	public function generateNavigation($arrModules, $blnShowAll)
	{
	
		$this->getOrderedPageArrayByRoot();
		$pages = self::$pageList;
		foreach ($pages as $page) 
		{
			
			if ($page['level'] == "root") {
				$arrModules['ContaoDynaContent']['modules']['contaodyna'.$page['page']['id']]= array
				(
					'label'        => $page['page']['title'],
					'title'        => 'Start:' . $page['page']['title'],
					'class'        => 'navigation dynanavigation isroot dontClick',
					'href'        => false,
				);
			} else {

				if ($GLOBALS['TL_CONFIG']['linkToDefaultContent']){
			 
					$arrOptions = array  
					(  
						'order'  => "FIELD(inColumn, 'main,left,right')",
						'limit'  => "1",
					);
					
					$articleObj = \ArticleModel::findByPid($page['page']['id'], $arrOptions);
					$href = "contao?do=article&table=tl_content&id=".$articleObj->id."&ref=" . TL_REFERER_ID . "" ;
					$css = 'navigation dynanavigation ispage belevel' . $page['level'] . ' ' . ($page['hasAccess']?"":"usernotallowed") . ' ' . ($page['page']['id'] == \Contao\Input::get('page')?'active':'' ) . ' ';
					
				} else {
					$href = "contao?do=dynacontent&ref=" . TL_REFERER_ID . "&page=" . $page['page']['id'] . "";
					$css = 'navigation dynanavigation ispage belevel' . $page['level'] . ' ' . ($page['hasAccess']?"":"usernotallowed") . ' ' . ($page['page']['id'] == \Contao\Input::get('page')?'active':'' ) . ' ';
				}

				$arrModules['ContaoDynaContent']['modules']['contaodyna'.$page['page']['id']]= array
				(
					'label'        => $page['page']['title'],
					'title'        => 'Edit:' . $page['page']['title'],
					'class'        => $css,
					'href'        => $href,
				);
			}
			
		}		
		return $arrModules;
	}

	/**
	 * Get all root pages and ordered pages as array with level information
	 */
	public function getOrderedPageArrayByRoot ()
	{
		$objUser = \BackendUser::getInstance();

		$result = \Database::getInstance()->prepare("SELECT * from tl_page WHERE type='root' ")->execute();
		
		if( $result->numRows) {
			$arr = $result->fetchAllAssoc(); 
			foreach ($arr AS $row) {
				self::$pageList[$row['id']]['page'] = $row;
				self::$pageList[$row['id']]['level'] = "root";
				$this->getOrderedPageArray($row['id'], $objUser);
			}
		}
	}

	/**
	 * prepare ordered pages as array with level information
	 */
	public static function getOrderedPageArray($rootPage, $objUser, $level = 1)
	{ 
		$pages = self::findPagesAndSubByPid($rootPage, true);

		if ($pages) {
			foreach ($pages as $page)  
			{
				self::$pageList[$page['id']]['page'] = $page;
				self::$pageList[$page['id']]['level'] = $level;
	
				if ($objUser->hasAccess($page['id'], "pagemounts")) {
					self::$pageList[$page['id']]['hasAccess'] = true;
				}
				else{
					self::$pageList[$page['id']]['hasAccess'] = false;
				}	
	
				if (self::findPagesAndSubByPid($page['id'], true))
				{	
					$level ++;
					self::getOrderedPageArray($page['id'], $objUser, $level );
					$level --;
				}
			}
		}
	}

	/**
	 * Find all Pages sorted by sorting - but not root
	 */
	public static function findPagesAndSubByPid($intPid)
	{
		$objSubpages = \Database::getInstance()->prepare("
		SELECT p1.*, (
				SELECT COUNT(*) FROM tl_page p2 WHERE 
				p2.pid=p1.id AND p2.type!='root'
			) AS subpages FROM tl_page p1 WHERE 
			p1.pid=? AND p1.type!='root'
			ORDER BY p1.sorting"
		)->execute($intPid);

		if ($objSubpages->numRows < 1)
		{
			return null;
		}
		
		return $objSubpages ->fetchAllAssoc(); 
	}

	/**
	 * Get all available content elements
	 */
	public function getContentElements() {
		$groups = array();
		foreach ($GLOBALS['TL_CTE'] as $k=>$v)
		{
			foreach (array_keys($v) as $kk)
			{
				$groups[$k][] = $kk;
			}
		}
		return $groups;
	}

	/**
	 * Create Element Headline in the List from original content class
	 */
	public function generateEditTitleFromClass($contentModel)
	{
		$return = "<span class='titleTypeOrig'>" . $GLOBALS['TL_LANG']['CTE'][$contentModel->type][0] . " </span> ";
		$cteClass = \ContentElement::findClass($contentModel->type);
		$cte = new $cteClass($contentModel);
		$return .= $cte->generate();
		
		return( $return );
	}

	/**
	 * Create Element Headline in the List
	 */
	public function generateEditTitle($contentModel)
	{
		$return = "<span class='titleType'>" . $GLOBALS['TL_LANG']['CTE'][$contentModel->type][0] . " | </span> ";

		$headline = deserialize($contentModel->headline);

		if (@$headline['value']) {

			$return .= $headline['value'];

		} elseif ($contentModel->mooHeadline != "") {

			if ($contentModel->type == "accordionSingle" || $contentModel->type == "accordionStart")
				$return .= $contentModel->mooHeadline;

		} elseif ($contentModel->text) {

			if ($contentModel->type == "text")
				$return .= substr(strip_tags($contentModel->text), 0, 80) . "...";

		} elseif ( is_numeric($contentModel->module)) {
			
			$module = \ModuleModel::findById($contentModel->module);
			$return .= $module->name;
		}
		
		return $return;
	}

}