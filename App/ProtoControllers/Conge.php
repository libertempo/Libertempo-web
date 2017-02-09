<?php
namespace App\ProtoControllers;

/**
 * ProtoContrôleur d'un congé, en attendant la migration vers le MVC REST
 *
 * @since  1.9
 * @author Prytoegrian <prytoegrian@protonmail.com>
 * @author Wouldsmina
 */
class Conge
{
    /**
     * Liste des congés
     *
     * @return string
     */
    public function getListe()
    {
        $return    = '';
        $errorsLst = [];
        if ($_SESSION['config']['where_to_find_user_email'] == "ldap") {
            include_once CONFIG_PATH . 'config_ldap.php';
        }

        if (!empty($_POST) && !$this->isSearch($_POST)) {
            if (0 < (int) \utilisateur\Fonctions::postDemandeCongesHeure($_POST, $errorsLst)) {
                $return .= '<div class="alert alert-info">' . _('suppr_succes') . '</div>';
            }
        }
        // on initialise le tableau global des jours fériés s'il ne l'est pas déjà :
        init_tab_jours_feries();

        $return .= '<h1>' . _('user_liste_conge_titre') . '</h1>';

        if (!empty($_POST) && $this->isSearch($_POST)) {
            $champsRecherche = $_POST['search'];
            $champsSql       = $this->transformChampsRecherche($_POST);
        } else {
            $champsRecherche = [
                'type' => 'cp',
            ];
            $champsSql = [];
        }
        $params = $champsSql + [
            'p_login' => $_SESSION['userlogin'],
            'type'    => 'cp',
            'p_etat'  => 'demande',
        ]; // champs par défaut écrasés par postés

        $return .= $this->getFormulaireRecherche($champsRecherche);

        $table = new \App\Libraries\Structure\Table();
        $table->addClasses([
            'table',
            'table-hover',
            'table-responsive',
            'table-condensed',
            'table-striped',
        ]);
        $childTable = '<thead><tr><th>' . _('divers_debut_maj_1') . '</th><th>' . _('divers_fin_maj_1') . '</th><th>' . _('divers_type_maj_1') . '</th><th>' . _('divers_nb_jours_pris_maj_1') . '</th><th>Statut</th><th></th><th></th>';
        $childTable .= '</tr>';
        $childTable .= '</thead><tbody>';
        $listId  = $this->getListeId($params);
        $session = session_id();
        if (empty($listId)) {
            $colonnes = 8;
            $childTable .= '<tr><td colspan="' . $colonnes . '"><center>' . _('aucun_resultat') . '</center></td></tr>';
        } else {
            $i                        = true;
            $listeConges              = $this->getListeSQL($listId);
            $interdictionModification = $_SESSION['config']['interdit_modif_demande'];
            $affichageDateTraitement  = $_SESSION['config']['affiche_date_traitement'];
            foreach ($listeConges as $conges) {
                /** Dates demande / traitement */
                $dateDemande = '';
                $dateReponse = '';
                if ($affichageDateTraitement) {
                    if (!empty($conges["p_date_demande"])) {
                        list($date, $heure) = explode(' ', $conges["p_date_demande"]);
                        $dateDemande        = '(' . \App\Helpers\Formatter::dateIso2Fr($date) . ' ' . $heure . ') ';
                    }
                    if (null != $conges["p_date_traitement"]) {
                        list($date, $heure) = explode(' ', $conges["p_date_traitement"]);
                        $dateReponse        = '(' . \App\Helpers\Formatter::dateIso2Fr($date) . ' ' . $heure . ') ';
                    }
                }

                /** Messages complémentaires */
                if (!empty($conges["p_commentaire"])) {
                    $messageDemande = '> Demande ' . $dateDemande . ":\n" . schars($conges["p_commentaire"]);
                } else {
                    $messageDemande = '';
                }
                if (!empty($conges["p_motif_refus"])) {
                    $messageReponse = '> Réponse ' . $dateReponse . ":\n" . schars($conges["p_motif_refus"]);
                } else {
                    $messageReponse = '';
                }

                $demi_j_deb = ($conges["p_demi_jour_deb"] == "am") ? 'matin' : 'après-midi';

                $demi_j_fin         = ($conges["p_demi_jour_fin"] == "am") ? 'matin' : 'après-midi';
                $user_modif_demande = "&nbsp;";

                // si on peut modifier une demande :on defini le lien à afficher
                if (!$interdictionModification && $conges["p_etat"] != "valid") {
                    //on ne peut pas modifier une demande qui a déja été validé une fois (si on utilise la double validation)
                    $user_modif_demande = '<a href="user_index.php?session=' . $session . '&p_num=' . $conges['p_num'] . '&onglet=modif_demande">' . _('form_modif') . '</a>';
                }
                $user_suppr_demande = '<a href="user_index.php?session=' . $session . '&p_num=' . $conges['p_num'] . '&onglet=suppr_demande">' . _('form_supprim') . '</a>';
                $childTable .= '<tr class="' . ($i ? 'i' : 'p') . '">';
                $childTable .= '<td class="histo">' . \App\Helpers\Formatter::dateIso2Fr($conges["p_date_deb"]) . ' <span class="demi">' . schars($demi_j_deb) . '</span></td>';
                $childTable .= '<td class="histo">' . \App\Helpers\Formatter::dateIso2Fr($conges["p_date_fin"]) . ' <span class="demi">' . schars($demi_j_fin) . '</span></td>';
                $childTable .= '<td class="histo">' . schars($conges["ta_libelle"]) . '</td>';
                $childTable .= '<td class="histo">' . affiche_decimal($conges["p_nb_jours"]) . '</td>';
                $childTable .= '<td>' . \App\Models\Conge::statusText($conges["p_etat"]) . '</td>';
                $childTable .= '<td class="histo">';
                if (!empty($messageDemande) || !empty($messageReponse)) {
                    $childTable .= '<i class="fa fa-comments" aria-hidden="true" title="' . $messageDemande . "\n\n" . $messageReponse . '"></i>';
                }
                $childTable .= '</td>';
                $childTable .= '<td class="histo">';
                if (!$interdictionModification) {
                    $childTable .= $user_modif_demande . '&nbsp;&nbsp;';
                }
                $childTable .= ($user_suppr_demande) . '</td>' . "\n";
                $childTable .= '</tr>';
                $i = !$i;
            }
        }
        $childTable .= '</tbody>';
        $table->addChild($childTable);
        ob_start();
        $table->render();
        $return .= ob_get_clean();

        return $return;
    }

