<?php

/**
 * matchs actions.
 *
 * @package    PhpProject1
 * @subpackage matchs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class matchsActions extends sfActions {

	public function executeMatchsInProgress(sfWebRequest $request) {
		$this->filter = new MatchsFormFilter($this->getFilters());
		$query = $this->filter->buildQuery($this->getFilters());
		$this->filterValues = $this->getFilters();

		$table = MatchsTable::getInstance();
		$this->pager = null;
		$this->pager = new sfDoctrinePager(
						'Matchs',
						12
		);
		$this->pager->setQuery($query->andWhere("status >= ? AND status <= ?", array(Matchs::STATUS_NOT_STARTED, Matchs::STATUS_END_MATCH))->orderBy("status ASC"));
		$this->pager->setPage($request->getParameter('page', 1));
		$this->pager->init();

		$this->page = $request->getParameter('page', 1);
		$this->url = "@matchs_current_page";

		$this->servers = ServersTable::getInstance()->findAll();
	}

	public function executeMatchsArchived(sfWebRequest $request) {
		$this->filter = new MatchsFormFilter($this->getFilters());
		$query = $this->filter->buildQuery($this->getFilters());
		$this->filterValues = $this->getFilters();

		$table = MatchsTable::getInstance();
		$this->pager = null;
		$this->pager = new sfDoctrinePager(
						'Matchs',
						12
		);
		$this->pager->setQuery($query->andWhere("status = ?", Matchs::STATUS_ARCHIVE)->orderBy("id DESC"));
		$this->pager->setPage($request->getParameter('page', 1));
		$this->pager->init();

		$this->url = "@matchs_archived_page";
	}

	public function executeFilters(sfWebRequest $request) {
		$this->filter = new MatchsFormFilter();
		$this->filter->bind($request->getPostParameter($this->filter->getName()));
		if ($this->filter->isValid()) {
			$this->setFilters($this->filter->getValues());
		}

		$this->redirect($request->getReferer());
	}

	public function executeFiltersClear(sfWebRequest $request) {
		$this->setFilters(array());
		$this->redirect($request->getReferer());
	}

	private function getFilters() {
		return $this->getUser()->getAttribute('matchs.filters', array(), 'admin_module');
	}

	private function setFilters($filters) {
		return $this->getUser()->setAttribute('matchs.filters', $filters, 'admin_module');
	}

	public function executeView(sfWebRequest $request) {
		$this->match = $this->getRoute()->getObject();

		$this->heatmap = PlayersHeatmapTable::getInstance()->createQuery()->where("match_id = ?", $this->match->getId())->count() > 0;
		if ($this->heatmap) {
			if (class_exists($this->match->getMap()->getMapName())) {
				$map = $this->match->getMap()->getMapName();
				$this->class_heatmap = new $map($this->match->getId());
			} else {
				$this->heatmap = false;
			}
		}
	}

	public function executeHeatmapData(sfWebRequest $request) {
		$this->match = $this->getRoute()->getObject();

		$this->heatmap = PlayersHeatmapTable::getInstance()->createQuery()->where("match_id = ?", $this->match->getId())->count() > 0;
		if ($this->heatmap) {
			if (class_exists($this->match->getMap()->getMapName())) {
				$map = $this->match->getMap()->getMapName();
				$this->class_heatmap = new $map($this->match->getId());
			} else {
				$this->heatmap = false;
			}
		}

		$map = $this->class_heatmap;
		foreach ($this->match->getPlayersHeatmap() as $event) {
			$map->addInformation($event->getId(), $event->getEventName(), $event->getEventX(), $event->getEventY(), $event->getPlayerId(), ($event->getPlayerTeam() == "CT") ? 1 : 2, $event->getRoundId(), $event->getRoundTime(), 0, 1, $event->getAttackerX(), $event->getAttackerY(), $event->getAttackerName(), $event->getAttackerTeam());
		}

		$type = $request->getPostParameter("type", "kill");

		$points = array();

		if ($type == "allstuff") {
			$points = array_merge($map->buildHeatMap("hegrenade"), $map->buildHeatMap("flashbang"), $map->buildHeatMap("smokegrenade"), $map->buildHeatMap("decoy"), $map->buildHeatMap("molotov"));
		} else {
			$side = $request->getPostParameter("sides", -1);
			if ($side == "all") {
				$side = -1;
			} elseif ($side == "ct") {
				$side = 1;
			} elseif ($side == "t") {
				$side = 2;
			} else {
				$side = -1;
			}
			$points = $map->buildHeatMap($type, $request->getPostParameter("rounds", array()), $side, $request->getPostParameter("players", array()));
		}

		return $this->renderText(json_encode(array("type" => "heatmap", "points" => $points)));
	}

	public function executeLogs(sfWebRequest $request) {
		$match = $this->getRoute()->getObject();
		return $this->renderText(file_get_contents(sfConfig::get("app_log_match") . "/match-" . $match->getId() . ".html"));
	}

}
