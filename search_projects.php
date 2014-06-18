<?php
session_start();
include 'utilities.php';

use Airavata\Model\Workspace\Experiment\ExperimentState;
use Airavata\API\Error\InvalidRequestException;
use Airavata\API\Error\AiravataClientException;
use Airavata\API\Error\AiravataSystemException;
use Airavata\API\Error\ExperimentNotFoundException;
use Thrift\Exception\TTransportException;



connect_to_id_store();
verify_login();

$airavataclient = get_airavata_client();

?>

<html>

<?php create_head(); ?>

<body>

<?php create_nav_bar(); ?>

    <div class="container" style="max-width: 750px;">

        <h1>Search for Projects</h1>

        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" class="form-inline" role="form">
            <div class="form-group">
                <label for="search-key">Search by</label>
                <select class="form-control" name="search-key" id="search-key">
                    <option value="project-name">Project Name</option>
                    <option value="project-description">Project description</option>
                </select>
            </div>

            <div class="form-group">
                <label for="search-value">for</label>
                <input type="search" class="form-control" name="search-value" id="search-value" placeholder="value" required
                       value="<?php if (isset($_POST['search-value'])) echo $_POST['search-value'] ?>">
            </div>

            <input name="search" type="submit" class="btn btn-primary" value="Search">
        </form>





        <?php

        if (isset($_POST['search']))
        {
            /**
             * get results
             */
            $projects = get_search_results();

            /**
             * display results
             */
            if (sizeof($projects) == 0)
            {
                print_warning_message('No results found. Please try again.');
            }
            else
            {
                foreach ($projects as $project)
                {
                    echo '<div class="panel panel-default">';

                    echo '<div class="panel-heading">';
                    echo "<h3>$project->name</h3>";
                    echo "<p>$project->description</p>";
                    echo '</div>';

                    $experiments = get_experiments_in_project($project->projectID);

                    echo '<table class="table">';

                    echo '<tr>';

                    echo '<th>Name <small style="color:#999; font-weight:normal;">Click <span class="glyphicon glyphicon-pencil"></span> to edit</small></th>';
                    echo '<th>Application</th>';
                    echo '<th>Status</th>';

                    echo '</tr>';

                    foreach ($experiments as $experiment)
                    {
                        $experimentStatus = $experiment->experimentStatus;
                        $experimentState = $experimentStatus->experimentState;
                        $experimentStatusString = ExperimentState::$__names[$experimentState];
                        $experimentTimeOfStateChange = $experimentStatus->timeOfStateChange;


                        echo '<tr>';

                        echo '<td>';


                        switch ($experimentStatusString)
                        {
                            case 'SCHEDULED':
                            case 'LAUNCHED':
                            case 'EXECUTING':
                            case 'CANCELING':
                            case 'COMPLETED':
                                echo $experiment->name;
                                break;
                            default:
                                echo $experiment->name .
                                    ' <a href="edit_experiment.php?expId=' .
                                    $experiment->experimentID .
                                    '"><span class="glyphicon glyphicon-pencil"></span></a>';
                                break;
                        }



                        echo '</td>';

                        echo "<td>$experiment->applicationId</td>";



                        //echo '<td>' . $experimentStatusString . ' at ' . date("Y-m-d H:i:s", $experimentTimeOfStateChange) . '</td>';
                        echo '<td><a href="experiment_summary.php?expId=' . $experiment->experimentID . '">' . $experimentStatusString . '</a></td>';

                        echo '</tr>';
                    }

                    echo '</table>';
                    echo '</div>';
                }
            }

        }


        //$transport->close();

        ?>


    </div>

</body>
</html>



















<?php
/**
 * Utility Functions
 */


/**
 * Create options for the search key select input
 * @param $values
 * @param $labels
 * @param $disabled
 */
function create_options($values, $labels, $disabled)
{
    for ($i = 0; $i < sizeof($values); $i++)
    {
        $selected = '';

        // if option was previously selected, mark it as selected
        if (isset($_POST['search-key']))
        {
            if ($values[$i] == $_POST['search-key'])
            {
                $selected = 'selected';
            }
        }

        echo '<option value="' . $values[$i] . '" ' . $disabled[$i] . ' ' . $selected . '>' . $labels[$i] . '</option>';
    }
}

/**
 * Get results of the user's search
 * @return array|null
 */
function get_search_results()
{
    global $airavataclient;

    $projects = array();

    try
    {
        switch ($_POST['search-key'])
        {
            case 'project-name':
                $projects = $airavataclient->searchProjectsByProjectName($_SESSION['username'], $_POST['search-value']);
                break;
            case 'project-description':
                $projects = $airavataclient->searchProjectsByProjectDesc($_SESSION['username'], $_POST['search-value']);
                break;
        }
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }

    return $projects;
}





/**
 * Get experiments in project
 * @param $projectId
 * @return array|null
 */
function get_experiments_in_project($projectId)
{
    global $airavataclient;

    $experiments = array();

    try
    {
        $experiments = $airavataclient->getAllExperimentsInProject($projectId);
    }
    catch (InvalidRequestException $ire)
    {
        print_error_message('InvalidRequestException!<br><br>' . $ire->getMessage());
    }
    catch (AiravataClientException $ace)
    {
        print_error_message('AiravataClientException!<br><br>' . $ace->getMessage());
    }
    catch (AiravataSystemException $ase)
    {
        print_error_message('AiravataSystemException!<br><br>' . $ase->getMessage());
    }
    catch (TTransportException $tte)
    {
        print_error_message('TTransportException!<br><br>' . $tte->getMessage());
    }

    return $experiments;
}

