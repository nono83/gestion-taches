
<?php

include('../secure/db.inc.php'); 
mysql_query("SET NAMES UTF8");

?>

<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8">
	<title>Commande</title>
	<link type="text/css" href="css/base/jquery.ui.all.css" rel="stylesheet" />
	<link type="text/css" href="css/ui.jqgrid.css" rel="stylesheet" />
	<link type="text/css" href="css/redmond/jquery-ui-1.8.2.custom.css" rel="stylesheet" />
	<link type="text/css" href="css/ui.multiselect.css" rel="stylesheet" />
   
	<script src="js/jquery-1.8.2.js"></script>
	<script src="js/jquery-1.8.2.min.js"></script>
	<script src="js/jquery-ui-1.8.24.custom.min.js"></script>
	<script src="js/i18n/grid.locale-fr.js" type="text/javascript"></script>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/jquery.layout.js" type="text/javascript"></script>
	<script type="text/javascript">
		$.jgrid.no_legacy_api = true;
		$.jgrid.useJSON = true;
	</script>
	<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
	<style>
	#contenu { margin-top: 10px; }
	#contenu label { width: 20%; display: block; float: left;}
	#contenu div { width: 30%; display: block; float: left;}
	.ui-state-error { padding: .3em; }
	.ui-layout-west .ui-jqgrid tr.jqgrow td { border-bottom: 0px none;}
	.ui-datepicker {z-index:1200;}
	td,th,tr,div { font-family: verdana; font-size: 12px; }
	</style>
	<script>
	
	$(function() {
	
	//$( "#date" ).datepicker();
	//$( "#date" ).datepicker( "option", "dateFormat", "dd/mm/yy" );
		$( "#date" ).datepicker({
			dateFormat: "dd/mm/yy"	
		});
		

		$( "#dialog-confirm" ).dialog({
		  autoOpen: true,
		  resizable: false,
		  height:140,
		  modal: true,
		  buttons: {
			"OK": function() {
			  $( this ).dialog( "close" );
			},
		  }
		});
		
		var lastsel;
		jQuery("#list-commande").jqGrid({
		datatype: "local",
		colNames:["ID","COMMANDE_DETAIL_ID","PRODUIT","QTE","REMISE","PU HT","TOTAL HT","TAUX_TVA","TOTAL TVA","TOTAL TTC","GRATUIT"],
		colModel:[
		{name:"ID",index:"ID",width:"0", key:true}
		,
		{name:"COMMANDE_DETAIL_ID",index:"COMMANDE_DETAIL_ID",width:"0",editable:false}
		,
		{name:"PRODUIT",index:"PRODUIT",width:"140",editrules:{required:true},editable:true,edittype:'select',
			editoptions:{
				value:"  :  ;CICOLEA 30 ml:CICOLEA 30 ml;Cicolea sample 2,5 ml:Cicolea sample 2,5 ml;Cicolea sample 2 ml:Cicolea sample 2 ml;huile d'olive 3 l:huile d'olive 3 l;VISOLEA 50 ml:VISOLEA 50 ml;VISOLEA SAMPLE 1,5 ml:VISOLEA SAMPLE 1,5 ml",
				dataEvents: [{        //this triggers on change of department combo-box
                    type: 'change',
                    fn: function(e){
						var rowid = $("#list-commande").jqGrid('getGridParam','selrow');
						data=$("#list-commande").jqGrid('getRowData',rowid);
						//jQuery("#list-commande").jqGrid('setRowData',rowid,{PU_HT:'16.50'});
						if (this.value=="VISOLEA 50 ml"){
							$('select#PU_HT').val('41.50');	
						}else{
							$('select#PU_HT').val('18.33');
						}
						var pu_ht =$('select#PU_HT').val();
						if(isNaN($('input#REMISE').val())==true||$.trim($('input#REMISE').val())==""){
							$('input#REMISE').val(0);
						}
						var remise=$('input#REMISE').val();
						var tx_tva =$('select#TAUX_TVA').val();	
						//var qte=jQuery("#list-commande").jqGrid('getCell', rowid, 'QTE');
						var qte=$('input#QTE').val();
						var prix_ht=parseInt(qte)*parseFloat(pu_ht.replace(',','.'));
						prix_ht=prix_ht-(prix_ht*parseFloat(remise)/100);
						var prix_tva=parseFloat(prix_ht)*tx_tva;
						var prix_ttc=parseFloat(prix_ht)+parseFloat(prix_tva);
						/*$("#list-commande").jqGrid('setRowData',rowid,{PRIX_HT:prix_ht.toFixed(2)});
						$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TVA:prix_tva.toFixed(2)});
						$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TTC:prix_ttc.toFixed(2)});*/
						$('input#PRIX_HT').val(prix_ht.toFixed(2));
						$('input#PRIX_TVA').val(prix_tva.toFixed(2));
						$('input#PRIX_TTC').val(prix_ttc.toFixed(2));
						//set_total();
					}
				}]
			}
		}
		,
		{name:"QTE",index:"QTE",width:"40",editrules:{required:true,number:true},editable:true,edittype:'text',formatter:'text',
			editoptions:{
				//value:"1:1;2:2;3:3;4:4;5:5;6:6;7:7;8:8;9:9",
				//value:get_qte_values,
				defaultValue:1,
				dataEvents: [{        //this triggers on change of department combo-box
                    type: 'change',
                    fn: function(e){
						var rowid = $("#list-commande").jqGrid('getGridParam','selrow');
						data=$("#list-commande").jqGrid('getRowData',rowid);
						//var pu_ht =data['PU_HT'];
						var pu_ht=$('select#PU_HT').val();
						var tx_tva =$('select#TAUX_TVA').val();	
						var remise=$('input#REMISE').val();	
						var prix_ht=parseInt(this.value)*parseFloat(pu_ht.replace(',','.'));
						prix_ht=prix_ht-(prix_ht*parseFloat(remise)/100);
						var prix_tva=parseFloat(prix_ht)*tx_tva;
						var prix_ttc=parseFloat(prix_ht)+parseFloat(prix_tva);
						$('input#PRIX_HT').val(prix_ht.toFixed(2));
						$('input#PRIX_TVA').val(prix_tva.toFixed(2));
						$('input#PRIX_TTC').val(prix_ttc.toFixed(2));
						//$("#list-commande").jqGrid('setRowData',rowid,{PRIX_HT:prix_ht.toFixed(2)});
						//$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TVA:prix_tva.toFixed(2)});
						//$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TTC:prix_ttc.toFixed(2)});
						//$("#list-commande").jqGrid('setRowData',rowid,{QTE:this.value});
						//set_total();
						//this.hidden=true;
					}
				}]
			}
		}
		,
		{name:"REMISE",index:"REMISE",width:"60",editrules:{required:true,number:true},editable:true,edittype:'text',formatter:'text',
			editoptions:{
				//value:"1:1;2:2;3:3;4:4;5:5;6:6;7:7;8:8;9:9",
				//value:get_qte_values,
				value:"0",
				dataEvents: [{        //this triggers on change of department combo-box
                    type: 'change',
                    fn: function(e){
						var rowid = $("#list-commande").jqGrid('getGridParam','selrow');
						data=$("#list-commande").jqGrid('getRowData',rowid);
						//var pu_ht =data['PU_HT'];
						var pu_ht=$('select#PU_HT').val();
						var tx_tva =$('select#TAUX_TVA').val();	
						if(isNaN($('input#REMISE').val())==true||$.trim($('input#REMISE').val())==""){
							$('input#REMISE').val(0);
						}
						var remise=$('input#REMISE').val();
						var qte=$('input#QTE').val();
						var prix_ht=parseInt(qte)*parseFloat(pu_ht.replace(',','.'));
						prix_ht=prix_ht-(prix_ht*parseFloat(this.value)/100);
						var prix_tva=parseFloat(prix_ht)*tx_tva;
						var prix_ttc=parseFloat(prix_ht)+parseFloat(prix_tva);
						$('input#PRIX_HT').val(prix_ht.toFixed(2));
						$('input#PRIX_TVA').val(prix_tva.toFixed(2));
						$('input#PRIX_TTC').val(prix_ttc.toFixed(2));
						//$("#list-commande").jqGrid('setRowData',rowid,{PRIX_HT:prix_ht.toFixed(2)});
						//$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TVA:prix_tva.toFixed(2)});
						//$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TTC:prix_ttc.toFixed(2)});
						//$("#list-commande").jqGrid('setRowData',rowid,{QTE:this.value});
						//set_total();
						//this.hidden=true;
					}
				}]
			}
		}
		,
		//{name:"PU_HT",index:"PU_HT",width:"95",editrules:{required:true,number:true},editable:true}
		{name:"PU_HT",index:"PU_HT",width:"95",editrules:{required:true,number:true},editable:true,edittype:'select',
			editoptions:{
				value:"18.04:18.04;18.33:18.33;22.00:22.00;28.45:28.45;0.05:0.05;0.02:0.02;41.50:41.50",
				dataEvents: [{        //this triggers on change of department combo-box
                    type: 'change',
                    fn: function(e){
						var rowid = $("#list-commande").jqGrid('getGridParam','selrow');
						data=$("#list-commande").jqGrid('getRowData',rowid);
						//jQuery("#list-commande").jqGrid('setRowData',rowid,{PU_HT:'16.50'});
						//$('input#PU_HT').val('18.33');
						var pu_ht =$('select#PU_HT').val();
						var tx_tva =$('select#TAUX_TVA').val();	
						var remise=$('input#REMISE').val();					
						//var qte=jQuery("#list-commande").jqGrid('getCell', rowid, 'QTE');
						var qte=$('input#QTE').val();
						var prix_ht=parseInt(qte)*parseFloat(pu_ht.replace(',','.'));
						prix_ht=prix_ht-(prix_ht*parseFloat(remise)/100);
						var prix_tva=parseFloat(prix_ht)*tx_tva;
						var prix_ttc=parseFloat(prix_ht)+parseFloat(prix_tva);
						/*$("#list-commande").jqGrid('setRowData',rowid,{PRIX_HT:prix_ht.toFixed(2)});
						$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TVA:prix_tva.toFixed(2)});
						$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TTC:prix_ttc.toFixed(2)});*/
						$('input#PRIX_HT').val(prix_ht.toFixed(2));
						$('input#PRIX_TVA').val(prix_tva.toFixed(2));
						$('input#PRIX_TTC').val(prix_ttc.toFixed(2));
						//set_total();
					}
				}]
			}
		}
		,
		{name:"PRIX_HT",index:"PRIX_HT",width:"95",editrules:{required:true,number:true},editable:true}
		,
		{name:"TAUX_TVA",index:"TAUX_TVA",width:"100",editrules:{required:true},editable:true,edittype:'select',
			editoptions:{
				value:"0.20:20;0.055:5.5",
				dataEvents: [{        //this triggers on change of department combo-box
                    type: 'change',
                    fn: function(e){
						var rowid = $("#list-commande").jqGrid('getGridParam','selrow');
						data=$("#list-commande").jqGrid('getRowData',rowid);
						//jQuery("#list-commande").jqGrid('setRowData',rowid,{PU_HT:'16.50'});
						//$('input#PU_HT').val('18.33');
						var pu_ht =$('select#PU_HT').val();
						var tx_tva =$('select#TAUX_TVA').val();	
						var remise=$('input#REMISE').val();					
						//var qte=jQuery("#list-commande").jqGrid('getCell', rowid, 'QTE');
						var qte=$('input#QTE').val();
						var prix_ht=parseInt(qte)*parseFloat(pu_ht.replace(',','.'));
						prix_ht=prix_ht-(prix_ht*parseFloat(remise)/100);
						var prix_tva=parseFloat(prix_ht)*tx_tva;
						var prix_ttc=parseFloat(prix_ht)+parseFloat(prix_tva);
						/*$("#list-commande").jqGrid('setRowData',rowid,{PRIX_HT:prix_ht.toFixed(2)});
						$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TVA:prix_tva.toFixed(2)});
						$("#list-commande").jqGrid('setRowData',rowid,{PRIX_TTC:prix_ttc.toFixed(2)});*/
						$('input#PRIX_HT').val(prix_ht.toFixed(2));
						$('input#PRIX_TVA').val(prix_tva.toFixed(2));
						$('input#PRIX_TTC').val(prix_ttc.toFixed(2));
						//set_total();
					}
				}]
			}
		}
		,
		{name:"PRIX_TVA",index:"PRIX_TVA",width:"95",editrules:{required:true,number:true},editable:true}
		,
		{name:"PRIX_TTC",index:"PRIX_TTC",width:"95",editrules:{required:true,number:true},editable:true}
		,
		{name:"GRATUIT",index:"GRATUIT",width:"95",editrules:{required:true},editable:true,edittype:'checkbox',formatter:"checkbox",formatoptions:"{ disabled: false}",align:"center",
			editoptions:{
				editoptions: { value: "True:False"},
				dataEvents: [{        //this triggers on change of department combo-box
                    type: 'change',
                    fn: function(e){
						var rowid = $("#list-commande").jqGrid('getGridParam','selrow');
						data=$("#list-commande").jqGrid('getRowData',rowid);
						if(this.checked==true){
							//$('input#PU_HT').val(0);
							var prix_ht=0;
							$('input#REMISE').val(100);
						}else{
							var pu_ht=$('input#PU_HT').val();
							var remise=$('input#REMISE').val();	
							var prix_ht=parseInt($('input#QTE').val())*parseFloat(pu_ht.replace(',','.'));
							prix_ht=prix_ht-(prix_ht*parseFloat(remise)/100);							
						}
						//var prix_ht=parseInt($('input#QTE').val())*parseFloat(pu_ht.replace(',','.'));
						var prix_tva=parseFloat(prix_ht)*0.2;
						var prix_ttc=parseFloat(prix_ht)+parseFloat(prix_tva);
						$('input#PRIX_HT').val(prix_ht.toFixed(2));
						$('input#PRIX_TVA').val(prix_tva.toFixed(2));
						$('input#PRIX_TTC').val(prix_ttc.toFixed(2));

					}
				}]
			}
		}
		],
		//caption: "Liste des commandes",
		sortable: true,
		autowidth: false,
		height: 100,
		width:880,
		shrinkToFit: false,
		pager: "#pagernav-commande",
		pgtext: "",
		pgbuttons: true,
		editurl: "commande.php",
		ignoreCase:true,
		sortname:'NOM',
    	sortorder: "asc",
		//closeAfterEdit: true,
		//forceFit : true,
		//cellEdit: true,
		//cellsubmit: 'clientArray',
		ondblClickRow: function(rowid) {
			jQuery("#list-commande").jqGrid('editGridRow', 
				rowid,{
					width:300,
					reloadAfterSubmit:false,
					topinfo:"",
					jqModal:true,
					align:"center",
					saveicon:[true,"left","ui-icon-disk"],
					checkOnSubmit:false,
					checkOnUpdate:false,
					closeOnEscape: true,
					savekey: [true],					
					afterComplete:function(sel, o) {
						set_total();
					}
				} 
			);
		},
		/*afterSaveCell : function(rowid,name,val,iRow,iCol) {
			//alert(name);
			if(name == 'PRODUIT') {
				var taxval = jQuery("#list-commande").jqGrid('getCell',rowid,iCol);
				alert (taxval);
				jQuery("#list-commande").jqGrid('setRowData',rowid,{PRIX_UNITAIRE:19.80});
			}
			if(name == 'QTE') {
				var pu_ht = jQuery("#list-commande").jqGrid('getCell',rowid,iCol+1);
				var prix_ht=parseFloat(val)*parseFloat(pu_ht.replace(',','.'));
				var prix_ttc=parseFloat(prix_ht)*0.2;
				jQuery("#list-commande").jqGrid('setRowData',rowid,{PRIX_HT:prix_ht});
				jQuery("#list-commande").jqGrid('setRowData',rowid,{PRIX_TTC:prix_ttc});
			}
		}*/
		
		});
		
		//Barre de navigation (navigator)
		jQuery("#list-commande").jqGrid('navGrid',"#pagernav-commande",{edit:true,add:true,del:true,edittitle:'Editer la ligne',deltitle:'Supprimer la ligne',refresh:false,search:false},
		//Ajout
		{
			width:300,
			reloadAfterSubmit:false,
			topinfo:"",
			jqModal:true,
			align:"center",
			saveicon:[true,"left","ui-icon-disk"],
			checkOnSubmit:false,
			checkOnUpdate:false,
			closeOnEscape: true,
			savekey: [true],
			closeAfterEdit:true,
			closeAfterAdd:true,
			onClose: function(sel, o) {
				set_total();
			}
		} ,
		
		//Edition
		{
			width:300,
			reloadAfterSubmit:false,
			topinfo:"",
			jqModal:true,
			align:"center",
			saveicon:[true,"left","ui-icon-disk"],
			checkOnSubmit:false,
			checkOnUpdate:false,
			closeOnEscape: true,
			savekey: [true] ,
			closeAfterEdit:true,
			closeAfterAdd:true,
			onClose:  function(sel, o) {
				set_total();
			}
		},
		
		//Del 
		{
			width:300,
			onClose:  function(sel, o) {
				set_total();
			},
			beforeShowForm:function(form) {
				$("td.delmsg",form).html("Souhaitez-vous supprimer cette ligne ?");
			}}
		);
		
		//jQuery("#list-commande").jqGrid('inlineNav',"#pagernav-commande");
		
		//resize
		//jQuery("#list-commande").jqGrid('gridResize',{minWidth:350,maxWidth:'100%',minHeight:80, maxHeight:'100%'});
/*----------------------------------------------------------------------------------------------------------------------*/
		function unformat_select(cellvalue, options, cellobject)
		{
			var unformatValue = '';
		
			$.each(options.colModel.editoptions.value, function (k, value)
			{
				if (cellvalue == value)
				{
					unformatValue = k;
				}
			});
		
			return unformatValue;
		}
		
/*----------------------------------------------------------------------------------------------------------------------*/
		function get_qte_values(){
			var qte_values='';
			for(i=1;i<=100;i++){
				qte_values+= i + ':' + i + ';';
			}
			qte_values.substr(1, qte_values.length-1)
			return qte_values;
		}
		
/*----------------------------------------------------------------------------------------------------------------------*/
		function set_total()
		{
			var total_ht=0;
			var total_tva=0;
			var total_ttc=0;
			var frais_ht=0;
			var frais_ttc=0;
			var frais_tva=0;
						
			var mya=new Array();
			
			mya=$("#list-commande").jqGrid('getDataIDs');  // Récupére tous les ID
												
			for(i=0;i<mya.length;i++)
			{
				data=$("#list-commande").jqGrid('getRowData',mya[i]); // récupére toutes les lignes
				total_ht=parseFloat(total_ht)+parseFloat(data['PRIX_HT'].replace(',','.').replace(' ','')); // extraction des colonnes de chaque lignes spérés par une tab
				total_ttc=parseFloat(total_ttc)+parseFloat(data['PRIX_TTC'].replace(',','.').replace(' ','')); // extraction des colonnes de chaque lignes spérés par une tab
				total_tva=parseFloat(total_tva)+parseFloat(data['PRIX_TVA'].replace(',','.').replace(' ',''));
			}
			frais_ttc=$("#frais").val().replace(',','.');
			frais_ht=frais_ttc/1.2;  //parseFloat($("#frais").val().replace(',','.'))/1.2;
			frais_tva=frais_ttc-frais_ht;
			total_ht=total_ht-(total_ht*parseFloat($("#remise").val())/100);
			total_ttc=total_ttc-(total_ttc*parseFloat($("#remise").val())/100);
			total_tva=total_tva-(total_tva*parseFloat($("#remise").val())/100);
			total_tva=total_tva+frais_tva;
			if($('input[name=facturation]:checked').val()=='tva'){
				total_ht=parseFloat(total_ht)+frais_ht;
			}else{
				total_ht=parseFloat(total_ht)+parseFloat(frais_ttc);
			}
			//total_tva=parseFloat(total_ht*0.2);
			total_ttc=parseFloat(total_ht + total_tva);//parseFloat(total_ttc)+parseFloat($("#frais").val().replace(',','.'));
			
			$("#ht").val(parseFloat(total_ht).toFixed(2));
			if($('input[name=facturation]:checked').val()=='tva'){
				$("#tva").val(parseFloat(total_tva).toFixed(2));
				$("#ttc").val(parseFloat(total_ttc).toFixed(2));
			}else{
				$("#tva").val(0);
				$("#ttc").val(0);
			}
			//alert(total_ttc);
		}

/*----------------------------------------------------------------------------------------------------------------------*/		
		function get_detail_commande(){
			var mya=new Array();
			
			mya=$("#list-commande").jqGrid('getDataIDs');  // Récupére tous les ID
												
			for(i=0;i<mya.length;i++)
			{
				data=$("#list-commande").jqGrid('getRowData',mya[i]); // récupére toutes les lignes
				$( "#commande" ).append('<input type="hidden" name="produit[]"  value="'+data['PRODUIT']+'">');
				$( "#commande" ).append('<input type="hidden" name="qte[]"  value='+parseInt(data['QTE'])+'>');
				$( "#commande" ).append('<input type="hidden" name="remise_produit[]"  value='+parseInt(data['REMISE'])+'>');								
				$( "#commande" ).append('<input type="hidden" name="pu_ht[]"  value="'+parseFloat(data['PU_HT'].replace(',','.').replace(' ',''))*100+'">');
				$( "#commande" ).append('<input type="hidden" name="prix_ht[]"  value="'+parseFloat(data['PRIX_HT'].replace(',','.').replace(' ',''))*100+'">');
				$( "#commande" ).append('<input type="hidden" name="prix_tva[]"  value="'+parseFloat(data['PRIX_TVA'].replace(',','.').replace(' ',''))*100+'">');								
				$( "#commande" ).append('<input type="hidden" name="prix_ttc[]"  value="'+parseFloat(data['PRIX_TTC'].replace(',','.').replace(' ',''))*100+'">');																		
				$( "#commande" ).append('<input type="hidden" name="gratuit[]"  value="'+(data['GRATUIT'].indexOf('value="on"')!=-1?1:0)+'">');	
				/*data['GRATUIT'] retourne la chaine <input type="checknox" value="on"...>*/
			}
		}
		
		//Création des tableaux du détail de la commande à la validation du formulaire. 
		$("#valide").click(get_detail_commande);

		//Recalcul des montants à la sélection dun nouveau frais
		$("#frais").change(set_total);
		
		//Recalcul des montants au changement de la remise
		$("#remise").change(set_total);
		
		//Recalcul des montants au changement de la remise
		$('input[name=facturation]').change(set_total);
		
		//$('input[name=facturation]').click(set_total);
		
		//Lien vers la fenêtre des clients
		$("#client_admin").click(function() {
				if ($("#clients").val()==0){
					window.open('clients.php','_blank');
				}else{
					window.open('clients.php?client_id='+$("#clients").val(),'_blank');					
				}
		});
		
		
		<?php
		//Génération du numéro de facture à créer
		function get_num_facture(){
			//03/01/2017 clôture comptable au 30/06 et non au 31/12
			$current_year=(date('n').date('d')>701)?substr(date('Y'),2):substr(date('Y')-1,2);
			$num_facture=0;
			$sql="SELECT max(CAST(SUBSTRING( num_facture,4 ) as UNSIGNED)) AS facture FROM `con_commandes` where num_facture like '".$current_year."/%'";
			$result = mysql_query($sql) or die(mysql_error());
			$commande_tmp = mysql_fetch_object($result);
			$num_facture=$commande_tmp->facture;
			$num_facture=$num_facture+1;
			//return 'CH'.$num_facture;
			//03/01/2017 clôture comptable au 30/06 et non au 31/12
			if(date('n').date('d')>701){
				return substr(date('Y'),2).'/'.$num_facture;
			}else{
				return substr(date('Y')-1,2).'/'.$num_facture;
			}
		}

		if (isset($_GET['commande_id']))
		{
			echo "var mydata = [";
			$sql = "SELECT  * ";
			$sql .= "FROM con_commande_details WHERE commande_id=".$_GET['commande_id'];
			$i=0;
			$res = mysql_query($sql,$db) or die(mysql_error());
			while ($line=mysql_fetch_array($res,MYSQL_ASSOC) ) {
				$i=$i+1;
				echo ",";
				echo "{";
				echo "ID:'".$i."'";
				echo ",";
				echo "COMMANDE_DETAIL_ID:'".$line['commande_detail_id']."'";
				echo ",";
				echo "PRODUIT:'".addslashes($line['libelle'])."'";
				echo ",";
				echo "QTE:'".$line['qte']."'";
				echo ",";
				echo "REMISE:'".$line['remise']."'";
				echo ",";
				echo "PU_HT:'".number_format($line['prix_unitaire']/100, 2, '.', ' ')."'";
				echo ",";
				echo "PRIX_HT:'".number_format($line['montant_ht']/100, 2, '.', ' ')."'";
				echo ",";
				echo "TAUX_TVA:'".$line['taux_tva']."'";
				echo ",";
				echo "PRIX_TVA:'".number_format($line['montant_tva']/100, 2, '.', ' ')."'";
				echo ",";
				echo "PRIX_TTC:'".number_format($line['montant_ttc']/100, 2, '.', ' ')."'";
				echo ",";
				$gratuit=$line['gratuit']=='oui'?'on':'';
				echo "GRATUIT:'".$gratuit."'";
				echo "}\n";
			 }
			echo "];";
			echo "for(var i=0;i<=mydata.length;i++) jQuery('#list-commande').jqGrid('addRowData',i+1,mydata[i]);";
			echo "$('#list-commande').trigger( 'reloadGrid' );";	
		
			$sql = "SELECT DISTINCT CONCAT(  `civilite` ,  ' ',  `nom` ,  ' ',  `prenom` ) AS client,  CONCAT(  `adresse` ,  ' ',  `cp` ,  ' ',  `ville` ) AS adresse,  `pays` ,mail, con_commandes . * ";
			$sql .= "FROM con_clients INNER JOIN con_commandes ON con_clients.client_id = con_commandes.client_id";
			$sql .= " WHERE commande_id=".$_GET['commande_id'];
			$i=0;
			$res = mysql_query($sql,$db) or die(mysql_error());
			while ($commande=mysql_fetch_object($res) ) {?>
				$('#num').val('<?php echo $commande->num_facture; ?>');
				<?php 
					$date_commande = new DateTime($commande->date_commande);
				?>
				$( "#date" ).datepicker( "setDate","<?php echo $date_commande->format('d/m/Y'); ?>" );
				$('#clients').val('<?php echo $commande->client_id; ?>');
				$('#reglee').attr("checked",<?php echo $commande->regle=='oui'?'true':'false'; ?>);
				$('#mode_paiement').val('<?php echo $commande->mode_paiement; ?>');
				<?php if ($commande->ventes_sans_tva_intracom==0&&$commande->ventes_sans_tva_horscee==0){ ?>
					$('#frais').val('<?php echo number_format($commande->frais_port_ttc/100,2, '.', ' '); ?>');
				<?php }
				else { ?>
					$('#frais').val('<?php echo number_format($commande->frais_port_ht/100,2, '.', ' '); ?>');
				<?php 
					}
				 ?>	
				$('#remise').val('<?php echo $commande->remise; ?>');
				/*$('#cee').attr("checked",<?php echo $commande->facturation_cee=='oui'?'true':'false'; ?>);
				$('#ventes_intra_com').val('<?php echo number_format($commande->ventes_sans_tva_intracom/100,2, '.', ' '); ?>');
				$('#ports_intra_com').val('<?php echo number_format($commande->ports_non_soumis_intracom/100,2, '.', ' '); ?>');
				$('#ventes_hors_cee').val('<?php echo number_format($commande->ventes_sans_tva_horscee/100,2, '.', ' '); ?>');
				$('#ports_hors_cee').val('<?php echo number_format($commande->ports_non_soumis_horscee/100,2, '.', ' '); ?>');
				*/
				<?php if ($commande->ventes_sans_tva_intracom!=0){ ?>
						$("[name=facturation]").val(["intra-com"]);
				<?php }
					elseif ($commande->ventes_sans_tva_horscee!=0){ ?>
						$("[name=facturation]").val(["hors-cee"]);
				<?php }
					else { ?>
						$("[name=facturation]").val(["tva"]);
					<?php 
					}
				 ?>
				/*$('#ht').val('<?php echo number_format($commande->total_ht/100,2, '.', ' '); ?>');
				$('#tva').val('<?php echo number_format($commande->total_tva/100,2, '.', ' '); ?>');
				$('#ttc').val('<?php echo number_format($commande->total_ttc/100,2, '.', ' '); ?>');*/
				$('#ht').val('<?php echo $commande->total_ht/100; ?>');
				$('#tva').val('<?php echo $commande->total_tva/100; ?>');
				$('#ttc').val('<?php echo $commande->total_ttc/100; ?>');
				$('#tableau_comptable').attr("checked",<?php echo $commande->tableau_comptable=='oui'?'true':'false'; ?>);
				//$('#observations').val('<?php echo addslashes(preg_replace("/\r\n|\r|\n/",'<br/>',$commande->observations)); ?>');	
				$('#observations').val('<?php echo preg_replace("/(.)?\\n/","\\r\\n",nl2br($commande->observations)); ?>');										
			<?php 
			}
		}else
		{ 
			$num_facture=get_num_facture();
		?>
			var date_jour=new Date();
			//alert(formatDate(date_jour));
			$('#num').val('<?php print $num_facture; ?>');
			$( "#date" ).datepicker( "setDate",date_jour );
			
	
		<?php 
		}
		?>
		
	});
	
	
	
	</script>

	
