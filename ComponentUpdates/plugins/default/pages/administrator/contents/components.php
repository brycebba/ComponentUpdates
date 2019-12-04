<?php
/**
 * Open Source Social Network
 *
 * @package   (softlab24.com).ossn
 * @author    OSSN Core Team <info@softlab24.com>
 * @copyright (C) SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
?>
<div class="panel-group" id="accordion">
   	<?php
	$OssnComs = new OssnComponents;
	$list = $OssnComs->getComponents();
	if($list){
        $remote_components = com_component_updates_GetRemoteComponents();
        if (strlen($remote_components['error']) > 0) { ?>
            <div class="panel panel-danger margin-top-10">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        Error Getting Remote Component Update Versions!
                    </h4>
                </div>
                <div class="panel-body">
                    <p>
                        <?php echo $remote_components['error']; ?>
                    </p>
                </div>
            </div>
        <?php }
		foreach($list as $component) {
			$vars = array();
			$vars['OssnCom'] = $OssnComs;
			$vars['component'] = $OssnComs->getCom($component);
            $vars['name'] = $component;
            if (strlen($remote_components['error']) == 0) {
                $vars['update'] = com_component_updates_FindRemoteComponentWithUpdate($vars['component'], $remote_components);
            }
            else {
                $vars['update'] = array();
            }
            //error_log('vars[update]: ' . $vars['component']->id . ': ' . ossn_dump($vars['update']));
            echo ossn_plugin_view("admin/components/list/item", $vars);
		}
	}
	?>
</div> 