    /**
     * Liste les personnes absentes a une date précise sous un responsable, le but est en fonction du responsable d'aller chercher les personnes qu'ils gèrent et qui sont en congés à la date demandée
     * Si le responsable est vide on prend toutes les personnes de la base
     * Si la date est vide on prend la date d'aujourd'hui
     *
     * @param string $responsable, use u_prenom like 'marie'
     * @param date $date
     *
     * @return array liste des utilisateurs et leurs date de fin de congés
     */

    public function getListeAbsentDateSousResponsable($responsable = null, $date = null)
    {
        // Si la date est vide on prend la date d'aujourd'hui
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $sql         = \includes\SQL::singleton();
        $responsable = 'pierre'; // TODO get nom du responsable
        if (empty($responsable)) {
            // ne pas d'utiliser BETWEEN pour avoir les entrées inclusive
            $req = "SELECT u_nom, u_prenom, p_date_fin, p_demi_jour_fin
                FROM conges_periode as p, conges_users as u
                WHERE p.p_etat = '" . \App\Models\Conge::STATUT_VALIDATION_FINALE . "'  AND p.p_date_deb <= '$date' AND '$date' <= p.p_date_fin AND u.u_login = p.p_login";
        } else {
            if (false) {
                // TEST
                $req = "SELECT u_nom, u_prenom, p_date_fin, p_demi_jour_fin
                FROM conges_periode as p, conges_users as u
                WHERE u.u_resp_login = '$responsable' AND p.p_etat = '" . \App\Models\Conge::STATUT_VALIDATION_FINALE . "' AND p.p_date_deb <= '$date' AND '$date' <= p.p_date_fin AND u.u_login = p.p_login";
            } else {
                $ids = \App\ProtoControllers\Responsable::getUsersGrandResponsable($responsable);
                print("<pre>");
                print_r($ids);
                print("</pre>");
                $where[] = 'u.u_login IN ("' . implode('","', $ids) . '")';

                $req = "SELECT u_nom, u_prenom, p_date_fin, p_demi_jour_fin
                FROM conges_periode as p, conges_users as u
                WHERE " . implode(' AND ', $where) . " AND p.p_etat = '" . \App\Models\Conge::STATUT_PREMIERE_VALIDATION . "' AND p.p_date_deb <= '$date' AND '$date' <= p.p_date_fin AND u.u_login = p.p_login";
            }
        }
        print_r($req . '<br />');
        $res = $sql->query($req);

        $listeAbsents = [];
        while ($data = $res->fetch_array()) {
            $absent             = array();
            $absent['nom']      = $data['u_nom'];
            $absent['prenom']   = $data['u_prenom'];
            $absent['date_fin'] = $data['p_date_fin'] . ' ' . $data['p_demi_jour_fin'];
            $listeAbsents[]     = $absent;
        }

        var_dump($listeAbsents);
        return $listeAbsents;
        // return [
        //     ['pierre', 'point', '2017-02-09', 'am'],
        //     ['paolo', 'durand', '2017-02-21', 'pm'],
        //     ['jean', 'gauthier', '2017-02-14', 'pm']
        // ];
    }

