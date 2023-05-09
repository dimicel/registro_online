var curso;
var id_nie = "";
var nombre = "";
var apellidos = "";
var email = "";
var anno_ini_curso;
var telef_alumno,email_alumno;



$(document).ready(function() {

    document.body.style.overflowY = "scroll";

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            document.getElementById("id_nie").value = resp["id_nie"];
            nombre = resp["nombre"];
            apellidos = resp["apellidos"];
            email = resp["email"];
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            document.getElementById("anno_curso").value = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
            document.getElementById("email").value = email;

            if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
                document.write("Error datos. Por favor, inténtelo más tarde.");
            }

        }
    }, "json"));
    dat2=dat1.then(()=>{
        $.post("../../php/usu_recdatospers.php",{id_nie:id_nie},(resp)=>{
            if (resp.error=="ok"){
                for (e in resp.datos){
                    if(typeof(resp.datos[e])=="undefined" || resp.datos[e]==null) resp.datos[e]="";
                }
                telef_alumno=resp.datos.telef_alumno;
                email_alumno=resp.datos.email;
            }
            else{
                telef_alumno='';
                email_alumno='';
            }
        },"json");
    });
    dat3 = dat2.then(() => {
        curso = anno_ini_curso + "-" + (anno_ini_curso + 1);

    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

});



function seleccion(obj){
    if (obj.id=="instrucciones"){
        open("instrucciones/instrucciones.pdf","_blank");
    }
    else if (obj.id=="consejeria"){
        $("#seccion-intro").hide();
        $("#seccion-consejeria").show();
        $("#seccion-centro_ministerio").hide();
    }
    else if(obj.id=="centro_ministerio"){
        $("#seccion-intro").hide();
        $("#seccion-consejeria").hide();
        $("#seccion-centro_ministerio").show();
    }
}

function vuelve(){
    $("#seccion-intro").show();
    $("#seccion-consejeria").hide();
    $("#seccion-centro_ministerio").hide();
}

function selGrado(obj){
    sel=document.getElementById("ciclos");
    if (obj.value==""){
        sel.innerHTML="";
        option = document.createElement('option');
        option.value = "";
        option.text = "Selecciona grado ...";
        sel.appendChild(option);
        return;
    }
    $.post("php/listaciclos.php",{grado:obj.value},(resp)=>{
        if (resp["error"]=="servidor"){
            alerta("Hay un problema con el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
        }
        else if(resp["error"]=="error_consulta"){
            alerta("Hay un problema con la base de datos. Inténtelo más tarde.","ERROR DB");
        }
        else if(resp["error"]=="no_ciclos"){
            alerta("No se encuentran ciclos formativos registrados.","SELECT SIN CICLOS");
        }
        else if(resp["error"]=="ok"){
            sel.innerHTML="";
            option = document.createElement('option');
            option.value = "";
            if (obj.value=="") option.text = "Selecciona grado ...";
            else option.text = "Selecciona ciclo ...";
            sel.appendChild(option);
            for (i=0;i<resp["datos"].length;i++){
                const option = document.createElement('option');
                option.value = resp["datos"][i];
                option.text = resp["datos"][i];
                sel.appendChild(option);
            }
            sel.selectedIndex=0;
        }
    },"json");
}


function selModulos(){
    alert(0);
    if (document.getElementById("ciclos").value=""){
        alert(1);
        alerta("Seleccione antes un ciclo formativo.","CICLO SIN SELECCIÓN");
        //return;
    }
}


