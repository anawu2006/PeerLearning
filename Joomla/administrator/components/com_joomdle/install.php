<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// 1.6 installer file
class com_joomdleInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent) 
        {

            $manifest = $parent->get("manifest");
            $parent = $parent->getParent();
            $source = $parent->getPath("source");
             
            $installer = new JInstaller();
            
            // Install plugins
            foreach($manifest->plugins->plugin as $plugin) {
                $attributes = $plugin->attributes();
                $plg = $source . '/' . $attributes['folder'].'/'.$attributes['plugin'];
                $plg = $source . '/' . $attributes['folder'];
                $installer->install($plg);
            }
            // Install modules
            foreach($manifest->modules->module as $module) {
                $attributes = $module->attributes();
                $mod = $source . '/' . $attributes['folder'].'/'.$attributes['module'];

                $installer->install($mod);
            }
            
            $db = JFactory::getDbo();
            $tableExtensions = $db->nameQuote("#__extensions");
            $columnElement   = $db->nameQuote("element");
            $columnType      = $db->nameQuote("type");
            $columnEnabled   = $db->nameQuote("enabled");
            
            $tableExtensions = "#__extensions";
            $columnElement   = "element";
            $columnType      = "type";
            $columnEnabled   = "enabled";

            // Enable plugins
            $db->setQuery(
                "UPDATE 
                    $tableExtensions
                SET
                    $columnEnabled=1
                WHERE
                    ($columnElement='courses' or $columnElement='coursecategories' or $columnElement='coursetopics' or $columnElement='joomdlehooks')
                AND
                    $columnType='plugin'"
            );
            
            $db->query();
            
            // Set plugin ordering
            $db->setQuery(
                "UPDATE 
                    $tableExtensions
                SET
                    ordering=100
                WHERE
                    $columnElement='joomdlehooks' 
                AND
                    $columnType='plugin'"
            );
            
            $db->query();


//			$this->create_tables ();
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {

return; //XXX not working, need to install all plugins/modules separately

                // $parent is the class calling this method
                echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
            $manifest = $parent->get("manifest");
            $parent = $parent->getParent();
            $source = $parent->getPath("source");
             
            $installer = new JInstaller();
            
				$db           = JFactory::getDBO();
            // Install plugins
            foreach($manifest->plugins->plugin as $plugin) {
                $attributes = $plugin->attributes();
                $plg = $source . '/' . $attributes['folder'].'/'.$attributes['plugin'];
                $plg = $source . '/' . $attributes['folder'];
$name = $attributes['plugin'];
	/*			print_r ($attributes);
				echo "X";
				$type = 'user';
				$data = JPluginHelper::getPlugin ($type, 'joomdlehooks');
				print_r ($data);
*/
				$query = 'SELECT extension_id '.
						' FROM #__extensions'.
						" WHERE name = '$name'";
				$db->setQuery($query);
				$extension_id = $db->loadResult();

                $installer->uninstall('plugin', $extension_id);
            }
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
                // $parent is the class calling this method
				$this->install ($parent);
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
            //    echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
			if ($type == 'install')
				$this->load_default_config ();
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
               // echo '<p>' . JText::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
			$rows = 0;
            $manifest = $parent->get("manifest");
?>

<h2>Joomdle Installation</h2>
<table  class="table table-striped">
    <thead>
        <tr>
            <th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
            <th width="30%"><?php echo JText::_('Status'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    <tbody>
        <tr class="row0">
            <td class="key" colspan="2"><?php echo 'Joomdle '.JText::_('Component'); ?></td>
            <td><strong><?php echo JText::_('Installed'); ?></strong></td>
        </tr>
        <tr>
            <th><?php echo JText::_('Module'); ?></th>
            <th><?php echo JText::_('Client'); ?></th>
            <th></th>
        </tr>
    <?php foreach ($manifest->modules->module as $module) : ?>
<?php
                $attributes = $module->attributes();
?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo $attributes['title']; ?></td>
            <td class="key"><?php echo ucfirst($attributes['client']); ?></td>
            <td><strong><?php echo JText::_('Installed'); ?></strong></td>
        </tr>
    <?php endforeach; ?>
        <tr>
            <th><?php echo JText::_('Plugin'); ?></th>
            <th><?php echo JText::_('Group'); ?></th>
            <th></th>
        </tr>
    <?php foreach ($manifest->plugins->plugin as $plugin) : ?>
<?php
                $attributes = $plugin->attributes();
?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo ucfirst($attributes['plugin']); ?></td>
            <td class="key"><?php echo $attributes['group']; ?></td>
            <td><strong><?php echo JText::_('Installed'); ?></strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
        }

		// NOT USED: replaced my sql scripts under sql/
		function create_tables ()
		{
			// Create tables
            $db = JFactory::getDbo();

			$allQueries     = array();

			$allQueries[]   ='CREATE TABLE IF NOT EXISTS `#__joomdle_field_mappings` (
              `id` int(11) NOT NULL auto_increment,
              `joomla_app` varchar(45) NOT NULL,
              `joomla_field` varchar(45) NOT NULL,
              `moodle_field` varchar(45) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8';


			$allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_profiletypes` (
              `id` int(11) NOT NULL auto_increment,
              `profiletype_id` int(11) NOT NULL,
              `create_on_moodle` int(11) NOT NULL,
              `moodle_role` int(11) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

			$allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_purchased_courses` (
              `id` int(11) NOT NULL auto_increment,
              `user_id` int(11) NOT NULL,
              `course_id` int(11) NOT NULL,
              `num` int(11) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

			$allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_course_applications` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `course_id` int(11) NOT NULL,
              `state` int(11) NOT NULL,
              `application_date` datetime NOT NULL,
              `confirmation_date` datetime NOT NULL,
                `motivation` text NOT NULL,
              `experience` text NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

		$allQueries[] = 'CREATE TABLE IF NOT EXISTS `#__joomdle_mailinglists` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `course_id` int(11) NOT NULL,
          `list_id` int(11) NOT NULL,
          `type` varchar(32) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

		$allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_bundles` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `courses` text NOT NULL,
          `cost` float NOT NULL,
          `currency` varchar(32) NOT NULL,
          `name` varchar(255) NOT NULL,
          `description` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

		 $allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_course_groups` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `course_id` int(11) NOT NULL,
		  `group_id` int(11) NOT NULL,
		  `type` varchar(32) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

		$allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_course_forums` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `moodle_forum_id` int(11) NOT NULL,
		  `kunena_forum_id` int(11) NOT NULL,
		  `course_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8';


		$db = JFactory::getDBO();
        foreach($allQueries as $query) {
                $db->setQuery( $query );
                $db->query();
			}

			//R0.6 upgrade
			if (!$this->TableColumnExists ('#__joomdle_profiletypes', 'moodle_role'))
			{
				$sql = 'ALTER TABLE ' . $db->nameQuote('#__joomdle_profiletypes') .' ADD ' . $db->nameQuote('moodle_role') .' int(11) NOT NULL';
				$db->setQuery($sql);
				$db->query();

			}

		}

		function load_default_config ()
		{
			$db = JFactory::getDBO();
			$query = "UPDATE #__extensions set params='auto_create_users=1
			MOODLE_URL=
			connection_method=fgc
			auto_delete_users=1
			auto_login_users=0
			linkstarget=wrapper
			scrolling=no
			width=100%
			height=1000
			autoheight=1
			transparency=0
			default_itemid=
			show_topìcs_link=1
			show_grading_system_link=0
			show_teachers_link=0
			show_enrol_link=1
			show_paypal_button=0
			topics_show_numbers=1
			coursecategory_show_category_info=1
			shop_integration=0
			courses_category=0
			buy_for_children=0
			enrol_email_subject=Welcome to COURSE_NAME
			enrol_email_text=To enter the course, go to: COURSE_URL
			additional_data_source=none
			use_xipt_integration=0'
			WHERE name='com_joomdle'";

			$db->setQuery($query);
			if (!$db->Query()) {
				return false;
			}



		}


	function getFields( $table )
    {
        $result = array();
        $db     = JFactory::getDBO();

        $query  = 'SHOW FIELDS FROM ' .$table;

        $db->setQuery( $query );

        $fields = $db->loadObjectList();

        foreach( $fields as $field )
        {
            $result[ $field->Field ]    = preg_replace( '/[(0-9)]/' , '' , $field->Type );
        }

        return $result;
    }

    function TableColumnExists($tablename, $columnname)
    {
        $fields = $this->getFields($tablename);
        if(array_key_exists($columnname, $fields))
        {
            return true;
        }
        return false;
    }

}
