<?php

require_once 'proratedmemberships.civix.php';
use CRM_Proratedmemberships_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function proratedmemberships_civicrm_config(&$config) {
  _proratedmemberships_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function proratedmemberships_civicrm_xmlMenu(&$files) {
  _proratedmemberships_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function proratedmemberships_civicrm_install() {
  _proratedmemberships_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function proratedmemberships_civicrm_postInstall() {
  _proratedmemberships_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function proratedmemberships_civicrm_uninstall() {
  _proratedmemberships_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function proratedmemberships_civicrm_enable() {
  _proratedmemberships_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function proratedmemberships_civicrm_disable() {
  _proratedmemberships_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function proratedmemberships_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _proratedmemberships_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function proratedmemberships_civicrm_managed(&$entities) {
  _proratedmemberships_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function proratedmemberships_civicrm_caseTypes(&$caseTypes) {
  _proratedmemberships_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function proratedmemberships_civicrm_angularModules(&$angularModules) {
  _proratedmemberships_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function proratedmemberships_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _proratedmemberships_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function proratedmemberships_civicrm_entityTypes(&$entityTypes) {
  _proratedmemberships_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function proratedmemberships_civicrm_themes(&$themes) {
  _proratedmemberships_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---


  function proratedmemberships_civicrm_buildAmount( $pageType, &$form, &$amount ) {
    // Frontoffice membership forms.
    if (is_a($form, "CRM_Contribute_Form_Contribution_Main") && $pageType == "membership") {
      if (!empty($amount)) {
        $feeBlock = &$amount;
        foreach ($amount as &$sets) {
          if (!empty($sets['options'])) {
            foreach ($sets['options'] as &$option) {
              if (!empty($option['membership_type_id'])) {
                $membershipTypeValues = civicrm_api3("MembershipType", "get", [
                  'sequential' => 1,
                  'id' => $option['membership_type_id'],
                  ])['values'];
                if (!empty($membershipTypeValues)) {
                  _getProratedFee($membershipTypeValues);
                  foreach ($membershipTypeValues as $membershipType) {
                    if ($membershipType['id'] == $option['membership_type_id']) {
                      $option['amount'] = $membershipType['minimum_fee'];
                    }
                  }
                }
              }
            }
          }
        }
        // FIXME: Somewhere between 4.7.15 and 4.7.23 the above stopped working and we have to do the following to make the confirm page show the correct amount.
        $form->_priceSet['fields'] = $feeBlock;
      }
    }
  }

  function proratedmemberships_civicrm_membershipTypeValues( &$form, &$membershipTypeValues ) {
    // Backoffice contributions.
    _getProratedFee($membershipTypeValues);
  }

  function _getProratedFee(&$membershipTypeValues) {
    $today = getdate();
    if (in_array($today['mon'], [3,4,5,6])) {
      // Do not prorate for April-June.
      return;
    }
    foreach ( $membershipTypeValues as &$values) {
      if ($values['period_type'] != 'fixed') {
        continue;
      }

      // Starting date as set in Member type
      $start_month = substr($values['fixed_period_start_day'], 0, strlen($values['fixed_period_start_day']) - 2);

      // Rollover date
      $rollover_month = substr($values['fixed_period_rollover_day'], 0, strlen($values['fixed_period_rollover_day']) - 2);

      //$today = getdate(strtotime('2020-06-01')); // test different dates

      // Calcuate the number of months remaining in the membership period
      $months = $start_month - $today['mon'];
      if ($months < 0) {
        $months = 12 + $months;
      }
      $ratio = $months/12;

      if (($today['mon'] >= $rollover_month) && ($today['mon'] <= $start_month)) {
        $values['minimum_fee'] = $values['minimum_fee'] + $values['minimum_fee'] * $ratio;
      } else {
        $values['minimum_fee'] = $values['minimum_fee'] * $ratio;
      }
    }
  }

  function proratedmemberships_civicrm_post($op, $objectName, $objectId, &$objectRef) {
    if ($objectName == "Membership" && $op == "create") {
      if (!empty($objectRef->contribution_recur_id)) {
        $minimumFee = civicrm_api3('MembershipType', 'getvalue', [
          'return' => "minimum_fee",
          'id' => $objectRef->membership_type_id,
        ]);
        // We update the contribution recur fee amount to the correct amount.
        $recur = civicrm_api3('ContributionRecur', 'getsingle', [
          'id' => $objectRef->contribution_recur_id,
        ]);
        civicrm_api3('ContributionRecur', 'create', [
          'contact_id' => $recur['contact_id'],
          'amount' => $minimumFee,
          'frequency_interval' => $recur['frequency_interval'],
          'id' => $objectRef->contribution_recur_id,
        ]);
      }
      if (!empty($objectRef->membership_type_id)) {
        $membershipTypeDetails = civicrm_api3('MembershipType', 'getsingle', ['id' => $objectRef->membership_type_id]);
        $today = getdate();
        // checking only for purchases made on Feb or March for fixed (not rolling) type of memberships
        if ($membershipTypeDetails['period_type'] == 'fixed' && in_array($today['mon'], [2, 3])) {
          $time = sprintf('+%d %s', $membershipTypeDetails['duration_interval'], $membershipTypeDetails['duration_unit']);
          $expectedEndDate = date('Ymd', strtotime($time, strtotime($objectRef->start_date)));
          if (strtotime($objectRef->end_date) < strtotime($expectedEndDate)) {
            $expectedEndDate = '31-03-' . date('Y', strtotime($expectedEndDate));
            $objectRef->end_date = $expectedEndDate;
            $objectRef->save();
          }
        }
      }
    }
  }

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function proratedmemberships_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function proratedmemberships_civicrm_navigationMenu(&$menu) {
  _proratedmemberships_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _proratedmemberships_civix_navigationMenu($menu);
} // */
