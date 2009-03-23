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

?>

<div
	class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">

<?php
$variables = '';
$variables = $variables.'?showTitle='.$this->params->get( 'title' );
$variables = $variables.'&amp;showNav='.$this->params->get( 'navigation' );
$variables = $variables.'&amp;showDate='.$this->params->get( 'date' );
$variables = $variables.'&amp;showPrint='.$this->params->get( 'print' );
$variables = $variables.'&amp;showTabs='.$this->params->get( 'tabs' );
$variables = $variables.'&amp;showCalendars=0';
$variables = $variables.'&amp;showTz='.$this->params->get( 'tz' );
$variables = $variables.'&amp;mode='.$this->params->get( 'view' );
$variables = $variables.'&amp;wkst='.$this->params->get( 'weekstart' );
$variables = $variables.'&amp;bgcolor=%23'.$this->params->get( 'bgcolor' );
$tz = $this->params->get('timezone');
if(!empty($tz))$tz='&ctz='.$tz;
$variables = $variables.$tz;
$variables = $variables.'&amp;height='.$this->params->get( 'height' );

$this->config['iWebCal_CALENDAR_FILE'] = $files[0];
$model = &$this->getModel();
$cal = new GCalendar2($model, $this->config); // Creates a new iWebCal viewer and calendar based on your settings in config.inc.
$cal->includes(); // Includes stylesheets and scripts needed by the calendar viewer.
echo $cal->title(); // Gives the page a title based on the filename of the calendar.
$cal->display(); // Displays the calendar and controls for it (i.e. most of the page).
?>