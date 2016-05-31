<?php 

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */

/**
 * Class DashboardHelper
 *
 * Helper class to render dashboards based on configuration. By default will look directly at the 'dashboard_items' key
 * of the Yii params if none are provided.
 *
 */
class DashboardHelper {

    /**
     * @var array
     */
    protected $items;
    /**
     * @var OEWebUser
     */
    protected $user;

    /**
     * Flag to toggle the drag and drop sorting controls for widgets
     * @TODO: this will be switched to true in the future when we set up controls for storing user preference for widgets
     * @var bool
     */
    public $sortable;

    /**
     * @param array $items expected to be of the form:
     *  [
     *      [
     *          'module' => 'ModuleName',
     *          'restricted' => <array of auth items that will grant access to this dashboard> (optional)
     *      ]
     *  ]
     *
     * @param OEWebUser $user
     */
    public function __construct(array $items = null, OEWebUser $user = null)
    {

        $this->items = $items;
        $this->user = $user;

        if (is_null($this->items)) {
            $this->items = Yii::app()->params['dashboard_items'] ?: array();
        }
        if (is_null($this->user)) {
            $this->user = Yii::app()->user;
        }

        // uses a config variable for ease of turning on the demo.
        $this->sortable = Yii::app()->params['dashboard_sortable'] ?: false;
    }

    /**
     * Renders the HTML snippet of the Dashboard
     *
     * @return mixed
     * @throws
     */
    public function render()
    {
        return Yii::app()->controller->renderPartial(
            '//base/_dashboard',
            array(
                'items' => $this->renderItems(),
                'sortable' => $this->sortable,
            ),
            true
        );
    }

    /**
     * @throws Exception for incorrect configuration for dashboard rendering
     */
    protected function renderItems()
    {
        $renders = array();

        foreach ($this->items as $item) {
            if (isset($item['restricted'])) {
                $allowed = false;
                foreach ($item['restricted'] as $authitem) {
                    if ($this->user->checkAccess($authitem)) {
                        $allowed = true;
                        break;
                    }
                }
                if (!$allowed) {
                    continue;
                }
            }
            
            if ( isset($item['module']) )
            {
                $module_name = $item['module'];

                $module = Yii::app()->moduleAPI->get($module_name);

                if (!$module) {
                    throw new Exception("$module_name not found");
                }
                if( isset($item['actions']) && is_array($item['actions']) ) {
                    $renders = array_merge($renders, $this->renderActions($module, $item['actions']));
                }
                else if( method_exists($module, 'renderDashboard') ) {
                    $renders[] = $module->renderDashboard();
                }
                
            } else if ( isset($item['title']) && isset($item['content']) ) {
                $renders[] = $item;
            } elseif ( isset($item['fakeClinicList'])) {
                // TODO: Remove this option
                $renders[] = $this->renderFakeClinicList(@$item['patients']);
            }

            else {
                throw new Exception("Invalid dashboard configuration, module or static content definition required");
            }
        }
        return $renders;
    }
    
    protected function renderActions($module, $actions)
    {
        $renders = array();
        
        foreach($actions as $method_name)
        {
            if( method_exists($module, $method_name) )
            {
                $renders[] = $module->$method_name();
            } else
            {
                throw new Exception("$method_name method not found");
            }
        }
        
        return $renders;
    }

    /**
     * @TODO: DELETE THIS MONSTROSITY
     *
     * @param $patients
     * @return array|void
     */
    protected function renderFakeClinicList($patients)
    {
        if (!$patients) {
            return;
        }

        $today = new DateTime();
        $auto_worklist_header = $today->format('l') . " Clinic - " . $today->format('d F Y');

        foreach ($patients as $p) {
            $worklist_patients[] = array(
                'patient' => Patient::model()->findByPk($p['patient_id']),
                'scheduledtime' => @$p['scheduledtime'],
                'vatime' => @$p['vatime'],
                'arrived' => @$p['arrived']
            );
        }


        $rows = "";
        foreach ($worklist_patients as $wp) {
            $rows .= <<<EOF
	<tr data-url="/patient/view/{$wp['patient']->id}" class="clickable">
		<td>{$wp['scheduledtime']}</td>
		<td>{$wp['vatime']}</td>
		<td>{$wp['arrived']}</td>
		<td>{$wp['patient']->hos_num}</td>
		<td style="white-space: nowrap;">{$wp['patient']->HSCICName}</td>
		<td>{$wp['patient']->genderString}</td>
		<td>{$wp['patient']->NHSDate('dob')}</td>
	</tr>
EOF;
        }

        $content = <<<EOF
    <h1>$auto_worklist_header</h1>
<div class="row">

    <div class="large-12 column">

            <table class="grid audit-logs worklist" id="worklist-table-11">
                <thead>
                <tr>
                                        <th>Time</th>
                                        <th>Arrived</th>
                                        <th>VA</th>
                                        <th>Hospital No.</th>
                    <th class="large-2">Patient</th>
                    <th>Gender</th>
                    <th>DOB</th>
                </tr>
                </thead>
                <tbody id="worklist-11-patients">
                	$rows
				</tbody>
            </table>
            </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('table.worklist').on('click', 'tr.clickable', function(e) {
        e.preventDefault();
        window.location.href = $(this).data('url');
    });
});
</script>
EOF;

        return array(
            'options' => array(
                'js-toggle-open' => true
            ),
            'title' => 'Automatic Worklists',
            'content' => $content
        );
    }
}