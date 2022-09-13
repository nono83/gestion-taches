
<?php
//header('Content-Type: text/html; charset=utf-8');	
include('../secure/db.inc.php'); 
	mysql_query("SET NAMES UTF8");
//include('clients-dialog.php'); 
//include('commande-dialog.php'); 

if (isset($_POST['oper'])){
	
	/*if($_POST['oper']=='add')
	 {
		$sql="INSERT INTO `reservation_cico` (cico_id,`reservation_id`, `intervenant_id`, `date_cico`, `heure_cico`, `type`, `precisions`) VALUES ";
		$sql.="(10,10000, ".$_POST['INTERVENANT'].", '2014-03-18', '".$_POST['NUM']."', 'CI', '".nl2br($_POST['PRECISIONS'])."')";
		
		$id = mysql_query($sql, $db) or die(mysql_error());
	 }
	 elseif($_POST['oper']=='edit')
	 {
		$sql="UPDATE con_commandes set num_facture='".$_POST['NUM']."',regle='".$_POST['REGLEE']."' where commande_id=".$_POST['COMMANDE_ID'];
		$id = mysql_query($sql, $db) or die(mysql_error());
	 }*/
	 if($_POST['oper']=='del')
	 {
		 $sql="DELETE FROM con_commande_details where commande_id=".$_POST['id'];
		$id = mysql_query($sql, $db) or die(mysql_error());
		 
		$sql="DELETE FROM con_commandes where commande_id=".$_POST['id'];
		$id = mysql_query($sql, $db) or die(mysql_error());
	 }
}

