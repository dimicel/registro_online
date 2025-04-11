campos = new Array();
encabezamiento = new Array();
var anno_ini_curso = 0;
var mes;
var orden_campo;
var orden_direccion;
var curso_actual;
var num_registros;
var num_reg_pagina = 25;
var numero_paginas;
var pagina = 1;
var orden_direccion_usu = "🡅";
var departamento="";



$(function() {
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";
   
    document.getElementById("cargando").style.display = 'inherit';
    prom1=Promise.resolve($.post("php/sesion.php", { tipo_usu: "jefe departamento" },()=>{},"json"));
    prom2=prom1.then((resp)=> {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            document.getElementById("rotulo_tipo_usu").innerHTML="GESTIÓN DEL REGISTRO ONLINE - DEPARTAMENTO: "+departamento.toUpperCase(); 
            anno_ini_curso = resp["anno_ini_curso"];
            departamento= resp["departamento"];
            mes = resp["mes"];
            _curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            curso_actual=_curso;
            generaSelectCurso();
            document.getElementById("curso").value = _curso;

            $('#navegacion_usus_top,#navegacion_usus_bottom').bootpag({
                total: 1,
                page: pagina,
                maxVisible: 10,
                leaps: true,
                firstLastUse: true,
                first: '←',
                last: '→',
                wrapClass: 'pagination',
                activeClass: 'active',
                disabledClass: 'disabled',
                nextClass: 'next',
                prevClass: 'prev',
                lastClass: 'last',
                firstClass: 'first'
            }).on("page", function(event, num) {
                pagina = num;
                listaUsus();
            });
            $('#navegacion_usus_top li').addClass('page-item');
            $('#navegacion_usus_top a').addClass('page-link');
            $('#navegacion_usus_bottom li').addClass('page-item');
            $('#navegacion_usus_bottom a').addClass('page-link');

            listaUsus();
            document.getElementById("cargando").style.display = 'none';
        } 
    });

    
    
});

function generaSelectCurso(){
    if (mes<6) a_final=anno_ini_curso;
    else a_final=anno_ini_curso+1;

    const miSelect = document.getElementById("curso");
    for (var i=2020;i<=a_final;i++){
        const elemento = document.createElement("option");
        elemento.value = i+"-"+(parseInt(i)+1);
        elemento.textContent = elemento.value;
        miSelect.appendChild(elemento);
    }
}


function cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function listaUsus() {
    direccion = new Array();
    direccion["🡅"] = "ASC";
    direccion["🡇"] = "DESC";

    estilo_usu = ["width:80px", "width:220px", "width:220px","width:70px;text-align:center","width:70px;text-align:center","width:70px;text-align:center"];
    encabezamiento_usu = ["NIE", "Alumno", "Nº Registro","Informe","Resolución","Visto"];

    //Construcción del encabezamiento de la tabla
    encab_usus = "<tr>";
    for (i = 0; i < encabezamiento_usu.length; i++) {
        if (encabezamiento_usu[i] == "Alumno") encab_usus += "<td style='" + estilo_usu[i] + "'onclick='ordenUsus()'>" + encabezamiento_usu[i] + " " + orden_direccion_usu + "</td>";
        else encab_usus += "<td style='" + estilo_usu[i] + "'>" + encabezamiento_usu[i] + "</td>";
    }
    ///////////////////////////////////////////////
    datos = {
        buscar: document.getElementById("busqueda_usus").value,
        orden_direccion_usu: direccion[orden_direccion_usu],
        pagina: pagina,
        num_reg_pagina: num_reg_pagina,
        curso:document.getElementById("curso").value,
        departamento:departamanto
    }
    $.post("php/departamento_listausuarios.php", datos, function(resp) {
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "sin_registros") {
            document.getElementById("div_notabla_usus").style.display = "inline-block";
            document.getElementById("div_tabla_usus").style.display = "none";
            numero_paginas=1;
            pagina=1;
            $('#navegacion_usus_top,#navegacion_usus_bottom').bootpag({
                total: numero_paginas
            });
            $('#navegacion_usus_top li').addClass('page-item');
            $('#navegacion_usus_top a').addClass('page-link');
            $('#navegacion_usus_bottom li').addClass('page-item');
            $('#navegacion_usus_bottom a').addClass('page-link');
        } else {
            document.getElementById("div_notabla_usus").style.display = "none";
            document.getElementById("div_tabla_usus").style.display = "inline-block";
            data = "";
            data_array = resp["registros"];
            for (i = 0; i < data_array.length; i++) {
                data += "<tr>";
                data += "<td style='" + estilo_usu[0] + "'>" + data_array[i]["id_nie"] + "</td>";
                data += "<td style='" + estilo_usu[1] + "'>" + data_array[i]["nombre"] + "</td>";
                data += "<td style='" + estilo_usu[2] + "'><a href='docs/"+data_array[i]["id_nie"]+"/exencion_form_emp/"+document.getElementById("curso").value+"/"+data_array[i]["dirRegistro"]+"/"+data_array[i]["registro"]+".pdf' target='_blank'>"+data_array[i]["registro"]+"</a></td>";
                if (data_array[i]["informe_jd"]!=""){
                    data += "<td style='" + estilo_usu[3] + ";text-align:center'><a href='"+data_array[i]["informe_jd"]+"' target='_blank'>Ver</a></td>";
                }
                else{
                    data += "<td style='" + estilo_usu[3] + ";text-align:center'>-</td>";
                }
                if (data_array[i]["resolucion"]!=""){
                    data += "<td style='" + estilo_usu[4] + ";text-align:center'><a href='"+data_array[i]["resolucion"]+"' target='_blank'>Ver</a></td>";
                }
                else{
                    data += "<td style='" + estilo_usu[4] + ";text-align:center'>-</td>";
                }
                if (data_array[i]["visto"]==1){
                    data += "<td style='" + estilo_usu[5] + ";text-align:center'>Sí</td>";
                }
                else{
                    data += "<td style='" + estilo_usu[5] + ";text-align:center'>No</td>";
                }
                data += "</tr>";
            }
            document.getElementById("encabezado_usus").innerHTML = encab_usus;
            document.getElementById("registros_usus").innerHTML = data;
            num_registros = resp.num_registros;
            numero_paginas = Math.ceil(num_registros / num_reg_pagina);
            if (pagina > numero_paginas) pagina = numero_paginas;
            
            $('#navegacion_usus_top,#navegacion_usus_bottom').bootpag({
                total: numero_paginas
            });
            $('#navegacion_usus_top li').addClass('page-item');
            $('#navegacion_usus_top a').addClass('page-link');
            $('#navegacion_usus_bottom li').addClass('page-item');
            $('#navegacion_usus_bottom a').addClass('page-link');
        }
    }, "json");
}



function ordenUsus() {
    if (orden_direccion_usu == "🡅") orden_direccion_usu = "🡇";
    else orden_direccion_usu = "🡅";
    listaUsus();
}


function cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

