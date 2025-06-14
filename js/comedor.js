

$(function() {
    mostrarPantallaEspera();
    $("#fecha_lista_comedor").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy",
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        firstDay: 1,
        monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNameShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        showButtonPanel: true,
        currentText: "Hoy",
        closeText: "Cerrar",
        minDate: new Date(2000, 0, 1),
        maxDate: "0y",
        nextText: "Siguiente",
        prevText: "Previo"
    });
    var today = new Date();
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
    var year = today.getFullYear();

    var todayFormatted = day + '/' + month + '/' + year;
    document.getElementById('fecha_lista_comedor').value = todayFormatted;
    ocultarPantallaEspera();
    res_listadoRevisionAsistencia();
});

function res_listadoRevisionAsistencia(){
    //Verifica que la fecha es valida y si no no se hace la consulta
    // formato esperado: dd/mm/yyyy
    var fechaStr=document.getElementById("fecha_lista_comedor").value;
    var curso= document.getElementById("res_curso").value;
    const partes = fechaStr.split('/');
    if (partes.length !== 3) return false;
    const dia = parseInt(partes[0], 10);
    const mes = parseInt(partes[1], 10) - 1; // Mes en JS: 0-11
    const anio = parseInt(partes[2], 10);

    const fecha = new Date(anio, mes, dia);
    var validez=fecha.getFullYear() === anio && fecha.getMonth() === mes && fecha.getDate() === dia
    if (!validez) return;
    mostrarPantallaEspera();
    $.post("php/residencia_comedor_listado.php",{curso:curso,fecha:fechaStr},(resp)=>{
        ocultarPantallaEspera();
        if (resp.error=="ok"){
            _lt="";
            for (let i=0;i<resp.registros.length;i++){
                if (resp.registros[i].avisado==1)_lt+="<tr style='background-color:yellow;color:brown;'>"; 
                else "<tr>";
                _lt+="<td width='20%'>"+resp.registros[i].id_nie+"</td>";
                _lt+="<td width='65%'>"+resp.registros[i].nombre+"</td>";
                if (resp.registros[i].desayuno==0)_lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"\";else this.innerHTML=\"X\";'></td>";
                else _lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"\";else this.innerHTML=\"X\";'>X</td>";
                if (resp.registros[i].comida==0)_lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"\";else this.innerHTML=\"X\";'></td>";
                else _lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"\";else this.innerHTML=\"X\";'>X</td>";
                if (resp.registros[i].cena==0)_lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"\";else this.innerHTML=\"X\";'></td>";
                else _lt+="<td width='5%' style='text-align:center' onclick='javascript:if(this.innerHTML==\"X\")this.innerHTML=\"\";else this.innerHTML=\"X\";'>X</td>";
                _lt+="</tr>";
            }
            document.getElementById("asistencia_comedor").innerHTML=_lt;
        }
        else if (resp.error == "server"){
            alerta("Hay un problema en el servidor.", "ERROR DE SERVIDOR");
        } 
        else if (resp.error == "sin_registros"){
            alerta("La lista de residentes está vacía.", "SIN REGISTROS");
        }
        else{
            alerta("Error en la base de datos.", "ERROR DB");
        }

    },"json");
}

function res_actualizaListadoAsistenciaComedor() {
    mostrarPantallaEspera();
    let fecha = document.getElementById("fecha_lista_comedor").value;
    let asistencias = [];
    let filas = document.querySelectorAll("#asistencia_comedor tr");
    filas.forEach((fila) => {
        let celdas = fila.querySelectorAll("td");
        if (celdas.length > 0) {
            let id_nie = celdas[0].innerText.trim();
            let desayuno = celdas[2].innerText.trim() === "X" ? 1 : 0;
            let comida = celdas[3].innerText.trim() === "X" ? 1 : 0;
            let cena = celdas[4].innerText.trim() === "X" ? 1 : 0;
            asistencias.push({ id_nie, desayuno, comida, cena });
        }
    });

    $.post("php/residencia_comedor_actualiza.php", {fecha: fecha, asistencias: JSON.stringify(asistencias) }, function(resp) {
        ocultarPantallaEspera();
        if (resp == "ok") {
            alerta("Listado de asistencia actualizado correctamente.", "ACTUALIZACIÓN CORRECTA");
        } else if (resp == "server") {
            alerta("Hay un problema en el servidor. Haga el control a mano, y páselo al sistema más tarde.", "ERROR DE SERVIDOR");
        } else {
            alerta("Error al actualizar el listado de asistencia. Haga el control a mano, y páselo al sistema más tarde.", "ERROR");
        }
    });
}

function cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}
