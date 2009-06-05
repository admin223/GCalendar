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

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'rss-calendar'.DS.'defaultcalendar.php');

class GCalendar extends DefaultCalendar{

	var $dateFormat = 'dd/mm/yy';

	function GCalendar($model){
		$this->DefaultCalendar($model);
	}

	function printToolBar(){
		$year = (int)$this->year;
		$month = (int)$this->month;
		$day = (int)$this->day;
		$view = $this->view;

		$document =& JFactory::getDocument();
		$document->setTitle('GCalendar: '.$this->getViewTitle($year, $month, $day, $this->getWeekStart(), $view));

		$mainFilename = "index.php?option=com_gcalendar&view=gcalendar";
		switch($view) {
			case "month":
				$nextMonth = ($month == 12) ? 1 : $month+1;
				$prevMonth = ($month == 1) ? 12 : $month-1;
				$nextYear = ($month == 12) ? $year+1 : $year;
				$prevYear = ($month == 1) ? $year-1 : $year;
				$prevURL = $mainFilename . "&gcalendarview=month&year=${prevYear}&month=${prevMonth}";
				$nextURL = $mainFilename . "&gcalendarview=month&year=${nextYear}&month=${nextMonth}";
				break;
			case "week":
				list($nextYear, $nextMonth, $nextDay) = explode(",", date("Y,n,j", strtotime("+7 days", strtotime("${year}-${month}-${day}"))));
				list($prevYear, $prevMonth, $prevDay) = explode(",", date("Y,n,j", strtotime("-7 days", strtotime("${year}-${month}-${day}"))));

				$prevURL = $mainFilename . "&gcalendarview=week&year=${prevYear}&month=${prevMonth}&day=${prevDay}";
				$nextURL = $mainFilename . "&gcalendarview=week&year=${nextYear}&month=${nextMonth}&day=${nextDay}";

				break;
			case "day":
				list($nextYear, $nextMonth, $nextDay) = explode(",", date("Y,n,j", strtotime("+1 day", strtotime("${year}-${month}-${day}"))));
				list($prevYear, $prevMonth, $prevDay) = explode(",", date("Y,n,j", strtotime("-1 day", strtotime("${year}-${month}-${day}"))));

				$prevURL = $mainFilename . "&gcalendarview=day&year=${prevYear}&month=${prevMonth}&day=${prevDay}";
				$nextURL = $mainFilename . "&gcalendarview=day&year=${nextYear}&month=${nextMonth}&day=${nextDay}";

				break;
			}

			$document =& JFactory::getDocument();
			$calCode  = "function datePickerClosed(dateField){\n";
			$calCode .= "var d = jQuery.datepicker.parseDate('".$this->dateFormat."', dateField.value);\n";
			$calCode .= "document.getElementById('gc_go_link').href = '".JRoute::_($mainFilename."&gcalendarview=".$view)."&day='+d.getDate()+'&month='+(d.getMonth()+1)+'&year='+d.getFullYear();\n";
			$calCode .= "};\n";
			$document->addScriptDeclaration($calCode);

			$document->addScript('administrator/components/com_gcalendar/libraries/jquery/jquery-1.3.2.js');
			$document->addScript('administrator/components/com_gcalendar/libraries/jquery/ui/ui.core.js');
			$document->addScript('administrator/components/com_gcalendar/libraries/jquery/ui/ui.datepicker.js');
			$document->addStyleSheet('administrator/components/com_gcalendar/libraries/jquery/themes/redmond/ui.all.css');

			$daysLong = "[";
			$daysShort = "[";
			$daysMin = "[";
			$monthsLong = "[";
			$monthsShort = "[";
			$dateObject = JFactory::getDate();
			for ($i=0; $i<7; $i++) {
				$daysLong .= "'".$dateObject->_dayToString($i, false)."'";
				$daysShort .= "'".$dateObject->_dayToString($i, true)."'";
				$daysMin .= "'".substr($dateObject->_dayToString($i, true), 0, 2)."'";
				if($i < 6){
					$daysLong .= ",";
					$daysShort .= ",";
					$daysMin .= ",";
				}
			}

			for ($i=0; $i<12; $i++) {
				$monthsLong .= "'".$dateObject->_monthToString($i, false)."'";
				$monthsShort .= "'".$dateObject->_monthToString($i, true)."'";
				if($i < 11){
					$monthsLong .= ",";
					$monthsShort .= ",";
				}
			}
			$daysLong .= "]";
			$daysShort .= "]";
			$daysMin .= "]";
			$monthsLong .= "]";
			$monthsShort .= "]";

			$calCode = "jQuery.noConflict();\n";
			$calCode .= "jQuery(document).ready(function(){\n";
			$calCode .= "document.getElementById('gcdate').value = jQuery.datepicker.formatDate('".$this->dateFormat."', new Date(".$year.", ".$month." - 1, ".$day."));\n";
			$calCode .= "jQuery(\"#gcdate\").datepicker({dateFormat: '".$this->dateFormat."'});\n";
			$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'dayNames', ".$daysLong.");\n";
			$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'dayNamesShort', ".$daysShort.");\n";
			$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'dayNamesMin', ".$daysMin.");\n";
			$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'monthNames', ".$monthsLong.");\n";
			$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'monthNamesShort', ".$monthsShort.");\n";
			$calCode .= "});\n";
			$document->addScriptDeclaration($calCode);

			echo "<div id=\"calToolbar\">\n";
			echo "<div id=\"calPager\" class=\"Item\">\n";
			echo "<a class=\"Item\" href=\"".JRoute::_($prevURL)."\" title=\"previous ".$view."\">\n";
			$this->image("btn-prev.gif", "previous ".$view, "prevBtn_img");
			echo "</a>\n";
			echo "<span class=\"ViewTitle Item\">\n";
			echo $this->getViewTitle($year, $month, $day, $this->getWeekStart(), $view);
			echo "</span>\n";
			echo "<a class=\"Item\" href=\"".JRoute::_($nextURL)."\" title=\"next ".$view."\">\n";
			$this->image("btn-next.gif", "next ".$view, "nextBtn_img");
			echo "</a></div>\n";
			echo "<a class=\"Item\" href=\"".JRoute::_($mainFilename."&gcalendarview=".$view."&year=".$this->today["year"]."&month=".$this->today["mon"]."&day=".$this->today["mday"])."\">\n";
			$this->image("btn-today.gif", "go to today", "", "today_img");
			echo "</a>\n";
			echo "<input class=\"Item\"	type=\"text\" name=\"gcdate\" id=\"gcdate\" \n";
			echo "onchange=\"datePickerClosed(this);\" \n";
			echo "size=\"10\" maxlength=\"10\" title=\"jump to date\" />";
			echo "<a class=\"Item\" id=\"gc_go_link\" href=\"".JRoute::_($mainFilename."&gcalendarview=".$view."&year=".$year."&month=".$month."&day=".$day)."\">\n";
			$this->image("btn-go.gif", "go to date", "gi_img");
			echo "</a>\n";

			echo "<div id=\"viewSelector\" class=\"Item\">\n";
			echo "<a href=\"".JRoute::_($mainFilename."&gcalendarview=day&year=".$year."&month=".$month."&day=".$day)."\">\n";
			$this->image("cal-day.gif", "day view", "calday_img");
			echo "</a>\n";

			echo "<a href=\"".JRoute::_($mainFilename."&gcalendarview=week&year=".$year."&month=".$month."&day=".$day)."\">\n";
			$this->image("cal-week.gif", "week view", "calweek_img");
			echo "</a>\n";

			echo "<a href=\"".JRoute::_($mainFilename."&gcalendarview=month&year=".$year."&month=".$month."&day=".$day)."\">\n";
			$this->image("cal-month.gif", "month view", "calmonth_img");
			echo "</a></div></div>\n";
	}

	/**
	 * This is an internal helper method and should not be called from outside of the class
	 * otherwise you know what you do.
	 *
	 */
	function image($name, $alt = "[needs alt tag]", $id="") {
		list($width, $height, $d0, $d1) = getimagesize(JPATH_SITE.DS.'components'.DS.'com_gcalendar'.DS.'views'.DS.'gcalendar'.DS.'tmpl'.DS.'img'.DS . $name);
		echo "<img src=\"".JURI::base() . "components/com_gcalendar/views/gcalendar/tmpl/img/" . $name."\"";
		echo " id=\"". $id."\" width=\"". $width."\" height=\"".$height."\" alt=\"".$alt."\" border=\"0\"";
		echo $attrs ."/>";
	}
}
?>