</head>
<body>
	<form action="commande_post.php" method="post" id="commande" >
	<table   width="60%" align="center" style" border-collapse:collapse;border-style:solid;border-width:0 px;"  cellspacing="5px" cellpadding="5px">
            <tr>
                <td style="border-width:0 px;" ><label for="client">Client</label></td>
                <td style="border-width:0 px;" colspan="2" >
                    <p>
                      <select name="clients" id="clients"   >
                        <option value="0" selected ></option>
                        <?php
                        $sql="select client_id,  CONCAT(`nom` ,  ' ',  `prenom` ) AS client from con_clients order by nom ";
                        $res = mysql_query($sql,$db) or die(mysql_error());
                        while ($line=mysql_fetch_array($res,MYSQL_ASSOC) ) {
                            echo'<option value="'.$line['client_id'].'" >'.stripslashes($line['client']).'</option>';
                        }
                      ?>
                      </select>
                      <input type="button" id="client_admin" name="client_admin" value="...">
                </p></td>
            </tr>
            <tr>
                <td style="border-width:0 px;"><label for="num">Num</label></td>
                <td style="border-width:0 px;" colspan="2"><input name="num" id="num"  required type="text"  size="10" maxlength="15" /></td>
            </tr>
            <tr>
              <td style="border-width:0 px;" ><label for="date"> Date</label></td>
              <td style="border-width:0 px;" colspan="2"><input  name="date" id="date" required type="text"  /></td>
            </tr>
             <tr>
               <td colspan="3"  style="border-width:0 px;">Réglée<input type="checkbox" id="reglee" name="reglee" align="texttop" > </td>
            </tr>
             <tr>
              <td style="border-width:0 px;" ><label for="frais"> Mode de réglement</label></td>
              <td style="border-width:0 px;" colspan="2">
                    <select id='mode_paiement' name='mode_paiement'>
                        <option value='Chèque'>Chèque</option>                                    
                        <option value='Espèce'>Espèce</option>
                        <option value='Carte de crédit'>Carte de crédit</option>		  
                        <option value='Virement'>Virement</option>		  		  		  
                    </select>
                  </td>
            </tr>
            <tr>
                <td colspan="3">
                    <table id="list-commande"></table>
                    <div id="pagernav-commande"></div>
                </td>
            </tr>
          <!--   <tr>
                  <td style="border-width:0 px;" ><label for="gratuits">Produits gratuits</label></td>
                  <td style="border-width:0 px;"><input name="gratuits" id="gratuits"  type="gratuits"  value="0" min="0" max="100" size="7" ></td>
             </tr>-->
            <tr>
              <td style="border-width:0 px;" ><label for="frais"> Frais de port</label></td>
              <td style="border-width:0 px;"><input name="frais" id="frais"   value="0"  size="7" >
                  </td>
            </tr>

            <tr>
                  <td style="border-width:0 px;" ><label for="remise">Remise (%)</label></td>
                  <td style="border-width:0 px;"><input name="remise" id="remise"  type="number"  value="0" min="0" max="100" size="7" ></td>
             </tr>
             <tr>
               <td style="border-width:0 px;">
                 <fieldset>
                     <legend>Facturation</legend> 
                     <input type="radio" name="facturation" value="tva" checked onSelect="" >TVA<br>
                     <input type="radio" name="facturation" value="intra-com" >Intra-com<br>
                     <input type="radio" name="facturation" value="hors-cee" >Hors CEE
                  </fieldset>
                 </td>
                 <td>
                 </td>
            </tr>
             <!--<tr>
                <td>
                    <fieldset>
                        <legend>Ventes Intra-com</legend>
                        <table>
                            <tr>
                                <td>
                                    Ventes                                                 
                                </td>
                                <td>
                                    <input name="ventes_intra_com" id="ventes_intra_com"   size="7"   />                                                 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Ports                                                 
                                </td>
                                <td>
                                    <input name="ports_intra_com" id="ports_intra_com"    size="7"    />                                                 
                                </td>
                            </tr>
                         </table>      
                    </fieldset>
                </td>
                 <td>
                    <fieldset style="width:180px" >
                        <legend>Ventes Hors CEE</legend>
                        <table>
                            <tr>
                                <td>
                                    Ventes                                                 
                                </td>
                                <td>
                                    <input name="ventes_hors_cee" id="ventes_hors_cee"    size="7"   />                                                 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Ports                                                 
                                </td>
                                <td>
                                    <input name="ports_hors_cee" id="ports_hors_cee"   size="7"   />                                                 
                                </td>
                            </tr>
                         </table>      
                    </fieldset>
                </td>
            </tr>-->
             <tr>
                  <td style="border-width:0 px;" ><label for="ht">Total HT</label></td>
                  <td style="border-width:0 px;"><input name="ht" id="ht"  required type="text"  size="7"    /></td>
             </tr>
             <tr>
                  <td style="border-width:0 px;" ><label for="tva">Total TVA</label></td>
                  <td style="border-width:0 px;"><input name="tva" id="tva"  required type="text"  size="7"   /></td>
             </tr>
             <tr>
                  <td style="border-width:0 px;" ><label for="ttc">Total TTC</label></td>
                  <td style="border-width:0 px;"><input name="ttc" id="ttc"  required type="text" size="7" /></td>
             </tr>
             <tr>
               <td colspan="3"  style="border-width:0 px;">Quantité autre
               <input type="checkbox" id="tableau_comptable" name="tableau_comptable" align="texttop" > </td>
             </tr>
              <tr>
               <td colspan="3"  style="border-width:0 px;">Coparticipation aux frais de publicité marketing <input type="checkbox" id="coparticipation" name="coparticipation" align="texttop" > </td>
             </tr>
             <tr>
                  <td style="vertical-align:top;border-width:0 px;" ><label for="observations">Observations</label></td>
                  <td style="border-width:0 px;" colspan="2"><textarea cols="50" rows="5" name="observations" id="observations" ></textarea></td>
             </tr>
          <tr>
            <td style="border-width:0 px;" colspan="3" align="center"  >
            <p align="center">
					<INPUT type="submit" name="valide" id="valide" value="Sauvegarder la commande">
           </p>
          </td>
    	</tr>
	</table>
    <INPUT type="hidden" name="action" id="action" value="<?php print $_GET['action']; ?>">
    <INPUT type="hidden" name="commande_id" id="commande_id" value="<?php print $_GET['commande_id']; ?>">
    </form>

</body>


</html>