?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Liste des commandes</title>
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
	<script src="js/jquery.tablednd.js" type="text/javascript"></script>
	<script src="js/jquery.contextmenu.js" type="text/javascript"></script>
	<script src="js/ui.multiselect.js" type="text/javascript"></script>
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
	//Dialog form en modal pour la gestion des patients
	$("#dialog").dialog({ 
		width: 1000 ,
		position: { my: "left top", at: "center", of: ""  },
		autoOpen: false
	});
	
	//Dialog form en modal pour la gestion des patients
	$("#dialog-commande").dialog({ 
		autoOpen: false
	});
	
	function myelem (value, options) {
	  var el = document.createElement("input");
	  el.type="text";
	  el.value = value;
	  return el;
	}
	 
	function myvalue(elem, operation, value) {
		if(operation === 'get') {
		   return $(elem).val();
		} else if(operation === 'set') {
		   $('input',elem).val(value);
		}
	}
	
	function element(value,options){
    	return $('<input type="time" value="'+value+'" />');
	}
	function elementval(elem){
		return elem.val();
	}
	$(function() {
		var today=new Date();
		var end_date=today.getDate()+"-"+(today.getMonth()+1)+"-"+today.getFullYear();
		//Exportation de la liste sous excel
		function exportExcel()
		{
			var mya=new Array();
			mya=$("#list").jqGrid('getDataIDs');  // Récupére tous les ID
			var data=$("#list").jqGrid('getRowData',mya[0]);     // Get First row to get the labels
			var colNames=new Array(); 
			var ii=0;
			for (var i in data){colNames[ii++]=i;}    // Récupére le nom des colonnes
			var html="";
			for(i=1;i<colNames.length;i++)
			{
				if(colNames[i]!='MESSAGE'){html=html+colNames[i]+"\t";} // extraction des noms de colonnes séparés par une tab
			}
			html=html+"\n"; // retour à la ligne
			
			for(i=0;i<mya.length;i++)
			{
				data=$("#list").jqGrid('getRowData',mya[i]); // récupére toutes les lignes
				for(j=1;j<colNames.length;j++)
				{
					var cell=data[colNames[j]];
					if(colNames[j]!='MESSAGE'){html=html+cell.replace("\n","")+"\t";} // extraction des colonnes de chaque lignes spérés par une tab
				}
				html=html+"\n";  // retour à la ligne aprés chaque fin de ligne
	
			}
			html=html+"\n";  // retour à la ligne
			document.forms[0].csvBuffer.value=html;
			document.forms[0].method='POST';
			document.forms[0].action='csvExport.php';  
			document.forms[0].target='_blank';
			document.forms[0].submit();
		}
	
		var lastsel;
		jQuery("#list").jqGrid({
		datatype: "local",
		mtype: "POST",
		colNames:["COMMANDE_ID","CLIENT","ADRESSE","PAYS","MAIL","MESSAGE","NUM","DATE","REGLEE","REMISE","PORT HT","PORT TVA","CICOLEA 30ml","VISOLEA 50ml","PRODUIT HT","PRODUIT TVA","TOTAL HT","TOTAL TVA","TOTAL TTC","PAR","CEE","OBSERVATIONS","ventes sans tva intracom","ports non soumis intracom","ventes sans tva hors CEE","ports non soumis hors CEE"],
		colModel:[
		{name:"COMMANDE_ID",index:"COMMANDE_ID",width:"0",editable:true,key:true}
		,
		{name:"CLIENT",index:"CLIENT",width:"300",editable:false,editoptions:{size:20}}
		,
		{name:"ADRESSE",index:"ADRESSE",width:"250",editable:false,editoptions:{size:50}}
		,
		{name:"PAYS",index:"PAYS",width:"70",editable:false,editoptions:{size:10}}
		,
		{name:"MAIL",index:"MAIL",width:"200",editable:false,editoptions:{size:10}}
		,
		{name:"MESSAGE",index:"MESSAGE",width:"0",editable:false,edittype:'textarea',editoptions:{rows:"4",cols:"24"}}
		,
		{name:"NUM",index:"NUM",width:"50",editable:true,edittype:'text',editoptions:{size:10}}
		,
		{name:"DATE",index:"DATE",width:"80",sorttype:"date",formatter:'date', formatoptions:{newformat:"d-m-Y"},search:true,
		/*searchoptions:{
			/*dataInit: function(el) {
				$(el).daterangepicker({ dateFormat: 'dd-mm-yy' });
			},
			sopt: ['eq'],
			dataInit: function(elem)
			{
			   jQuery(elem).datepicker({
					dateFormat: 'dd-mm-yy'
				});
			},
			dataEvents: [
				{ type: 'change', fn: function(e) { 
					$("#list")[0].triggerToolbar();
				} }
            ],                                                   
			attr:{title:'Selectionnez une date'}}*/
		}
		,
		{name:"REGLEE",index:"REGLEE",width:"50",editable:true,edittype:'checkbox',editoptions:{value:"oui:non" }}
		,
		{name:"REMISE",index:"REMISE",width:"0",align:"right",editable:false}
		,
		{name:"PORT_HT",index:"PORT_HT",width:"55",align:"right",editable:false,editoptions:{size:10}}
		,
		{name:"PORT_TVA",index:"PORT_TVA",width:"70",align:"right",editable:false,editoptions:{size:10}}
		,
		{name:"CICOLEA_30ml",index:"CICOLEA_30ml",width:"85",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"VISOLEA_50ml",index:"VISOLEA_50ml",width:"85",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"PRODUIT_HT",index:"PRODUIT_HT",width:"80",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"PRODUIT_TVA",index:"PRODUIT_TVA",width:"80",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"TOTAL_HT",index:"TOTAL_HT",width:"80",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"TOTAL_TVA",index:"TOTAL_HT",width:"70",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"TOTAL_TTC",index:"TOTAL_HT",width:"80",align:"right",sorttype:"number",editable:false,editoptions:{size:10}}
		,
		{name:"MODE_PAIEMENT",index:"MODE_PAIEMENT",width:"60",align:"right",editable:false,editoptions:{size:10}}
		,
		{name:"CEE",index:"CEE",width:"40",editable:false,editoptions:{size:10}}
		,
		{name:"OBSERVATIONS",index:"OBSERVATIONS",width:"300",editable:false,editoptions:{size:10}}
		,
		{name:"VENTES_SANS_TVA_INTRACOM",index:"VENTES_SANS_TVA_INTRACOM",sorttype:"number",width:"150",editable:false,editoptions:{size:10}}
		,
		{name:"PORTS_NON_SOUMIS_INTRACOM",index:"PORTS_NON_SOUMIS_INTRACOM",sorttype:"number",width:"150",editable:false,editoptions:{size:10}}
		,
		{name:"VENTES_SANS_TVA_HORS_CEE",index:"VENTES_SANS_TVA_HORS_CEE",sorttype:"number",width:"150",editable:false,editoptions:{size:10}}
		,
		{name:"PORTS_NON_SOUMIS_HORS_CEE",index:"PORTS_NON_SOUMIS_HORS_CEE",sorttype:"number",width:"150",editable:false,editoptions:{size:10}}
		],
		//caption: "Liste des commandes",
		//sortable: true,
		autowidth: true,
		height: 300,
		shrinkToFit: false,
		rowNum:100,
   		rowList:[100,200,500,1000,1500],
		viewrecords: true,
		pager: "#pagernav",
		pgtext: "",
		pgbuttons: true,
		editurl: "commandes-cheques.php",
		ignoreCase:true,
		//sortname:'NUM',
    	//sortorder: "desc",
		closeAfterEdit: true,
		//Critéres de recherche sur les dates par défaut
		
		postData: {
			filters:'{"groupOp":"AND","rules":[{"field":"DATE","op":"gt","data":"01-01-2014"},{"field":"DATE","op":"lt","data":"'+end_date+'"}]}'
		},

		//prmNames:{id:"CICO_ID"},
		ondblClickRow: function(rowid) {
			//jQuery("#list").jqGrid('editGridRow', rowid,{width:300,reloadAfterSubmit:false,topinfo:"",jqModal:true,align:"center",saveicon:[true,"left","ui-icon-disk"],checkOnSubmit:false,checkOnUpdate:false,closeOnEscape: true,savekey: [true]} );
			data=$("#list").jqGrid('getRowData',rowid); // récupére toutes les lignes
			//location.href='facture.php?commande_id='+data['COMMANDE_ID'];
			window.open('commande.php?action=edit&commande_id='+data['COMMANDE_ID'],'_blank');
		},
		});
		
		//Barre de navigation (navigator)
		jQuery("#list").jqGrid('navGrid',"#pagernav",{search:true,edit:false,add:false,del:true,deltitle:'Supprimer l\'enregistrement',alerttext:'Aucun enregistrement sélectionné',refresh:false},
		{},
		{},
		//Del options
		{mtype:"POST",
		beforeShowForm:function(form) {
			//ret = $("#list-clients").getRowData($("#list-clients").jqGrid('getGridParam','selrow'));
			$("td.delmsg",form).html("Souhaitez-vous supprimer cette commande ?");
		}}
		,{multipleSearch:true}
		);
		
		//Edition commande
		jQuery("#list").jqGrid('navButtonAdd',"#pagernav",
		{caption:"", buttonicon:"ui-icon-pencil", 
		onClickButton:function () { 
			rowid = $("#list").jqGrid ('getGridParam', 'selrow'),
			data=$("#list").jqGrid('getRowData',rowid); // récupére toutes les lignes
			//location.href='facture.php?commande_id='+data['COMMANDE_ID'];
			window.open('commande.php?action=edit&commande_id='+data['COMMANDE_ID'],'_self');
		} 
		, position: "first", title:"Modifier la commande", cursor: "pointer"})
		
		//Ajout commande
		jQuery("#list").jqGrid('navButtonAdd',"#pagernav",
		{caption:"", buttonicon:"ui-icon-plus", 
		onClickButton:function () { 
			rowid = $("#list").jqGrid ('getGridParam', 'selrow'),
			data=$("#list").jqGrid('getRowData',rowid); // récupére toutes les lignes
			//location.href='facture.php?commande_id='+data['COMMANDE_ID'];
			window.open('commande.php?action=add','_self');
		} 
		, position: "first", title:"Ajouter une commande", cursor: "pointer"})
		
		//Accés clients
		jQuery("#list").jqGrid('navButtonAdd',"#pagernav",
		{caption:"", buttonicon:"ui-icon-person", 
		onClickButton:function () { 
			//$("#dialog").dialog("open");
			window.open('clients.php','nom');
		} 
		, position: "last", title:"Liste des clients", cursor: "pointer"})
		
		//Impression facture
		jQuery("#list").jqGrid('navButtonAdd',"#pagernav",
		{caption:"", buttonicon:"ui-icon-print", 
		onClickButton:function () { 
			rowid = $("#list").jqGrid ('getGridParam', 'selrow'),
			data=$("#list").jqGrid('getRowData',rowid); // récupére toutes les lignes
			
			//location.href='facture.php?commande_id='+data['COMMANDE_ID'];
			if(data['CEE']=='oui'||data['VENTES_SANS_TVA_INTRACOM']!='0,00'||data['PORTS_NON_SOUMIS_INTRACOM']!='0,00'){
				window.open('facture-cee.php?commande_id='+data['COMMANDE_ID'],'_blank');
			}else if(data['VENTES_SANS_TVA_HORS_CEE']!='0,00'||data['PORTS_NON_SOUMIS_HORS_CEE']!='0,00'){
				window.open('facture-hors-cee.php?commande_id='+data['COMMANDE_ID'],'_blank');
			} else {
				window.open('facture.php?commande_id='+data['COMMANDE_ID'],'_blank');
			}
		} 
		, position: "last", title:"Imprimer la facture", cursor: "pointer"})
		
		//Impression liste
		jQuery("#list").jqGrid('navButtonAdd',"#pagernav",
		{caption:"", buttonicon:"ui-icon-clipboard", 
		onClickButton:function () { 
			exportExcel();
		} 
		, position: "last", title:"Impression de la liste", cursor: "pointer"})
		
		
		//jQuery("#list").jqGrid('inlineNav',"#pagernav");
		jQuery("#list").jqGrid('filterToolbar',{ defaultSearch : "cn"});
		
		//resize
		jQuery("#list").jqGrid('gridResize',{minWidth:350,maxWidth:'100%',minHeight:80, maxHeight:'100%'});
		
		
		
	});
	
	
	
	</script>

	<?php
	echo "<script>";
		echo "$(function() {";
		echo "var mydata = [";
		/*$sql = "SELECT DISTINCT CONCAT(  `civilite` ,  ' ',  `nom` ,  ' ',  `prenom` ) AS client, CONCAT(  `adresse` ,  ' ',  `cp` ,  ' ',  `ville` ) AS adresse,  `pays` ,mail,  CAST(SUBSTRING( num_facture,3 ) as UNSIGNED) AS facture,sum(qte) as qte,gratuit,libelle, (select sum(qte) FROM con_commande_details INNER JOIN con_commandes ON con_commandes.commande_id = con_commande_details.commande_id WHERE libelle='CICOLEA 30 ml' group by libelle) as qte_cicolea_30ml ,  con_commandes . * ";
		$sql .= "FROM con_clients INNER JOIN con_commandes ON con_clients.client_id = con_commandes.client_id INNER JOIN con_commande_details ON con_commandes.commande_id = con_commande_details.commande_id ";
		$sql .= "group by num_facture,civilite,nom,prenom,adresse,cp,pays,ville,mail,gratuit,libelle,`commande_id`,`date_commande` ,`frais_port_ht`,`frais_port_tva`,`frais_port_tva`,`frais_port_ttc` ,`num_facture`,`regle`,`total_ht`,`total_tva`,`total_ttc` , `facture`,`message`,`remise`,`mode_paiement`,`facturation_cee`,`observations`,`ventes_sans_tva_intracom`,`ports_non_soumis_intracom` ,`ventes_sans_tva_horscee`,`ports_non_soumis_horscee`,`tableau_comptable`,`coparticipation`,`tmp` ";
		$sql .= "having gratuit<>'oui' and libelle<>'Cicolea sample 2,5 ml'  order by date_commande desc,CAST(SUBSTR(`num_facture`, 4, 3) AS SIGNED INTEGER )  desc";
		*/
		$sql = "SELECT DISTINCT CONCAT( `civilite` , ' ', `nom` , ' ', `prenom` ) AS client, CONCAT( `adresse` , ' ', `cp` , ' ', `ville` ) AS adresse, `pays` ,mail, CAST(SUBSTRING( num_facture,3 ) as UNSIGNED) AS facture, (select IFNULL(sum(qte),0) FROM con_commande_details WHERE con_commandes.commande_id = con_commande_details.commande_id and libelle='CICOLEA 30 ml' and gratuit<>'oui' ) as qte_cicolea_30ml,";
		$sql .= "(select IFNULL(sum(qte),0) FROM con_commande_details WHERE con_commandes.commande_id = con_commande_details.commande_id and gratuit<>'oui' and libelle='VISOLEA 50 ml' ) as qte_visolea_50ml , con_commandes . * ";
		$sql .= "FROM con_clients INNER JOIN con_commandes ON con_clients.client_id = con_commandes.client_id order by date_commande desc,CAST(SUBSTR(`num_facture`, 4, 3) AS SIGNED INTEGER ) desc";
		$i=0;
		$res = mysql_query($sql,$db) or die(mysql_error());
		while ($line=mysql_fetch_array($res,MYSQL_ASSOC) ) {
			$i=$i+1;
			echo ",";
			echo "{";
			echo "COMMANDE_ID:'".$line['commande_id']."'";
			echo ",";
			echo "CLIENT:'".preg_replace("/(.)?\\n/","\\r\\n",$line['client'])."'";
			echo ",";
			echo "ADRESSE:'".preg_replace("/(.)?\\n/","\\r\\n",addslashes($line['adresse']))."'";
			echo ",";
			echo "PAYS:'".$line['pays']."'";
			echo ",";
			echo "MAIL:'".$line['mail']."'";
			echo ",";
			echo "MESSAGE:'".addslashes(preg_replace("/(.)?\\n/","\\r\\n",$line['message']))."'";
			echo ",";
			echo "NUM:'".$line['num_facture']."'";
			echo ",";
			echo "DATE:'".$line['date_commande']."'";
			echo ",";
			echo "REGLEE:'".$line['regle']."'";
			echo ",";
			echo "REMISE:'".$line['remise']."%'";
			echo ",";
			echo "PORT_HT:'".number_format($line['frais_port_ht']/100, 2, ',', ' ')."'";
			echo ",";
			echo "PORT_TVA:'".number_format($line['frais_port_tva']/100, 2, ',', ' ')."'";
			echo ",";
			/*echo "FRAIS_TTC:'".number_format($line['frais_port_ttc']/100, 2, ',', ' ')."'";
			echo ",";*/
			$QTE=($line['tableau_comptable']=='non')?$line['qte']:"0";
			$QTE_AUTRE=($line['tableau_comptable']=='oui')?$line['qte']:"0";			
			echo "CICOLEA_30ml:'".$line['qte_cicolea_30ml']."'";
			echo ",";
			echo "VISOLEA_50ml:'".$line['qte_visolea_50ml']."'";
			echo ",";
			echo "PRODUIT_HT:'".number_format(($line['total_ht']-$line['frais_port_ht'])/100, 2, ',', ' ')."'";
			echo ",";
			echo "PRODUIT_TVA:'".number_format(($line['total_tva']-$line['frais_port_tva'])/100, 2, ',', ' ')."'";
			echo ",";
			echo "TOTAL_HT:'".number_format($line['total_ht']/100, 2, ',', ' ')."'";
			echo ",";
			echo "TOTAL_TVA:'".number_format($line['total_tva']/100, 2, ',', ' ')."'";
			echo ",";
			echo "TOTAL_TTC:'".number_format($line['total_ttc']/100, 2, ',', ' ')."'";
			echo ",";
			echo "MODE_PAIEMENT:'".$line['mode_paiement']."'";
			echo ",";
			echo "CEE:'".$line['facturation_cee']."'";
			echo ",";
			echo "OBSERVATIONS:'".addslashes(preg_replace("/\r\n|\r|\n/",'<br/>',$line['observations']))."'";
			//echo "OBSERVATIONS:'".stripslashes($line['observations'])."'";
			echo ",";
			echo "VENTES_SANS_TVA_INTRACOM:'".number_format($line['ventes_sans_tva_intracom']/100, 2, ',', ' ')."'";
			echo ",";
			echo "PORTS_NON_SOUMIS_INTRACOM:'".number_format($line['ports_non_soumis_intracom']/100, 2, ',', ' ')."'";
			echo ",";
			echo "VENTES_SANS_TVA_HORS_CEE:'".number_format($line['ventes_sans_tva_horscee']/100, 2, ',', ' ')."'";
			echo ",";
			echo "PORTS_NON_SOUMIS_HORS_CEE:'".number_format($line['ports_non_soumis_horscee']/100, 2, ',', ' ')."'";
			//echo "OBSERVATIONS:'".addslashes(nl2br(preg_replace("/(.)?\\n/","\\r\\n",$line['observations'])))."'";
			echo "}\n";
		 }
		echo "];";
		echo "for(var i=0;i<=mydata.length;i++) jQuery('#list').jqGrid('addRowData',i+1,mydata[i]);";
		echo "$('#list').trigger( 'reloadGrid' );";
		echo "});";
	echo "</script>";
	
	?>
</head>
<body>
<form method="post" action="csvExport.php">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>
<div id=#page>
	<div id="menu">
		<table id="list"></table>
		<div id="pagernav"></div>
		
	</div>

</div>

</body>
</html>

