<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Allon Moritz
 * @copyright 2007-2009 Allon Moritz
 * @version $Revision: 2.1.0 $
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class ModGcalendarUpcomingHelper {
	
	function getCalendarItems(&$params){
		$cache = & JFactory::getCache();
		$items  = $cache->call( array( 'ModGCalendarUpcomingHelper', 'getOnlineItems' ), &$params );
		return $items;
	}
	
	function getOnlineItems(&$params) {
		JModel::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_gcalendar'.DS.'models');
		$model =JModel::getInstance('GCalendar','GCalendarModel');
		$model->setState('parameters.menu', $params);
		$results = $model->getDBCalendars();
		if(empty($results))
		return array(JText::_("CALENDAR_NOT_FOUND"),NULL);

		$values = array();
		foreach ($results as $result) {
			if(!empty($result->calendar_id) && $result->selected){
				$feed = modGcalendarUpcomingHelper::create_gc_feed($params);
				$feed->put('gcid',$result->id);
				$url = SimplePie_GCalendar::create_feed_url($result->calendar_id, $result->magic_cookie);
				$feed->set_cal_language(GCalendarUtil::getFrLanguage());

				$feed->set_feed_url($url);
					
				// Initialize the feed so that we can use it.
				$feed->init();

				if ($feed->error()){
					return array(JText::_("SP_LATEST_ERROR").$feed->error(),NULL);
				}

				// Make sure the content is being served out to the browser properly.
				$feed->handle_content_type();

				$values = array_merge($values, $feed->get_items());
			}
		}

		// we sort the array based on the event compare function
		usort($values, array("SimplePie_Item_GCalendar", "compare"));

		//return the feed data structure for the template
		return array(NULL,$values);
	}

	function create_gc_feed($params){
		$sortOrder = $params->get( 'order', NULL );
		$pasteEvents = $params->get( 'pastevents', 0 );

		$feed = new SimplePie_GCalendar();
		$feed->set_show_past_events(FALSE);
		$feed->set_sort_ascending(TRUE);
		$feed->set_orderby_by_start_date($sortOrder);
		$feed->set_expand_single_events(TRUE);
		$feed->enable_order_by_date(FALSE);
		$feed->enable_cache(FALSE);
		return $feed;
	}
}
?>
