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

    

    //listing des listes de tâches dans la combobox
    fillComboLists();

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