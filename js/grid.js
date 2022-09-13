//$(document).ready(function () {
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
     editurl: "tache-post.php", //url de publication des données
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
             reloadAfterSubmit:true,
             topinfo:"",
             jqModal:true,
             align:"center",
             saveicon:[false],
             closeicon:[false],
             checkOnSubmit:false,
             checkOnUpdate:false,
             closeOnEscape: true,
             closeAfterEdit:true,
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
             url:"tache-post.php",
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
             //reloadAfterSubmit:true,
             afterSubmit: refreshGrid,
             //afterComplete: 
             modal:true,
             url:"tache-post.php",
             mtype:"POST",
             saveicon:[false],
             closeicon:[false],
             checkOnSubmit:false,
             checkOnUpdate:false,
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

            //Alimentation du tableau des tâches
    function fillGrid(listeID) {
        var gridArrayData = [];
        // affiche le message loader "chargement..."
        $("#jqGrid")[0].grid.beginReq();
        //listing des listes de tâches de la colonne "Liste" du tableau
        fillGridLists();
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
                //Vide le tableau
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

        //calcul du poucentage de taches validées si nombre de tâches totales totalTasks <>0
        var percent=(totalTasks===0)?0:Math.floor((pgBarValue/totalTasks)*100);

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

//});