<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   atheletes
 * @author    Hamid Abbaszadeh
 * @license   LGPL-3.0+
 * @copyright respinar 2013-2015
 */


/**
 * Dynamically add the permission check and parent table
 */
if (Input::get('do') == 'athletes')
{
	$GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_athlete';
	//$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_athletes', 'checkPermission');
}


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_content_athletes extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Check permissions to edit table tl_content
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Set the root IDs
		if (!is_array($this->User->athletes) || empty($this->User->athletes))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->athletes;
		}

		// Check the current action
		switch (Input::get('act'))
		{
			case 'paste':
				// Allow
				break;

			case '': // empty
			case 'create':
			case 'select':
				// Check access to the athlete item
				if (!$this->checkAccessToElement(CURRENT_ID, $root, true))
				{
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'cutAll':
			case 'copyAll':
				// Check access to the parent element if a content element is moved
				if ((Input::get('act') == 'cutAll' || Input::get('act') == 'copyAll') && !$this->checkAccessToElement(Input::get('pid'), $root, (Input::get('mode') == 2)))
				{
					$this->redirect('contao/main.php?act=error');
				}

				$objCes = $this->Database->prepare("SELECT id FROM tl_content WHERE ptable='tl_athlete' AND pid=?")
										 ->execute(CURRENT_ID);

				$session = $this->Session->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objCes->fetchEach('id'));
				$this->Session->setData($session);
				break;

			case 'cut':
			case 'copy':
				// Check access to the parent element if a content element is moved
				if (!$this->checkAccessToElement(Input::get('pid'), $root, (Input::get('mode') == 2)))
				{
					$this->redirect('contao/main.php?act=error');
				}
				// NO BREAK STATEMENT HERE

			default:
				// Check access to the content element
				if (!$this->checkAccessToElement(Input::get('id'), $root))
				{
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}


	/**
	 * Check access to a particular content element
	 *
	 * @param integer $id
	 * @param array   $root
	 * @param boolean $blnIsPid
	 *
	 * @return boolean
	 */
	protected function checkAccessToElement($id, $root, $blnIsPid=false)
	{
		if ($blnIsPid)
		{
			$objArchive = $this->Database->prepare("SELECT a.id, n.id AS nid FROM tl_athlete n, tl_athlete_category a WHERE n.id=? AND n.pid=a.id")
										 ->limit(1)
										 ->execute($id);
		}
		else
		{
			$objArchive = $this->Database->prepare("SELECT a.id, n.id AS nid FROM tl_content c, tl_athlete n, tl_athlete_category a WHERE c.id=? AND c.pid=n.id AND n.pid=a.id")
										 ->limit(1)
										 ->execute($id);
		}

		// Invalid ID
		if ($objArchive->numRows < 1)
		{
			$this->log('Invalid athletes content element ID ' . $id, __METHOD__, TL_ERROR);

			return false;
		}

		// The athletes group is not mounted
		if (!in_array($objArchive->id, $root))
		{
			$this->log('Not enough permissions to modify article ID ' . $objArchive->nid . ' in athletes archive ID ' . $objArchive->id, __METHOD__, TL_ERROR);

			return false;
		}

		return true;
	}

}
