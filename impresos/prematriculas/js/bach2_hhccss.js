// JavaScript Document

function b2h_seleccionIdioma(_idioma){
	var lista=document.getElementById("b2h_esp_itin1");
	
	b2h_muestraEspecItin();

	for (i=0; i<lista.length;i++){
		if (_idioma=="ingles" && lista.options[i].innerHTML=="2ª Lengua Extranjera II (Inglés)") lista.options[i].innerHTML="2ª Lengua Extranjera II (Francés)";
		else if (_idioma=="frances" && lista.options[i].innerHTML=="2ª Lengua Extranjera II (Francés)") lista.options[i].innerHTML="2ª Lengua Extranjera II (Inglés)"; 
	}
	lista.selectedIndex=-1;
}



function b2h_seleccTronGen(op){
	var lista=document.getElementById("b2h_esp_itin1");

	b2h_muestraEspecItin();
	
	if (op=="itinerario 1"){
		$("#div_b2h_op_vacio").addClass("d-none");
		$("#div_b2h_op1").removeClass("d-none");
		$("#div_b2h_op2").addClass("d-none");
	}
	else if (op=="itinerario 2"){
		$("#div_b2h_op_vacio").addClass("d-none");
		$("#div_b2h_op1").addClass("d-none");
		$("#div_b2h_op2").removeClass("d-none");
	}
	
	if (op=="itinerario 1"){
		 op="Matemáticas Aplicadas a CC.SS.";
		 b2h_seleccTronOpItin1(retornaValRadioButton(document.getElementsByName("b2h_op1")));
	}
	else if (op=="itinerario 2"){
		 op="Latín II";
		 b2h_seleccTronOpItin2(retornaValRadioButton(document.getElementsByName("b2h_op2")));
	}
	
	for (i=0; i<lista.length;i++){
		if (lista.options[i].value=="1") lista.options[i].innerHTML=op;
	}
	
	lista.selectedIndex=-1;		
}


function b2h_seleccTronOp(op){
	var lista=document.getElementById("b2h_esp_itin1");
	
	for (i=0; i<lista.length;i++){
		if (lista.options[i].value=="2") lista.options[i].innerHTML=op;
	}
	
	lista.selectedIndex=-1;		
	
	b2h_muestraEspecItin();
}

function b2h_seleccTronOpItin1(op){
	var lista=document.getElementById("b2h_esp_itin1");
		
	b2h_muestraEspecItin();
	
	if (op=="Griego II"){
		for (i=0; i<lista.length;i++){
			if (lista.options[i].value=="3") lista.options[i].innerHTML="Geografía";
			if (lista.options[i].value=="4") lista.options[i].innerHTML="Historia del Arte";
		}
	}
	else if (op=="Geografía"){
		for (i=0; i<lista.length;i++){
			if (lista.options[i].value=="3") lista.options[i].innerHTML="Griego II";
			if (lista.options[i].value=="4") lista.options[i].innerHTML="Historia del Arte";
		}
	}
	else if (op=="Historia del Arte"){
		for (i=0; i<lista.length;i++){
			if (lista.options[i].value=="3") lista.options[i].innerHTML="Griego II";
			if (lista.options[i].value=="4") lista.options[i].innerHTML="Geografía";
		}
	}
}

function b2h_seleccTronOpItin2(op){
	var lista=document.getElementById("b2h_esp_itin1");

	b2h_muestraEspecItin();
	
	if (op=="Geografía"){
		for (i=0; i<lista.length;i++){
			if (lista.options[i].value=="3") lista.options[i].innerHTML="Griego II";
			if (lista.options[i].value=="4") lista.options[i].innerHTML="Historia del Arte";
		}
	}
	else if (op=="Historia del Arte"){
		for (i=0; i<lista.length;i++){
			if (lista.options[i].value=="3") lista.options[i].innerHTML="Griego II";
			if (lista.options[i].value=="4") lista.options[i].innerHTML="Geografía";
		}
	}	
}

function b2h_muestraEspecItin(){
	var itin=retornaValRadioButton(document.getElementsByName("b2h_itin"));
	var idioma=document.getElementById("b2h_ingles").checked || document.getElementById("b2h_frances").checked;
	var tronop1=document.getElementById("b2h_to11").checked || document.getElementById("b2h_to12").checked;
	
	if (itin=="itinerario 1") var itinto=document.getElementById("b2h_op11").checked || document.getElementById("b2h_op12").checked || document.getElementById("b2h_op13").checked;
	else if (itin=="itinerario 2") var itinto=document.getElementById("b2h_op21").checked || document.getElementById("b2h_op22").checked;
	else var itinto=false;
	
	if (idioma && tronop1 && itinto){
		$("#div_b2h_esp_itin_vacio").addClass("d-none");
		$("#div_b2h_esp_itin1").removeClass("d-none");
		$("#rot_epec_itin").css("margin-top","30px");
	}
	else{
		$("#div_b2h_esp_itin_vacio").removeClass("d-none");
		$("#div_b2h_esp_itin1").addClass("d-none");
		$("#rot_epec_itin").css("margin-top","0px");
	}
}

function b2h_generaDatosSerialize(){
	document.getElementById("b2h_eitin11").value=document.getElementById("b2h_esp_itin1").options[0].innerHTML;
	document.getElementById("b2h_eitin12").value=document.getElementById("b2h_esp_itin1").options[1].innerHTML;
	document.getElementById("b2h_eitin13").value=document.getElementById("b2h_esp_itin1").options[2].innerHTML;
	document.getElementById("b2h_eitin14").value=document.getElementById("b2h_esp_itin1").options[3].innerHTML;
	document.getElementById("b2h_eitin15").value=document.getElementById("b2h_esp_itin1").options[4].innerHTML;
	document.getElementById("b2h_eitin16").value=document.getElementById("b2h_esp_itin1").options[5].innerHTML;
	document.getElementById("b2h_eitin17").value=document.getElementById("b2h_esp_itin1").options[6].innerHTML;
	document.getElementById("b2h_eitin18").value=document.getElementById("b2h_esp_itin1").options[7].innerHTML;
	document.getElementById("b2h_eitin19").value=document.getElementById("b2h_esp_itin1").options[8].innerHTML;
	document.getElementById("b2h_eitin20").value=document.getElementById("b2h_esp_itin1").options[9].innerHTML;
	document.getElementById("b2h_eitin21").value=document.getElementById("b2h_esp_itin1").options[10].innerHTML;
	document.getElementById("b2h_eitin22").value=document.getElementById("b2h_esp_itin1").options[11].innerHTML;
	document.getElementById("b2h_eitin23").value=document.getElementById("b2h_esp_itin1").options[12].innerHTML;
	document.getElementById("b2h_eitin24").value=document.getElementById("b2h_esp_itin1").options[13].innerHTML;
	document.getElementById("b2h_eitin25").value=document.getElementById("b2h_esp_itin1").options[14].innerHTML;
	document.getElementById("b2h_eitin26").value=document.getElementById("b2h_esp_itin1").options[15].innerHTML;
	document.getElementById("b2h_eitin27").value=document.getElementById("b2h_esp_itin1").options[16].innerHTML;

}