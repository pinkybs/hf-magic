<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Building_Controller extends Role_Controller {
	

	public function getParam($param)
	{
		$result = $this->input->post($param);
		if ( !$result ) {
			$result = $this->input->get($param);
		}
		return $result;
	}
	
	public function saveedit()
	{
		$role_id = Role::getOwnRoleId();
		
		$decorChangeList = $this->getParam('decorChangeList');
		$decorBagChangeList = $this->getParam('decorBagChangeList');
		$decorSellChangeList = $this->getParam('decorSellChangeList');
		$floorChangeList = $this->getParam('floorChangeList');
		$floorBagChangeList = $this->getParam('floorBagChangeList');
		$floorSellChangeList = $this->getParam('floorSellChangeList');
		$wallChangeList = $this->getParam('wallChangeList');
		$wallBagChangeList = $this->getParam('wallBagChangeList');
		$wallSellChangeList = $this->getParam('wallSellBagChangeList');
		
		//var_dump($decorChangeList);
		$build1 = empty($decorChangeList) ? array() : json_decode($decorChangeList, true);
		$build2 = empty($decorBagChangeList) ? array() : json_decode($decorBagChangeList, true);
		$build3 = empty($decorSellChangeList) ? array() : json_decode($decorSellChangeList, true);
		$floor1 = empty($floorChangeList) ? array() : json_decode($floorChangeList, true);
		$floor2 = empty($floorBagChangeList) ? array() : json_decode($floorBagChangeList, true);
		$floor3 = empty($floorSellChangeList) ? array() : json_decode($floorSellChangeList, true);
		$wall1 = empty($wallChangeList) ? array() : json_decode($wallChangeList, true);
		$wall2 = empty($wallBagChangeList) ? array() : json_decode($wallBagChangeList, true);
		$wall3 = empty($wallSellChangeList) ? array() : json_decode($wallSellChangeList, true);
		
		/*$build1 = empty($decorChangeList) ? array() : $decorChangeList;
		$build2 = empty($decorBagChangeList) ? array() : $decorBagChangeList;
		$build3 = empty($decorSellChangeList) ? array() : $decorSellChangeList;
		$floor1 = empty($floorChangeList) ? array() : $floorChangeList;
		$floor2 = empty($floorBagChangeList) ? array() : $floorBagChangeList;
		$floor3 = empty($floorSellChangeList) ? array() : $floorSellChangeList;
		$wall1 = empty($wallChangeList) ? array() : $wallChangeList;
		$wall2 = empty($wallBagChangeList) ? array() : $wallBagChangeList;
		$wall3 = empty($wallSellChangeList) ? array() : $wallSellChangeList;*/
		
		//$build1 = json_decode($build1, true);
		
		//building info
		$aryBuild = array_merge($build1, $build2);
		$aryPostBuild = array();
		foreach ($aryBuild as $key=>$vdata) {
			$aryPostBuild[$key]['id'] = $vdata['id'];
			$aryPostBuild[$key]['building_id'] = $vdata['d_id'];
			$aryPostBuild[$key]['mirror'] = $vdata['mirror'];
			$aryPostBuild[$key]['bag_type'] = $vdata['bag_type'];
			//if in bag -> bage_type == 1
			if ( $vdata['bag_type'] == 1 ) {
				$aryPostBuild[$key]['x'] = 0;
				$aryPostBuild[$key]['y'] = 0;
				$aryPostBuild[$key]['z'] = 0;
			}
			else {
				$aryPostBuild[$key]['x'] = $vdata['x'];
				$aryPostBuild[$key]['y'] = $vdata['y'];
				$aryPostBuild[$key]['z'] = $vdata['z'];
			}
		}
		foreach ($build3 as $sdata) {
			$aryPostBuild[] = array('sell'=>1, 'id'=>$sdata['id']);
		}
			
		//floor info
		$aryFloor = $floor1;
		$aryShowFloor = $arySellFloor = array();
		foreach ($aryFloor as $key=>$vdata) {
			$aryShowFloor[$key]['floor_id'] = $vdata['d_id'];
			$aryShowFloor[$key]['x'] = $vdata['x'] - 1;
			$aryShowFloor[$key]['z'] = $vdata['z'] - 1;
		}
		foreach ($floor3 as $sdata) {
			$arySellFloor[] = array('sell'=>1, 'floor_id'=>$sdata['d_id'], 'quantity'=>(empty($sdata['num'])?1:$sdata['num']));
		}
		$aryPostFloor = array('aryShowFloor' => $aryShowFloor, 'arySellFloor' => $arySellFloor);
		
		//wall info
		$aryWall = $wall1;
		$aryShowWall = $arySellWall = array();
		foreach ($aryWall as $key=>$vdata) {
			$aryShowWall[$key]['wall_id'] = $vdata['d_id'];
			//x-wall
			if (0 == $vdata['z']) {
				$aryShowWall[$key]['x'] = 0;
				$aryShowWall[$key]['z'] = $vdata['x'] - 1;
			}
			//y-wall
			else if (0 == $vdata['x']) {
				$aryShowWall[$key]['x'] = 1;
				$aryShowWall[$key]['z'] = $vdata['z'] - 1;
			}
		}
		foreach ($wall3 as $sdata) {
			$arySellWall[] = array('sell'=>1, 'wall_id'=>$sdata['d_id'], 'quantity'=>(empty($sdata['num'])?1:$sdata['num']));
		}
		$aryPostWall = array('aryShowWall' => $aryShowWall, 'arySellWall' => $arySellWall);	
		
		$building = Building::instance($role_id);
        $result = $building->changeDecoration($role_id, $aryPostBuild, $aryPostFloor, $aryPostWall);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	if ( isset($result['content']) ) {
        		Network::buffer_error($result['content']);
        	}
        	else {
        		Network::buffer_error('error_diy');
        	}
        }
	}
}