    /**
     * Y-a-t-il une recherche dans l'avion ?
     *
     * @param array $post
     *
     * @return bool
     */
    protected function isSearch(array $post)
    {
        return !empty($post['search']);
    }

    /**
     * Retourne le formulaire de recherche de la liste
     *
     * @param array $champs Champs de recherche (postés ou défaut)
     *
     * @return string
     */
    protected function getFormulaireRecherche(array $champs)
    {
        $session = session_id();
        $form    = '';
        $form    = '<form method="post" action="" class="form-inline search" role="form">';
        $form .= '<div class="form-group"><label class="control-label col-md-4" for="statut">Statut&nbsp;:</label><div class="col-md-8"><select class="form-control" name="search[p_etat]" id="statut">';
        foreach (\App\Models\Conge::getOptionsStatuts() as $key => $value) {
            $selected = (isset($champs['p_etat']) && $key == $champs['p_etat'])
            ? 'selected="selected"'
            : '';
            $form .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $form .= '</select></div></div>';
        $form .= '<div class="form-group "><label class="control-label col-md-4" for="type">Type&nbsp;:</label><div class="col-md-8"><select class="form-control" name="search[type]" id="type">';
        foreach (\utilisateur\Fonctions::getOptionsTypeConges() as $key => $value) {
            $selected = (isset($champs['type']) && $key == $champs['type'])
            ? 'selected="selected"'
            : '';
            $form .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $form .= '</select></div></div>';
        $form .= '<div class="form-group"><label class="control-label col-md-4" for="annee">Année&nbsp;:</label><div class="col-md-8"><select class="form-control" name="search[annee]" id="sel1">';
        foreach (\utilisateur\Fonctions::getOptionsAnnees() as $key => $value) {
            $selected = (isset($champs['annee']) && $key == $champs['annee'])
            ? 'selected="selected"'
            : '';
            $form .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $form .= '</select></div></div><div class="form-group"><div class="input-group"><button type="submit" class="btn btn-default"><i class="fa fa-search" aria-hidden="true"></i></button>&nbsp;<a href="' . ROOT_PATH . 'utilisateur/user_index.php?session=' . $session . '&onglet=liste_conge" type="reset" class="btn btn-default">Reset</a></div></div></form>';

        return $form;
    }

    /**
     * Transforme les champs de recherche afin d'être compris par la bdd
     *
     * @param array $post
     *
     * @return array
     */
    protected function transformChampsRecherche(array $post)
    {
        $champs = [];
        $search = $post['search'];
        foreach ($search as $key => $value) {
            if ('annee' === $key) {
                $champs['dateDebut'] = ((int) $value) . '-01-01';
                $champs['dateFin']   = ((int) $value) . '-12-31';
            } else {
                $champs[$key] = $value;
            }
        }

        return $champs;
    }

    /*
     * SQL
     */

    /**
     * Retourne une liste d'id de congés
     *
     * @param array $params Paramètres de recherche
     *
     * @return array
     */
    protected function getListeId(array $params)
    {
        if (!empty($params)) {
            $where = [];
            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'dateDebut':
                        $where[] = 'p_date_deb >= "' . $value . '"';
                        break;
                    case 'dateFin':
                        $where[] = 'p_date_deb <= "' . $value . '"';
                        break;
                    case 'type':
                        $where[] = 'CTA.ta_short_libelle = "' . $value . '"';
                        break;
                    default:
                        $where[] = $key . ' = "' . $value . '"';
                        break;
                }
            }
        }
        $ids = [];
        $sql = \includes\SQL::singleton();
        $req = 'SELECT p_num AS id
                FROM conges_periode CP
                    INNER JOIN conges_type_absence CTA ON (CP.p_type = CTA.ta_id) '
            . ((!empty($where)) ? ' WHERE ' . implode(' AND ', $where) : '');
        $res = $sql->query($req);
        while ($data = $res->fetch_array()) {
            $ids[] = (int) $data['id'];
        }

        return $ids;
    }

    /**
     * Retourne une liste de congés
     *
     * @param array $listId
     *
     * @return array
     */
    protected function getListeSQL(array $listId)
    {
        if (empty($listId)) {
            return [];
        }
        $sql = \includes\SQL::singleton();
        $req = 'SELECT CP.*, CTA.ta_libelle
                FROM conges_periode CP
                    INNER JOIN conges_type_absence CTA ON (CP.p_type = CTA.ta_id)
                WHERE p_num IN (' . implode(',', $listId) . ')
                ORDER BY p_date_deb DESC';

        return $sql->query($req)->fetch_all(MYSQLI_ASSOC);
    }
}
