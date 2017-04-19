<?php

define('ROOT_PATH', '../');
require_once ROOT_PATH . 'define.php';

$session = (isset($_GET['session']) ? $_GET['session'] : ((isset($_POST['session'])) ? $_POST['session'] : session_id()));

include_once ROOT_PATH . 'fonctions_conges.php';
include_once INCLUDE_PATH . 'fonction.php';
include_once INCLUDE_PATH . 'session.php';
include_once ROOT_PATH . 'fonctions_calcul.php';

$config = new \App\Libraries\Configuration();
if ($config->getMailFromLdap()) {
    include CONFIG_PATH . 'config_ldap.php';
}

// SERVER
$PHP_SELF = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
// GET / POST
$onglet = getpost_variable('onglet');

/*********************************/
/*   COMPOSITION DES ONGLETS...  */
/*********************************/

$onglets = array();

$onglets['liste_conge'] = _('user_liste_conge');

if ($config->canUserSaisieDemande() || $config->canUserSaisieMission()) {
    $onglets['nouvelle_absence'] = _('divers_nouvelle_absence');
}

if ($config->canUserEchangeRTT()) {
    $onglets['echange_jour_absence'] = _('user_onglet_echange_abs');
}

if ($config->isHeuresAutorise()) {
    if ($config->canUserSaisieDemande() || $config->canUserSaisieMission()) {
        $onglets['ajout_heure_repos'] = _('divers_ajout_heure_repos');
    }
    $onglets['liste_heure_repos'] = _('user_liste_heure_repos');
    
    if ($config->canUserSaisieDemande() || $config->canUserSaisieMission()) {
        $onglets['ajout_heure_additionnelle'] = _('divers_ajout_heure_additionnelle');
    }
    $onglets['liste_heure_additionnelle'] = _('user_liste_heure_additionnelle');
}

if ($config->canUserChangePassword()) {
    $onglets['changer_mot_de_passe'] = _('user_onglet_change_passwd');
}

if ( !isset($onglets[ $onglet ]) && !in_array($onglet, array('modif_demande','suppr_demande','modif_heure_repos', 'modif_heure_additionnelle'))) {
    $onglet = 'liste_conge';
}

/*********************************/
/*   COMPOSITION DU HEADER...    */
/*********************************/

$add_css = '<style>#onglet_menu .onglet{ width: ' . (str_replace(',', '.', 100 / count($onglets))) . '% ;}</style>';
header_menu('', 'Libertempo : ' . _('user'), $add_css);

/*********************************/
/*   AFFICHAGE DES ONGLETS...  */
/*********************************/

echo '<div id="onglet_menu">';
foreach ($onglets as $key => $title) {
    echo '<div class="onglet ' . ($onglet == $key ? ' active' : '') . '" >
        <a href="' . $PHP_SELF . '?session=' . $session . '&onglet=' . $key . '">' . $title . '</a>
    </div>';
}
echo '</div>';

/*********************************/
/*   AFFICHAGE DU RECAP ...    */
/*********************************/

echo "<div class=\"wrapper\">\n";
echo '<h3>' . _('tableau_recap') . '</h3>';
echo affiche_tableau_bilan_conges_user($_SESSION['userlogin']);
echo "<hr/>\n";
echo "</div>\n";

/*********************************/
/*   AFFICHAGE DE L'ONGLET ...    */
/*********************************/

echo '<div class="' . $onglet . ' wrapper">';
include ROOT_PATH . 'utilisateur/user_' . $onglet . '.php';
echo '</div>';

/*********************************/
/*   AFFICHAGE DU BOTTOM ...   */
/*********************************/

bottom();
