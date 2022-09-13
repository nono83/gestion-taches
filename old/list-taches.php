<?php
header('Content-Type: text/html; charset=utf-8');
require_once ('tache.php');
require_once ('liste.php');
require_once ('_config.inc.php');

//Traitement du CRUD des tâches en fonction du paramétre "oper" envoyé par la jqgrid
$tache=new Tache();
if (isset($_POST['oper'])){
    switch ($_POST['oper']){
        
        case 'add':
            $tache->setNom($_POST['Tache']);
            $tache->setStatut($_POST['Statut']);
            $tache->setListe_id($_POST['Liste']);
            $tache->add();  
            break;
        
        case 'edit':
            $tache->setId($_POST['TacheID']);
            $tache->setNom($_POST['Tache']);
            $tache->setStatut($_POST['Statut']);
            $tache->setListe_id($_POST['Liste']);
            $tache->update();  
            break;
        
        case 'del':
            $tache->setId($_POST['id']);
            $tache->del();  
            break;

        case 'listing':
            try {
                //$pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, '');
                $pdo = new PDO("mysql:host=".SERVER.";dbname=".BASE, USER, '');
                $pdo->exec("set names utf8"); 
                //Clause where de la requête. Si $_POST['listeID']==0 on retourne la liste entière des tâches sinon juste la liste des tâches liées à la l'id de la liste
                $whereClause=$_POST['listeID']==0?'':'where taches.liste_id=:id';
                $statement = $pdo->prepare('SELECT taches.id, taches.nom as tache, taches.statut as statut, listes.nom as liste  FROM taches INNER JOIN listes ON taches.liste_id=listes.id '.$whereClause);
                $statement->bindValue(':id',  $_POST['listeID'], PDO::PARAM_INT);
                $statement->setFetchMode(PDO::FETCH_CLASS, 'taches'); 
                if ($statement->execute()) {
                    $tachearray = array();
                    while ($tache = $statement->fetch(PDO::FETCH_ASSOC)) {
                /*       echo '<pre>';
                        print_r($tache);
                        echo '</pre>';  */
                        $tachearray[]= $tache;
                        }
                }else {
                    $errorInfo = $statement->errorInfo();
                    echo 'Message : '.$errorInfo[2];
                } 
            } catch (PDOException $e) {
                echo 'Impossible de se connecter à la base de données';
            }
            
            echo json_encode($tachearray);
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <script type="text/ecmascript" src="js/jquery.min.js"></script> 
    <script type="text/ecmascript" src="js/trirand/i18n/grid.locale-fr.js"></script>
    <script type="text/ecmascript" src="js/trirand/jquery.jqGrid.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" media="screen" href="css/trirand/ui.jqgrid-bootstrap.css" />
	<script>
		$.jgrid.defaults.width = 780;
		$.jgrid.defaults.styleUI = 'Bootstrap';
	</script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <title>Liste des taches</title>
</head>
<body>

<!-- fenêtre modale  -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" >Nouvelle liste</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="modal-text"></div>  
        <div class="form-group mb-3">
            <label class="text-muted" for="list-name">Liste</label>
            <input id="list-name" class="form-control" type="text" name="list-name" placeholder="Saisissez la nouvelle liste" aria-describedby="email">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="action-modal" class="btn btn-primary">Ajouter</button>
        <button type="button" id="close-modal" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- combobox des listes de taches -->
<div class="container-fluid" style="margin:5px 50px" >
    <h1>Gestion des tâches</h1>
    <hr style="margin-bottom:35px">
    <div class="row py-5 mt-2">
        <div class="col-md-4 col-lg-3 col-xl-2" ></div>
        <div class="col-md-4 col-lg-3 col-xl-2" >
            <div class="form-group ">
                <select id="lists" class="form-control " name="liste" id="liste">
                    <option  value="">Sélectionnez une liste</option>
                </select>
            </div>
        </div>

        <!-- Boutons -->
        <div class="col-md-4 col-lg-3 col-xl-2" ><div class="form-group ">
                <button id="add-list" class="btn btn-success  font-weight-bold " data-toggle="modal" data-target="#Modal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 16">
                    <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5z"/>
                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                    </svg>
                </button>
                <button id="update-list" class="btn btn-success  font-weight-bold " data-toggle="modal" data-target="#Modal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                    <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                    </svg>
                </button>
                <button id="del-list" class="btn btn-success  font-weight-bold" data-toggle="modal" data-target="#Modal"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                    </svg>
                </button>
            </div>   </div>
    </div>

    <!-- Grille des taches -->
    <div class="row" >
        <div class="col-md-2 col-lg-2 col-xl-2" ></div>
        <div class="py-3 col-md-7 col-lg-8 col-xl-2">
            <table  id="jqGrid"></table>
            <div id="jqGridPager"></div>
        </div>
        <div class="col-md-3 col-lg-2 col-xl-2" ></div>
    </div>
</div>

<!-- Progress bar -->
<div class="row" >
        <div class="col-md-2 col-lg-2 col-xl-2" ></div>
        
        <div class="alert alert-success py-3 col-md-7 col-lg-8 col-xl-2" style="padding:0px 10px;margin-top: 35px;">
        <h3>Etat d'avancement des tâches</h3>
            <div class="progress">
                <div id="pgBar" class=""  role="progressbar" style="width: 35%;" aria-valuenow="" aria-valuemin="0" aria-valuemax=""></div>
                </div>
            </div>
        <div class="col-md-3 col-lg-2 col-xl-2" ></div>
    </div>
</div>

<!-- champ caché définissant le mode d'édition de la dialog form de la grille (ajout ou édition) -->
<input type="hidden" id="mode">

    <script type="text/javascript"> 
        $(document).ready(function () {
            //Chargement du tableau des taches liées à  la liste  sélectionnée
            $("#lists").change(function(){
                fillGrid($("#lists").val());
            });

           
            //Déclenchement des requêtes CRUD ajax en fonction du libellé du bouton "action-modal"
            $("#action-modal").click(function(){
                switch ($("#action-modal").text()){
                    case "Ajouter":
                        if (listExists($('#list-name').val())){
                            showDialog('list exists');
                        }else{
                            $.ajax({
                                url: "list-post.php",
                                data: { nom: $('#list-name').val(), action: 'add'},
                                type: "POST",
                                success: function (result) {
                                    //Rafraichissement du listing des listes dans la grilles
                                    fillGridLists();
                                    //Rafraichissement de la combo après ajout
                                    fillComboLists();
                                    //Déclenchement de la fermeture de la modale 
                                    $('#close-modal').trigger('click');
                                } 
                            });   
                        }
                        break;

                    case "Sauvegarder":
                        $.ajax({
                            url: "list-post.php",
                            data: { listeID: $('#lists').val(), nom: $('#list-name').val(), action: 'update'},
                            type: "POST",
                            success: function (result) {
                                //Rafraichissement de la combo après sauvegarde
                                fillComboLists();
                                //Rafraichissement de la liste des tâches
                                fillGrid($("#lists").val());
                                //Déclenchement de la fermeture de la modale 
                                $('#close-modal').trigger('click');
                            } 
                        });   
                        break;       
                        
                    case "Supprimer":
                        $.ajax({
                            url: "list-post.php",
                            data: { listeID:  $('#lists').val(),  action: 'del'},
                            type: "POST",
                            success: function (result) {
                                showDialog("del confirmation");
                                //Rafraichissement de la combo après suppression
                                fillComboLists();
                                //Rafraichissement de la liste des tâches
                                fillGrid(0);
                            } 
                        });
                        break;    
                }   
                    
            });

             //Bouton maj d'une  liste de la fenêtre modale
             $("#update-list").click(function(){
                if($("#lists").val()==0){
                    //Si aucun élément sélectionné on affiche un message pour en informer l'utilisateur 
                    showDialog("no selected"); 
                }else{
                    showDialog("update");
                }
            });

            //Bouton suppression d'une liste de la fenêtre modale
            $("#del-list").click(function(){
                if($('#lists  :selected').val()==0){
                    //Si aucun élément sélectionné on affiche un message pour en informer l'utilisateur 
                    showDialog("no selected"); 
                }else{
                    //Sinon affichage de la fenêtre modale de suppression
                    showDialog("del");
                }
            });

             //Bouton ajout d'une nouvelle liste de la fenêtre modale
             $("#add-list").click(function(){
                    showDialog("add");
            });

            //Affichage de la fenêtre modale de gestion des listes  en fonction des actions déclenchées 
            function showDialog(action){
                switch (action){
                    case "add":
                        $('.modal-title').text('Ajout d\'une nouvelle liste');
                        $('#modal-text').hide();
                        $('.modal-body .form-group').show();
                        $('#list-name').val('');
                        $('#action-modal').text("Ajouter"); 
                        $('#action-modal').show();  
                        break;

                    case "update":
                        $('.modal-title').text('Mise  jour d\'une liste');
                        $('#modal-text').hide();
                        $('.modal-body .form-group').show();
                        $('#list-name').val($('#lists option:selected').text());
                        $('#action-modal').text("Sauvegarder"); 
                        $('#action-modal').show();  
                        break;

                    case "del":
                        $('.modal-title').text('Suppression d\'une liste');
                        $('.modal-body .form-group').hide();
                        //$('.modal-body').text('Si vous supprimez cette liste, toutes les taches associées seront supprimées également. Confirmez vous la suppression de cette liste ?') ;
                        $('#modal-text').show();
                        $('#modal-text').text('Si vous supprimez cette liste, toutes les taches associées seront supprimées également. Confirmez vous la suppression de cette liste ?'); 
                        $('#action-modal').text("Supprimer"); 
                        $('#action-modal').show();  
                        break;

                     case "del confirmation":
                        $('.modal-title').text('Suppression d\'une liste');
                        $('.modal-body .form-group').hide();
                        $('#action-modal').hide();  
                        $('#modal-text').show();
                        $('#modal-text').text('L\'enregistrement a bien été supprimé.');
                        break;

                    case "no selected":
                        $('.modal-title').text('Aucun enregistrement sélectionné');
                        $('.modal-body .form-group').hide();
                        $('#action-modal').hide();  
                        $('#modal-text').show();
                        $('#modal-text').text("Veuillez sélectionner un élément dans la liste pour poursuivre la mise à jour ou la suppression.");
                        break;

                    case "list exists":
                        $('.modal-title').text('Enregistrement existant');
                        $('.modal-body .form-group').hide();
                        $('#action-modal').hide();  
                        $('#modal-text').show();
                        $('#modal-text').text("L'enregistrement que vous souhaitez enregistrer existe déjà.");
                        break;   
                        
                        
                }
            }

            //Alternance de la couleur de fonds des cellules du tableau
            $.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";

            //gestion du tableau des taches
            $("#jqGrid").jqGrid({
                mtype: "POST",// les données sont envoyées en POST
                colModel: [ //Définition des colonnes. Si editable=true la donnée de la colonne est éditable
                   /*  {label: "Actions",
                        name: "actions",
                        width: 100,
                        formatter: "actions",
                        align:'center',
                        formatoptions: {
                            keys: true,
                            editOptions: {},
                            addOptions: {},
                            delOptions: {height:150,width:400,delicon:[false],closeicon:[false],resize:false}
                        }       
                    }, */
                    {label: 'Tache ID',
                        name: 'TacheID',
                        hidden: true,//colonne des id non visible
                        editable: true,
                        key:true
                    },
                    {
						label: 'Tache',
                        name: 'Tache',
                        width: 150,
                        editable: true,
                        editrules:{
                            custom_func: checkTask,
                            custom: true,
                            required: true,
                        }
                    },
                    {
						label: 'Statut',
                        name: 'Statut',
                        width: 150,
                        editable: true,
                        edittype:'select',
                        editoptions:{value:"validée:validée;non validée:non validée"}
                    },
                    {
						label: 'Liste',
                        name: 'Liste',
                        width: 150,
                        editable: true,
                        edittype:'select'
                    }
                ],
                viewrecords: true, //Affiche le numéro de page courante dans la pagination et le nombre d'enregistrements
                editurl: "list-taches.php", //url de publication des données
                width: 780,
                height: 200,
                emptyrecords: 'Aucun enregistrement',
				datatype: 'local',
                loadonce:true,
                pager: "#jqGridPager",//Toolbar
				//caption: "Taches",
                autowidth: true,
                rowNum:10,
                sortname:'Tache',//Tri par le libellé des tâches par défaut 
    	        sortorder: "asc",
                ondblClickRow: function(rowid) {
                $("#jqGrid").jqGrid('editGridRow', 
                    rowid,{
                        left:( $(window).width() - 400 ) / 2+$(window).scrollLeft(),
                        top: ($(window).height() - 250 ) / 2+$(window).scrollTop(),
                        width:400,
                        height:280,
                        reloadAfterSubmit:false,
                        topinfo:"",
                        jqModal:true,
                        align:"center",
                        saveicon:[false],
                        closeicon:[false],
                        checkOnSubmit:false,
                        checkOnUpdate:false,
                        closeOnEscape: true,
                        savekey: [true],	
                        resize:false,
                        recreateForm:true,				
                        afterComplete:function(sel, o) {
                            refreshGrid();
                        },
                        //définition du mode de la dialog form (edit ou add). Utilisé dans la fonction de validation des données "checkTask"  
                        beforeShowForm : function (formid)
                        {
                            $("#mode").val("edit");
                        }
                    } 
                );
            },
            });

            
            //Barre de navigation 
            jQuery("#jqGrid")
                    .jqGrid("navGrid","#jqGridPager",
                    {add:true,addtitle:"Ajouter un enregistrement", edit:true, del:true, refresh:true,refreshtitle:"Recharger la liste", search:true,searchtitle:"Recherche",alertcap:"Avertissements",alerttext:"Veuillez sélectionner un enregistrement"}, 
                    //options
                    {   // options pour edition
                        left:( $(window).width() - 400 ) / 2+$(window).scrollLeft(),
                        top: ($(window).height() - 250 ) / 2+$(window).scrollTop(),
                        width:400,
                        height:280,
                        reloadAfterSubmit:true,
                        //beforeSubmit: setProgressBar,
                        modal:true,
                        saveicon:[false],
                        closeicon:[false],
                        url:"list-taches.php",
                        mtype:"POST",
                        checkOnSubmit:false,
                        checkOnUpdate:false,
                        closeOnEscape:true,
                        resize:false,
                        afterComplete: refreshGrid,
                        closeAfterEdit:true,
                        recreateForm:true,//Réinitialise le formulaire à chaque appel 
                        //définition du mode de la dialog form (edit ou add). Utilisé dans la fonction de validation des données "checkTask"  
                        beforeShowForm : function (formid)
                        {
                            $("#mode").val("edit");
                        }
                    }, 
                    {// options pour l'ajout
                        left:( $(window).width() - 400 ) / 2+$(window).scrollLeft(),
                        top: ($(window).height() - 250 ) / 2+$(window).scrollTop(),
                        width:400,
                        height:280,
                        reloadAfterSubmit:true,
                        afterSubmit: refreshGrid,
                        //afterComplete: 
                        modal:true,
                        url:"list-taches.php",
                        mtype:"POST",
                        saveicon:[false],
                        closeicon:[false],
                        checkOnSubmit:true,
                        checkOnUpdate:true,
                        closeOnEscape:true,
                        resize:false,
                        closeAfterAdd:true,
                        recreateForm:true,//Réinitialise le formulaire à chaque appel 
                        //définition du mode de la dialog form (edit ou add). Utilisé dans la fonction de validation des données "checkTask"  
                        beforeShowForm : function (formid)
                        {
                            $("#mode").val("add");
                        }
                    }, 
                    {// Options pour la suppression 
                        left:( $(window).width() - 300 ) / 2+$(window).scrollLeft(),
                        top: ($(window).height() - 130 ) / 2+$(window).scrollTop(),
                        width:300,
                        height:130,
                        resize:false,
                        reloadAfterSubmit:false,
                        afterComplete: refreshGrid
                    }, 
                    {// options pour la recherche
                        left:( $(window).width() - 500 ) / 2+$(window).scrollLeft(),
                        top: ($(window).height() - 250 ) / 2+$(window).scrollTop(),
                        width:500,
                        height:250,
                        resize:false,
                        multipleSearch : true// Possibilité de réaliser une recherche multicritéres
                    } ,
                    {closeOnEscape:true}, // ferme la fenêtre dialog quand l'utilisateur appuie sur la touche ESC 
                   
                    )

            //Chargement initiale du tableau des tâches et du listing des listes de tâches dans la combobox et la grille
            fillGrid(0);
            //listing des listes de tâches dans la combobox
            fillComboLists();
            //listing des listes de tâches de la colonne "Liste" du tableau
            fillGridLists();

            //Alimentation du tableau des tâches
            function fillGrid(listeID) {
                var gridArrayData = [];
				// affiche le message loader "chargement..."
				$("#jqGrid")[0].grid.beginReq();
                $.ajax({
                    url: "tache-post.php",
                    dataType:"json",
                    data: { listeID: listeID, oper:'listing'},
                    type: "POST",
                    success: function (result) {
                        var tacheValides=0;
                        for (var i = 0; i < result.length; i++) {
                             var tache = result[i];
                            //if(tache.statut=="validée") tacheValides+=1;

                             gridArrayData.push({
                                TacheID: tache.id, 
                                Tache: tache.tache,
                                Statut: tache.statut,
                                Liste: tache.liste
                            });                           
                        } 
                        //Vidu tableau
                        $("#jqGrid").setGridParam({ data: null });
						// Ajoute les données à la grille
						$("#jqGrid").jqGrid('setGridParam', { data: gridArrayData});
                        //$('#jqGrid').setGridParam({ page: 1, datatype: "json" });
						// cache le message loader "chargement..."
						$("#jqGrid")[0].grid.endReq();
						// Rafraichissement du tableau
						$("#jqGrid").trigger('reloadGrid'); 
                        //Initialisation de la progressBar
                        setProgressBar();
                    }
                });

            }

            //Alimente la combo des listes de tâches
            function fillComboLists(){
                $('#lists').empty();
                $('#lists').append($('<option>', {value:0, text:"Toutes les listes"}));
                $.ajax({
                    url: "list-post.php",
                    dataType:"json",
                    data: { action: 'listing'},
                    type: "POST",
                    success: function (result) {
                        result.forEach(function(list){
                            $('#lists').append($('<option>', {value:list.id, text:list.nom}));
                                   
                        });       
                    }
                });
            }

             //Chargement dynamique du listing des listes dans la colonne 'Liste' du tableau
             function fillGridLists(){
                var lists='';
                $.ajax({
                    url: "list-post.php",
                    dataType:"json",
                    data: { action: 'listing'},
                    type: "POST",
                    success: function (result) {
                        result.forEach(function(list){
                            lists+=';' + list.id + ':' + list.nom;   
                        });  
                        lists=lists.substr(1);
                        jQuery("#jqGrid").setColProp('Liste', { editoptions: {size:3, value: lists.toString()} })      
                    }
                });

            }

            //Construction de la progress bar en fonction du nombre de tâches réalisées
            function setProgressBar(){
                var rows = $('#jqGrid').jqGrid('getRowData');
                var totalTasks=rows.length;

                //var rows = jQuery("#jqGrid").getDataIDs();
                var pgBarValue=0;
                for(i=0;i<rows.length;i++){
                    if (rows[i].Statut=='validée'){pgBarValue++;}
                    
                    //console.log(rows[i].Statut);
                }

                //calcul du poucentage de taches validées
                var percent=Math.floor((pgBarValue/totalTasks)*100);

                //Suppression de toute les classes
                $( "#pgBar" ).removeClass();

                //affectation des valeurs
                $("#pgBar").css("width", percent + '%');
                $("#pgBar").attr("aria-valuenow",pgBarValue);
                $("#pgBar").attr("aria-valuemax",totalTasks);
                $("#pgBar").text(pgBarValue + '/' + totalTasks);

                //Code couleur: rouge si <33% de tâches réalisées, bleue entre 33% et 66% , verte si > 66%  
                if (percent<=33){
                    $( "#pgBar" ).addClass( "progress-bar  progress-bar-striped progress-bar-danger" );
                }
                else if (percent>=33&&percent<=66){
                    $( "#pgBar" ).addClass( "progress-bar  progress-bar-striped progress-bar-info" );
                }else{
                    $( "#pgBar" ).addClass( "progress-bar  progress-bar-striped progress-bar-success" );
                }
               
            }
            
            function refreshGrid(){
                fillGrid($("#lists").val());
            } 

            //Contrôle de la validation des données à la sauvegarde d'une tâche 
             function checkTask(value, column) {
                if ( value.length>50)
                    return [false, "La tâche ne peut pas avoir plus de 50 caractères"];
                  else if($("#mode").val()=="add" && taskExists(value))
                    return [false, "La tâche existe déja"];  
                else
                    return [true, ""];
            }

            function taskExists(value){
                //var rows = $("#jQGridDemo > tbody > tr:has(td:contains('" + str + "'))");
               // var rows = $("#jQGridDemo > tbody > tr:has(td:contains('test15'))");
                var rows = $('#jqGrid').jqGrid('getRowData');

                for(i=0;i<rows.length;i++){
                    if (rows[i].Tache==value){
                        return true;
                    }
                }  
                return false;
            }

            //Test si le nom de la liste saisi existe déjà dans la combobox
            function listExists(value){
                //Test l'expression exacte
                if($("#lists option").filter(function() {return $(this).text() === value;}).length>0){
                //Test l'expression contenue    
                //if($('#lists').find("option:contains('" + value  + "')").length){
                    return true;
                }
                return false;
            }
        });
    </script>

</body>
</html>