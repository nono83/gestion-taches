<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8" />
    <script type="text/javascript" src="js/jquery.min.js"></script> 
    <script type="text/javascript" src="js/trirand/i18n/grid.locale-fr.js"></script>
    <script type="text/javascript" src="js/trirand/jquery.jqGrid.min.js"></script>
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

<script type="text/javascript" src="js/grid.js"></script>
<script type="text/javascript" src="js/lists.js"></script>

</body>
</html>