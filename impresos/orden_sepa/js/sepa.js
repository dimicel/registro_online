var i = 0;
var id_nie = "";
var anno_curso;
var titular_iban="", iban="", bic="";




$(document).ready(function() {
    document.getElementById("cargando").style.display = '';

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, () => {}, "json"));
    dat2 = dat1.then((res1) => {
        id_nie = res1["id_nie"];
        document.getElementById("id_nie").value=res1["id_nie"];
        anno_ini_curso = res1["anno_ini_curso"];
        mes_mat = res1["mes"];
        dia_mat = res1["dia"];
        //document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
        if (mes_mat == 6) anno_ini_premat = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        else if (mes_mat >= 7 && mes_mat <= 9) anno_ini_premat = (anno_ini_curso - 1) + "-" + (anno_ini_curso);
        
        if (mes_mat != 6) {
            anno_curso = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        } else {
            anno_curso = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
        }
        if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
            alerta("Error datos. Por favor, inténtelo más tarde.","ERROR");
            window.history.back();
        }

        return $.post("php/datos_residente.php", {id_nie:id_nie, curso:anno_curso }, () => {}, "json");
    });
    dat3 = dat2.then((resp) => {
        if (resp.error=="ok"){
            for (e in resp.datos){
                if(typeof(resp.datos[e])==="undefined" || resp.datos[e]===null ) resp.datos[e]="";
            }
            
            if(titular_iban=="") titular_iban=resp.datos.titular_cuenta;
            if(iban=="")iban=resp.datos.iban;
            if(bic=="")bic=resp.datos.bic;
            document.getElementById("titular_cuenta").value=titular_iban;
            document.getElementById("iban").value=iban;
            document.getElementById("bic").value=bic;
            document.getElementById("registro").value=resp.datos.registro;
            document.getElementById("direccion").value=resp.datos.direccion;
            document.getElementById("cp").value=resp.datos.cp;
            document.getElementById("localidad").value=resp.datos.localidad;
            document.getElementById("provincia").value=resp.datos.provincia;
        }
        else if(resp.error=="no_inscrito"){
            alerta("El usuario no está inscrito en la residencia (internado).","NO RESIDENTE",true);
        }
        else if(resp.error=="bonificado"){
            alerta("El residente es BONIFICADO, y por lo tanto no necesita crear una orden SEPA.","RESIDENTE BONIFICADO",true);
        }
        document.getElementById("cargando").style.display = 'none';
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

});

function registraSolicitud() {
    if (!$("#sepa").valid()) return;
    document.getElementById('firma_sepa').value = encodeURIComponent(canvas_upload);
    document.getElementById("cargando").style.display = '';
    $.ajax({
            url: 'php/generapdf.php',
            method: 'POST',
            data: $("#sepa").serialize(),
            success: function(response) {
                document.getElementById("cargando").style.display = 'none';
                if (response === 'ok') {
                    alerta("Orden SEPA generada correctamente.","OK",true);
                }
                else if(response=="server") {
                    alerta("Hay problemas en el servidor. Inténtelo en otro momento.","ERROR EN SERVIDOR",true);
                }
                else if(response=="db"){
                    alerta("Hay problemas en la base de datos. Inténtelo en otro momento.","ERROR DB",true);
                }
                else if(response.includes("registro_erroneo")){
                    alerta("No se ha podido hacer el registro por un problema en la base de datos.","ERROR REGISTRO",true);
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                document.getElementById("cargando").style.display = 'none';
                alerta("Ha ocurrido algún problema y no se ha podido hacer el registro. Error "+textStatus+"/"+errorThrown,"ERROR REGISTRO",true);
                console.error('Error:', textStatus, errorThrown);
            }
        });
    
}





